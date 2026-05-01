(function($) {

	PPContentGrid = function(settings) {
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-content-post-' + this.settings.layout;
		this.postClass      = this.wrapperClass + ' .pp-content-' + this.settings.layout + '-post';
		this.matchHeight	= settings.matchHeight == 'yes' ? true : false;
		this.style			= settings.style;
		this.masonry		= settings.masonry == 'yes' ? true : false;
		this.filters 		= settings.filters;
		this.filterTax 		= settings.filterTax;
		this.filterType 	= settings.filterType;
		this.breakpoints    = settings.breakpoints;
		this.page           = $( this.wrapperClass ).data('paged');
		this.isFiltering	= false;
		this.isDefaultFilter = false;
		this.activeFilter 	= '';
		this.totalPages 	= 1;
		this.currentPage 	= 1;
		this.cacheData		= {};
		this.ajaxData 		= {};
		this.includeSettings = true;

		if ( 'undefined' === typeof this.breakpoints && FLBuilderLayoutConfig ) {
			this.breakpoints = FLBuilderLayoutConfig.breakpoints;
		}

		if ( this._hasPosts() ) {
			this._initLayout();
			this._initSearch();
		}
	};

	PPContentGrid.prototype = {
		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		postClass       : '',
		filters			: false,
		filterTax		: '',
		filterType		: '',
		filterData		: {},
		isFiltering		: false,
		isDefaultFilter	: false,
		activeFilter	: '',
		totalPages		: 1,
		currentPage		: 1,
		cacheData		: {},
		ajaxData 		: {},
		includeSettings	: true,
		matchHeight		: false,
		masonry			: false,
		style			: '',

		_hasPosts: function() {
			return $(this.postClass).length > 0;
		},

		_useCssGrid: function() {
			return this.matchHeight && 'dynamic' === this.filterType;
		},

		_initIsotopeData: function() {
			var postFilterData = {
				itemSelector: '.pp-content-post',
				percentPosition: true,
				transitionDuration: '0.3s',
				hiddenStyle: {
					opacity: 0
				},
				visibleStyle: {
					opacity: 1
				},
				masonry: {
					columnWidth: '.pp-content-post',
					gutter: '.pp-grid-space'
				},
				isOriginLeft: ! $('body').hasClass( 'rtl' ),
			};

			if ( ! this.masonry ) {
				postFilterData = $.extend( {}, postFilterData, {
					layoutMode: 'fitRows',
					fitRows: {
						gutter: '.pp-grid-space'
				  	},
				} );
			}

			// set filter data globally to use later for ajax scroll pagination.
			this.filterData = postFilterData;

			// set default filter as active filter.
			if ( this.settings.defaultFilter !== '' ) {
				this.activeFilter = this.settings.defaultFilter;
			}
		},

		_initSearch: function() {
			var searchFormClass = 'cg-search-form';
			
			$(this.nodeClass).trigger( 'grid.searchInit', [ searchFormClass, this ] );

			if ( ! $( this.nodeClass ).hasClass( 'cg-search-result' ) && $( '.' + searchFormClass ).length > 0 ) {
				return;
			}

			$( '.' + searchFormClass ).find( 'form' ).on( 'submit', function(e) {
				e.preventDefault();
				
				var input = $(e.target).find('input[type="search"]');
		
				bb_powerpack.search_term = input.val();
				if ( input.val() !== '' ) {
					this.settings.is_search = true;
					this.isFiltering = true;
				} else {
					this.settings.is_search = false;
					this.isFiltering = false;
					this.cacheData = {};
					this.ajaxData = {};
				}
				this._getAjaxPosts( this.activeFilter, this.filterData, this.currentPage );
			}.bind( this ) );
		
			$('.' + searchFormClass).find( 'form input' ).on( 'input clear', function(e) {
				if ( $(e.target).val() === '' ) {
					bb_powerpack.search_term = '';
					this.settings.is_search = false;
					this.isFiltering = false;
					this.cacheData = {};
					this.ajaxData = {};
					this._getAjaxPosts( this.activeFilter, this.filterData, this.currentPage );
				}
			}.bind( this ) );
		},

		_initLayout: function() {
			if ( $(this.nodeClass).find('.pp-posts-wrapper').hasClass('pp-posts-initiated') ) {
				return;
			}

			switch(this.settings.layout) {

				case 'grid':
					this._initIsotopeData();
					this._gridLayout();
					this._initPagination();
					this._reLayout();
					break;

				case 'carousel':
					this._carouselLayout();
					break;
			}

			$(this.postClass).css('visibility', 'visible');

			var self = this;

			$(window).on('load', function() {
				if ( ! $( self.nodeClass ).hasClass( 'pp-cg-no-page-scroll' ) ) {
					FLBuilderLayout._scrollToElement( $( self.nodeClass + ' .pp-paged-scroll-to' ) );
				}
			});

			$(this.nodeClass).on( 'grid.afterInit carousel.afterInit', function() {
				$(self.nodeClass).find('.pp-posts-wrapper').addClass('pp-posts-initiated');

				if ( 'carousel' === self.settings.layout ) {
					setTimeout(function() {
						self._gridLayoutMatchHeightSimple();
					}, 200);
				}
				if ( $(self.nodeClass).find( '.owl-nav' ).length > 0 && self.settings.carousel.autoplay ) {
					var carousel = $(self.nodeClass).find( '.owl-carousel' );
					$(self.nodeClass).find( '.owl-nav button' ).on( 'mouseover', function() {
						carousel.trigger( 'stop.owl.autoplay' );
					} ).on( 'mouseleave', function() {
						carousel.trigger( 'play.owl.autoplay' );
					} );
				}
			} );

			// Fix native lazy load issue.
			$(this.nodeClass).find('.pp-posts-wrapper img').on('load', function() {
				if ( 'lazy' !== $(this).attr('loading') ) {
					return;
				}
				var postsWrapper = $(self.nodeClass).find('.pp-content-post-grid');
				if ( 'undefined' !== typeof $.fn.isotope ) {
					setTimeout(function() {
						if ( postsWrapper.data( 'isotope' ) ) {
							postsWrapper.isotope('layout');
						}
					}, 500);
				}
			});
		},

		_gridLayout: function()
		{
			var wrap = $(this.wrapperClass);
			var postFilterData = this.filterData;

			this._loadItemsFromCache();

			this._setLastPostClass();
			this._setCenterAlign();

			$( window ).on( 'resize', this._setCenterAlign.bind( this ) );

			wrap.imagesLoaded( function() {

				var node = $(this.nodeClass);
				var base = this;
				var postFilters = ! this._useCssGrid() && ! wrap.data( 'isotope' ) ? wrap.isotope( postFilterData ) : wrap;

                if ( this.settings.filters || this.masonry ) {

					var filterWrap = $(this.nodeClass).find('.pp-post-filters');
					var filterToggle = $(this.nodeClass).find('.pp-post-filters-toggle');

					filterToggle.on('click', function () {
						filterWrap.slideToggle(function () {
							if ($(this).is(':visible')) {
								filterToggle.addClass('pp-post-filters-open');
							}
							if (!$(this).is(':visible')) {
								filterToggle.removeClass('pp-post-filters-open');
							}
						});
					});

					filterWrap.on('click keyup', '.pp-post-filter', function(e) {
						var shouldFilter = 'click' === e.type || ( 'keyup' === e.type && ( 13 === e.keyCode || 13 === e.which ) )
						if ( ! shouldFilter ) {
							return;
						}
						// set active filter globally to use later for ajax scroll pagination.
						base.activeFilter = $(this).data('term');
						base.isFiltering = true;

						if ('static' === base.filterType) {
							var filterVal = $(this).attr('data-filter');
							postFilters.isotope({ filter: filterVal });
						} else {
							var term = $(this).data('term');
							$(base.wrapperClass).addClass('pp-is-filtering');
							base._getPosts(term, postFilterData);
						}

						filterWrap.find('.pp-post-filter').removeClass('pp-filter-active');
						$(this).addClass('pp-filter-active');

						filterToggle.find('span.toggle-text').html($(this).text());
						if (filterToggle.hasClass('pp-post-filters-open')) {
							filterWrap.slideUp();
							filterToggle.removeClass('pp-post-filters-open');
						}

						$(base.nodeClass).trigger('grid.filter.change');
					});

					$( window ).on( 'resize', function() {
						if ( ! filterToggle.is( ':visible' ) ) {
							filterWrap.removeAttr( 'style' );
						}
					} );

					// Default filter.
					if ( base.settings.defaultFilter !== '' ) {
						var defaultFilter = base.settings.defaultFilter;
						if ( filterWrap.find('li[data-term="' + defaultFilter + '"]').length > 0 ) {
							self.isDefaultFilter = true;
							filterWrap.find('li[data-term="' + defaultFilter + '"]').trigger('click');
						}
					}

					// Trigger filter by hash parameter in URL.
					if ( '' !== location.hash ) {
						var filterHash = location.hash.split('#')[1];

						filterWrap.find('li[data-term="' + filterHash + '"]').trigger('click');
					}

					// Trigger filter on hash change in URL.
					$(window).on('hashchange', function() {
						if ( '' !== location.hash ) {
							var filterHash = location.hash.split('#')[1];
	
							filterWrap.find('li[data-term="' + filterHash + '"]').trigger('click');
						}
					});
                }

				if ('dynamic' === base.filterType) {
					$(base.nodeClass).find('.fl-builder-pagination a').off('click').on('click', function (e) {
						e.preventDefault();
						var pageNumber = base._getPageNumber( this );
						base.currentPage = pageNumber;
						base._getPosts('', postFilterData, pageNumber);
					});
				}

                if( ! this.masonry ) {
                    setTimeout( function() {
						if ( base.settings.filters && 'static' === base.filterType ) {
							node.find('.pp-filter-active').trigger('click');
						}
						base._gridLayoutMatchHeight();
						if ( wrap.data( 'isotope' ) ) {
							wrap.isotope('layout');
						}
                    }, 1000 );
                }

				$(this.nodeClass).trigger('grid.afterInit');

			}.bind( this ) );
		},

		_loadItemsFromCache: function() {
			if ( window.localStorage && $(this.nodeClass).hasClass( 'cg-cache-active' ) ) {
				var cachedResponse = window.localStorage.getItem( 'ppcg_' + this.settings.id );
				if ( cachedResponse ) {
					cachedResponse = JSON.parse( cachedResponse );
					cachedTime = cachedResponse.timestamp;
					timeDiff = ( new Date().getTime() - cachedTime ) / 1000;
					timeDiff /= (60*60);
					if ( timeDiff >= 12 || location.href !== cachedResponse.currentUrl ) { // TODO
						window.localStorage.removeItem( 'ppcg_' + this.settings.id );
					} else {
						var pages = cachedResponse.cacheData.ajaxCache[cachedResponse.filter].page;
						Object.keys( pages ).forEach(function(page) {
							this._renderPosts(pages[page], {
								term: cachedResponse.filter,
								isotopeData: this.filterData,
								page: cachedResponse.paged
							});
						}.bind(this));

						this.currentPage  = cachedResponse.currentPage;
						this.activeFilter = cachedResponse.activeFilter;
						this.cacheData    = cachedResponse.cacheData;

						$(this.nodeClass).find('.pp-post-filter').removeClass('pp-filter-active');

						if ( '' === this.activeFilter || 'all' === cachedResponse.filter ) {
							$(this.nodeClass).find('.pp-post-filter[data-filter="*"]').addClass('pp-filter-active');
						} else {
							$(this.nodeClass).find('.pp-post-filter[data-term="' + this.activeFilter + '"]').addClass('pp-filter-active');
						}
					}
				}
			}
		},
		
		_removeItemsFromCache: function() {
			if ( window.localStorage ) {
				window.localStorage.removeItem( 'ppcg_' + this.settings.id );
			}
		},

		_setLastPostClass: function() {
			var posts = $( this.postClass );

			if ( posts.length > 0 ) {
				var last = posts[ posts.length - 1 ];

				$( last ).addClass( 'pp-post-last' );
			}
		},

		_setCenterAlign: function() {
			if ( $( this.nodeClass ).hasClass( 'cg-center-align' ) ) {
				var columns = this._getColumns();
				var gridSpace = $( this.wrapperClass ).find( '.pp-grid-space' ).outerWidth();
				if ( $( this.postClass ).length >= columns ) {
					gridSpace = gridSpace * ( columns - 1 );
				}
				if ( 1 === columns ) {
					gridSpace = 0;
				}
				var colWidth = ( $( this.nodeClass + ' .fl-module-content' ).outerWidth() / columns ) - gridSpace;

				if ( colWidth > 0 ) {
					this.filterData.masonry.columnWidth = colWidth;
					this.filterData.masonry.isFitRows = true;
					this.filterData.percentPosition = false;

					gridSpace += 0.18;

					if ( $( this.postClass ).length < columns ) {
						var wrapperWidth = ( colWidth * $( this.postClass ).length ) + ( gridSpace * ( $( this.postClass ).length - 1 ) );
					} else {
						var wrapperWidth = ( colWidth * columns ) + gridSpace;
					}

					$( this.postClass ).css( 'width', colWidth + 'px' );
					$( this.wrapperClass ).css( 'width', wrapperWidth + 'px' );
				}
			}
		},

		_carouselLayout: function()
		{
			var self = this;
			var wrap = $(this.nodeClass + ' .pp-content-post-carousel .pp-content-posts-inner');

			var setAriaAttrs = function(e) {
				var visibleSlides = 0; // Counter for real visible slides
				$(e.target).find('.owl-item').each(function(index) {
					// Check if the item is not a clone
					if ( ! $(this).hasClass('cloned') ) {
						visibleSlides++;
						$(this).attr('aria-label', 'Item ' + visibleSlides + ' of ' + e.item.count);
					}
				});

				var currentIndex = e.item.index; // Get the index of the first visible slide
				var visibleItems = e.page.size;  // Get the number of visible slides

				// Loop through all slides
				$(e.target).find('.owl-item').each(function(index) {
					// Set aria-hidden="false" for visible slides, aria-hidden="true" for non-visible slides
					if (index >= currentIndex && index < currentIndex + visibleItems) {
						$(this).attr('aria-hidden', 'false');
						$(this).attr('tabindex', '0');
						$(this).find('a, button').attr('tabindex', '0');
					} else {
						$(this).attr('aria-hidden', 'true');
						$(this).attr('tabindex', '-1');
						$(this).find('a, button').attr('tabindex', '-1');
					}
				});
			};

			var owlOptions = {
				onInitialized: function(e) {
					var count = 1;
					$(e.target).find('.owl-dot').each(function() {
						$(this).append( '<span class="sr-only">Slide group ' + count + '</span>' );
						count++;
						if ( $(this).hasClass( 'active' ) ) {
							$(this).attr('aria-current', 'true');
						} else {
							$(this).attr('aria-current', 'false');
						}
					});
					
					$(e.target).find( '.owl-item' ).attr('role','group');

					setAriaAttrs(e);

					self._gridLayoutMatchHeightSimple();
				},
				onChanged: function(e) {
					$(e.target).find('.owl-dot').each(function() {
						if ( $(this).hasClass( 'active' ) ) {
							$(this).attr('aria-current', 'true');
						} else {
							$(this).attr('aria-current', 'false');
						}
					});
			
					//var currentItem = e.item.index + 1;
					//$(e.target).find('.owl-item').eq(currentItem).focus();

					setAriaAttrs(e);
				},
				onDragged: function(e) {
					if ( ! self.settings.carousel.autoplay ) {
						$(e.target).data('owl.carousel').refresh();
					}
				},
				onResized: this._gridLayoutMatchHeightSimple.bind( this ),
				onRefreshed: this._gridLayoutMatchHeightSimple.bind( this ),
				onLoadedLazy: this._gridLayoutMatchHeightSimple.bind( this ),
				rtl: $('body').hasClass( 'rtl' )
			};
			if ( $(this.postClass).length < this.settings.carousel.items ) {
				this.settings.carousel.slideBy = 'page';
				this.settings.carousel.loop = false;
			}
			/*
			if ( this.settings.carousel.lazyLoad ) {
				$( this.postClass ).each(function() {
					if ( $( this ).find( '.pp-content-grid-post-image img' ).length > 0 ) {
						var src = $( this ).find( '.pp-content-grid-post-image img' ).addClass( 'owl-lazy' ).attr( 'src' );
						$( this ).find( '.pp-content-grid-post-image img' ).removeAttr( 'src' ).attr( 'data-src', src );
					} else {
						var src = $( this ).find( '.pp-post-image img' ).addClass( 'owl-lazy' ).attr( 'src' );
						$( this ).find( '.pp-post-image img' ).removeAttr( 'src' ).attr( 'data-src', src );
					}
				});
			}
			*/

			owlOptions = $.extend({}, this.settings.carousel, owlOptions);
			$(this.nodeClass).trigger( 'carousel.beforeInit', [ owlOptions ] );

			wrap.imagesLoaded( function() {
				wrap.owlCarousel( owlOptions );

				var owl = wrap.data('owl.carousel');

				wrap.on( 'keyup', function(e) {
					if ( 37 === e.keyCode || 37 === e.which ) {
						owl.prev();
					}
					if ( 39 === e.keyCode || 39 === e.which ) {
						owl.next();
					}
				} );

				if ( $(this.nodeClass).hasClass( 'overflow-visible' ) ) {
					$( this.nodeClass ).closest('.fl-row').css( 'overflow-x', 'hidden' );
					$( this.nodeClass ).find('.owl-carousel').css( 'overflow', 'visible' );
				}

				$(this.nodeClass).trigger('carousel.afterInit');
			}.bind( this ) );
		},

		_getPosts: function (term, isotopeData, paged) {
			var processAjax = false,
				filter 		= term,
				paged 		= (!paged || 'undefined' === typeof paged) ? 1 : paged;

			if ('undefined' === typeof term || '' === term) {
				filter = 'all';
			}

			this._getTotalPages();

			var cacheData = this._getCacheData(filter);

			if ('undefined' === typeof cacheData) {
				processAjax = true;
			} else {
				var cachedResponse = cacheData.page[paged];
				if ('undefined' === typeof cachedResponse) {
					processAjax = true;
				} else {
					$(this.nodeClass).trigger('grid.beforeRender');
					this._renderPosts(cachedResponse, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
				}
			}

			if (processAjax) {
				this._getAjaxPosts(term, isotopeData, paged);
			}
		},

		_getAjaxPosts: function (term, isotopeData, paged) {
			var taxonomy = this.filterTax,
				paged = 'undefined' === typeof paged ? false : paged,
				self = this;

			var currentPage = bb_powerpack.current_page.split('?')[0];

			var data = {
				pp_action: 'get_ajax_posts',
				post_id: $( this.nodeClass ).closest( '.fl-builder-content' ).data( 'post-id' ),
				post_type: bb_powerpack.conditionals.current_post_type,
				node_id: this.settings.id,
				paged: ! paged ? this.page : paged,
				current_page: currentPage,
				loop_count: $(this.wrapperClass).data('loop-count') ? $(this.wrapperClass).data('loop-count') : 1,
			};

			if ( 'undefined' !== typeof this.includeSettings && this.includeSettings ) {
				data.settings = this.settings.fields;
			}

			if ( $(this.wrapperClass).data('template-id') ) {
				data.template_id = $(this.wrapperClass).data('template-id');
				data.template_node_id = $(this.wrapperClass).data('template-node-id');
			}

			// Archive.
			if ( this.settings.is_archive ) {
				data['is_archive'] = true;
			}

			// Term.
			if ('undefined' !== typeof term && '' !== term) {
				data['term'] = term;
			} else if ( this.settings.is_tax && this.settings.current_term ) {
				data['is_tax'] = true;
				data['taxonomy'] = this.settings.current_tax;
				data['term'] = this.settings.current_term;
			}
			// Term - provide current term data additionally.
			if ( this.settings.is_tax && this.settings.current_term ) {
				data['current_tax'] = this.settings.current_tax;
				data['current_term'] = this.settings.current_term;
			}

			// Author.
			if ( this.settings.is_author && this.settings.current_author ) {
				data['is_author'] = true;
				data['author_id'] = this.settings.current_author;
			}

			if ( this.settings.is_search ) {
				data['is_search'] = true;
				data['search_term'] = bb_powerpack.search_term;
				data['search_post_type'] = bb_powerpack.conditionals.current_post_type;
			}

			if ( $(this.wrapperClass).data('orderby') ) {
				data['orderby'] = $(this.wrapperClass).data('orderby');
			}

			this.ajaxData = data;

			if ( ! this.paginating ) {
				$(this.wrapperClass).addClass('pp-is-filtering');
			}

			$.post(
				location.href.split('#').shift(),
				data,
				function (response) {
					// if ( 'undefined' !== typeof response.success && ! response.success ) {
					// 	self.includeSettings = true;
					// 	self._getAjaxPosts( term, isotopeData, paged );
					// 	return;
					// }
					self.includeSettings = true;
					self._setCacheData(term, response, paged);
					$(self.nodeClass).trigger('grid.beforeRender');
					self._renderPosts(response, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
					$(self.wrapperClass).removeClass('pp-is-filtering');
					if ( ! self.paginating ) {
						self.paginating = false;
					}
				}
			).fail( function( xhr ) {
				if ( 404 == xhr.status && 'undefined' !== xhr.responseJSON.data ) {
					var response = xhr.responseJSON;
					self.includeSettings = true;
					self._setCacheData(term, response, paged);
					$(self.nodeClass).trigger('grid.beforeRender');
					self._renderPosts(response, {
						term: term,
						isotopeData: isotopeData,
						page: paged
					});
					$(self.wrapperClass).removeClass('pp-is-filtering');
					if ( ! self.paginating ) {
						self.paginating = false;
					}
				}
			} );
		},

		_renderPosts: function (response, args) {
			var self = this,
				wrap = $(this.wrapperClass),
				posts = $(response.data),
				count = 0;

			posts.each(function() {
				if ( $(this).hasClass('pp-content-post') ) {
					count++;
				}
			});

			wrap.parent().css( '--items-count', count );

			if ( ( 'load_more' !== self.settings.pagination && 'scroll' !== self.settings.pagination ) || self.isFiltering ) {
				if ( wrap.data( 'isotope' ) ) {
					wrap.isotope('remove', $(this.postClass));
				} else {
					wrap.find( this.postClass ).remove();
				}
			}

			if ( wrap.data( 'isotope' ) ) {
				wrap.isotope( 'remove', $( '.pp-posts-not-found-text' ) );
			} else {
				wrap.find( '.pp-posts-not-found-text' ).remove();
			}

			if ( wrap.data( 'isotope' ) ) {
				if (!this.masonry) {
					wrap.isotope('insert', posts, this._gridLayoutMatchHeight.bind( this ) );
					wrap.imagesLoaded( function () {
						setTimeout(function () {
							self._gridLayoutMatchHeight();
						}, 150);
					}.bind( this ) );
				} else {
					wrap.isotope('insert', posts);
				}

				wrap.find('.pp-grid-space').remove();
				wrap.append('<div class="pp-grid-space"></div>');
			} else {
				posts.hide();
				if ( ( 'load_more' == self.settings.pagination || 'scroll' == self.settings.pagination ) && ! self.isFiltering ) {
					wrap.append( posts.fadeIn(400) );
				} else {
					wrap.html( posts.fadeIn(400) );
				}
			}

			wrap.imagesLoaded( function () {
				setTimeout(function () {
					if (!this.masonry) {
						self._gridLayoutMatchHeight();
					}
					if ( wrap.data( 'isotope' ) ) {
						wrap.isotope('layout');
					}
				}, 500);
			}.bind( this ) );

			if ( 'load_more' === self.settings.pagination ) {
				$(self.nodeClass).find('.pp-content-grid-load-more').remove();
			}
			if ( 'scroll' === self.settings.pagination ) {
				$(self.nodeClass).find('.pp-content-grid-loader').remove();
			}

			if (response.pagination) {
				var $pagination = $(response.pagination);

				if ( 'numbers' === self.settings.pagination ) {
					$pagination.removeAttr( 'style' );
				}

				$(self.nodeClass).find('.fl-builder-pagination').remove();
				$(self.nodeClass).find( '> .fl-module-content' ).append($pagination);
				if ( 'load_more' === self.settings.pagination ) {
					setTimeout(function() {
						self._getTotalPages();
						if ( self.totalPages !== self.currentPage ) {
							$(self.nodeClass).find('.fl-module-content .pp-content-grid-load-more').fadeIn();
							self._initPagination();
						} else {
							$(self.nodeClass).find('.fl-module-content .pp-content-grid-load-more').slideUp();
						}
					}, 250);
				}
				$(self.nodeClass).off('click', '.pp-ajax-pagination a').on('click', '.pp-ajax-pagination a', function (e) {
					e.preventDefault();
					var pageNumber = self._getPageNumber( this );
					self.currentPage = pageNumber;
					self._getPosts(args.term, args.isotopeData, pageNumber);
				});
			} else {
				$(self.nodeClass).find('.fl-builder-pagination').remove();
			}

			if ( ('load_more' !== self.settings.pagination && 'scroll' !== self.settings.pagination) || self.isFiltering ) {
				if ( self.settings.scrollTo && ! self.isDefaultFilter ) {
					var scrollToClass = self.settings.scrollToClass || self.nodeClass;
					var offsetTop = $(scrollToClass).offset().top - 200;
					$('html, body').stop().animate({
						scrollTop: offsetTop
					}, 300);
					self.isDefaultFilter = false;
				}
			}

			// Append any scripts and styles found in response.
			if ( response.styles_scripts && '' !== response.styles_scripts ) {
				$('body').append( $( response.styles_scripts ) );
			}

			if ( self.isFiltering ) {
				self.isFiltering = false;
				$(self.nodeClass).trigger( 'grid.filter.complete' );
			}
			wrap.removeClass('pp-is-filtering');

			$(self.nodeClass).trigger('grid.rendered');
		},

		_getPageNumber: function( pageElement )
		{
			var pageNumber = parseInt( $(pageElement).text() ); //$(pageElement).attr('href').split('#page-')[1];

			if ( $(pageElement).hasClass('next') ) {
				pageNumber = parseInt( $(pageElement).parents('.pp-content-grid-pagination').find('.current').text() ) + 1;
			}
			if ( $(pageElement).hasClass('prev') ) {
				pageNumber = parseInt( $(pageElement).parents('.pp-content-grid-pagination').find('.current').text() ) - 1;
			}

			return pageNumber;
		},

		_setCacheData: function (filter, response, paged) {
			if ('undefined' === typeof filter || '' === filter) {
				filter = 'all';
			}
			if ('undefined' === typeof paged || !paged) {
				paged = 1;
			}

			if ('undefined' === typeof this.cacheData.ajaxCache) {
				this.cacheData.ajaxCache = {};
			}
			if ('undefined' === typeof this.cacheData.ajaxCache[filter]) {
				this.cacheData.ajaxCache[filter] = {};
			}
			if ('undefined' === typeof this.cacheData.ajaxCache[filter].page) {
				this.cacheData.ajaxCache[filter].page = {};
			}

			this.cacheData.ajaxCache[filter].page[paged] = response;

			if ( window.localStorage ) {
				var localData = {
					paged: paged,
					currentPage: this.currentPage,
					currentUrl: location.href,
					cacheData: this.cacheData,
					filter: filter,
					activeFilter: this.activeFilter,
					timestamp: new Date().getTime()
				};
				window.localStorage.setItem( 'ppcg_' + this.settings.id, JSON.stringify(localData) );
			}
		},

		_getCacheData: function (filter) {
			var cacheData = this.cacheData;

			if ('undefined' === typeof cacheData.ajaxCache) {
				cacheData.ajaxCache = {};
			}

			return cacheData.ajaxCache[filter];
		},

		_getColumns: function() {
			var columns = this.settings.postColumns.xl;

			if (window.innerWidth <= this.settings.breakpoints.large) {
				columns = this.settings.postColumns.lg;
			}
			if (window.innerWidth <= this.settings.breakpoints.medium) {
				columns = this.settings.postColumns.md;
			}
			if (window.innerWidth <= this.settings.breakpoints.small) {
				columns = this.settings.postColumns.sm;
			}

			return columns;
		},

		_gridLayoutMatchHeight: function()
		{
			var highestBox = 0;
			var contentHeight = 0;
			var postElements = $(this.postClass + ':visible');
			var columns = this._getColumns();

			if (! this.matchHeight || 1 === columns) {
				return;
			}

			if ( 'style-9' === this.style ) {
				return;
			}

			if ( this._useCssGrid() ) {
				return;
			}

			if ( this.settings.layout === 'grid' ) {
				if ( this.masonry ) {
					return;
				}

				postElements.css('height', 'auto');

				var rows = Math.round(postElements.length / columns);

				if ( postElements.length % columns > 0 ) {
					rows = rows + 1;
				}

				// range.
				var j = 1,
					k = columns;

				for( var i = 0; i < rows; i++ ) {
					// select number of posts in the current row.
					var postsInRow = $(this.postClass + ':visible:nth-child(n+' + j + '):nth-child(-n+' + k + ')');

					// get height of the larger post element within the current row.
					postsInRow.css('height', '').each(function () {
						if ($(this).height() > highestBox) {
							highestBox = $(this).height();
							contentHeight = $(this).find('.pp-content-post-data').outerHeight();
						}
					});
					// apply the height to all posts in the current row.
					postsInRow.height(highestBox);

					// increment range.
					j = k + 1;
					k = k + columns;
					if ( k > postElements.length ) {
						k = postElements.length;
					}
					highestBox = 0;
				}
			} else {
				// carousel layout.
				postElements.css('height', '').each(function(){

					if($(this).height() > highestBox) {
						highestBox = $(this).height();
						contentHeight = $(this).find('.pp-content-post-data').outerHeight();
					}
				});

				postElements.height(highestBox);
			}
            //$(this.postClass).find('.pp-content-post-data').css('min-height', contentHeight + 'px').addClass('pp-content-relative');
		},

		_gridLayoutMatchHeightSimple: function () {
			if ( ! this.matchHeight ) {
				return;
			}

			if ( $(this.wrapperClass).hasClass( 'pp-css-grid' ) ) {
				return;
			}

			if ( 'style-9' === this.style ) {
				return;
			}

			var highestBox = 0;
			var contentHeight = 0;
			var postElements = $(this.postClass);

			var columns = this._getColumns();

			if ( 1 === columns && this.settings.layout === 'grid' ) {
				return;
			}

			postElements.css('height', '').each(function () {

				if ($(this).height() > highestBox) {
					highestBox = $(this).height();
					contentHeight = $(this).find('.pp-content-post-data').outerHeight();
				}
			});

			postElements.height(highestBox);
		},

		_initPagination: function()
		{
			var self = this;

			setTimeout(function() {
				self._getTotalPages();

				if ( self.settings.pagination === 'load_more' ) {
					self._initLoadMore();
				}
				if ( self.settings.pagination === 'scroll' && typeof FLBuilder === 'undefined' ) {
					self._initScroll();
				}
			}, 500);
		},

		_getTotalPages: function()
		{
			var pages = $( this.nodeClass + ' .pp-content-grid-pagination' ).find( 'li .page-numbers:not(.next)' );

			if ( pages.length > 1) {
				var total = pages.last().text().replace( /\D/g, '' )
				this.totalPages = parseInt( total );
			} else {
				this.totalPages = 1;
			}

			return this.totalPages;
		},

		_initLoadMore: function()
		{
			var self 		= this,
				$button 	= $(this.nodeClass).find('.pp-grid-load-more-button'),
				currentPage = self.currentPage,
				activeFilter = self.activeFilter,
				isAjaxPagination = 'dynamic' === self.filterType;

			if ( ! self.filters || 'dynamic' !== self.filterType ) {
				activeFilter = '';
			}

			$(self.nodeClass).on('grid.filter.change', function() {
				// re-assign active filter.
				if ( self.filters && 'dynamic' === self.filterType ) {
					activeFilter = self.activeFilter
				}
			});

			$button.off('click').on('click', function(e) {
				e.preventDefault();

				$(this).addClass('disabled loading');
				self.isFiltering = false;
				self.paginating = true;

				currentPage = parseInt( currentPage ) + 1;

				self._getPosts(activeFilter, self.filterData, currentPage);
				self.currentPage = currentPage;
			});

			$(self.nodeClass).on('grid.rendered', function() {
				$button.removeClass( 'disabled loading' );

				if ( currentPage >= self.totalPages ) {
					$button.parent().hide();
				}
			});

			// Reset pagination index on filter.
			$(self.nodeClass).on('grid.filter.complete', function() {
				if ( $(self.nodeClass).find( '.pp-content-grid-pagination' ).length > 0 ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
					$button.parent().show();
				} else {
					$button.parent().hide();
				}
			});
		},

		_initScroll: function()
		{
			var	self			= this,
				gridOffset 		= $(this.wrapperClass).offset(),
				gridHeight		= $(this.wrapperClass).height(),
				winHeight		= $(window).height(),
				currentPage 	= this.currentPage,
				activeFilter	= self.activeFilter,
				rendered		= false,
				loaded			= false;
			
			if ( self.scrollPaginationIninitiated ) {
				return;
			}

			if ( ! self.filters || 'dynamic' !== self.filterType ) {
				activeFilter = '';
			}

			$(window).on('scroll', function() {
				if ( loaded ) {
					return;
				}
				var scrollPos = $(window).scrollTop();

				if ( scrollPos >= gridOffset.top - ( winHeight - gridHeight ) ) {
					self.isFiltering = false;
					currentPage = parseInt( currentPage ) + 1;

					$(self.nodeClass).find('.pp-content-grid-loader').show();

					if ( currentPage <= self.totalPages ) {
						loaded = true;
						self.paginating = true;
						self._getPosts(activeFilter, self.filterData, currentPage);
					} else {
						loaded = true;
						$(self.nodeClass).find('.pp-content-grid-loader').hide();
					}

					self.currentPage = currentPage;
				}
			}.bind( this ) );

			$(self.nodeClass).on('grid.filter.change', function() {
				// re-assign active filter.
				if ( self.filters && 'dynamic' === self.filterType ) {
					activeFilter = self.activeFilter
				}
				
				// get container height.
				gridHeight = $(self.wrapperClass).height();
				self._gridLayoutMatchHeightSimple();

				if ( $(self.wrapperClass).data( 'isotope' ) ) {
					$(self.wrapperClass).isotope('layout');
				}

				if ( 'dynamic' === self.filterType ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
					loaded = false;
				}
			});

			$(self.nodeClass).on('grid.rendered', function() {
				// get gridHeight again after render.
				gridHeight = $(self.wrapperClass).height();

				if ( ! rendered ) {
					self._getTotalPages();
				}
				
				$(self.nodeClass).find('.pp-content-grid-loader').hide();

				setTimeout(function() {
					self._gridLayoutMatchHeightSimple();
					if ( $(self.wrapperClass).data( 'isotope' ) ) {
						$(self.wrapperClass).isotope('layout');
					}
				}, 500);

				// set loaded flag.
				if ( currentPage >= self.totalPages ) {
					loaded = true;
				} else {
					loaded = false;
				}

				rendered = true;
			});

			// Reset pagination index on filter.
			$(self.nodeClass).on('grid.filter.complete', function() {
				if ( $(self.nodeClass).find( '.pp-content-grid-pagination' ).length > 0 ) {
					self._getTotalPages();
					self.currentPage = currentPage = 1;
				}
			});

			self.scrollPaginationIninitiated = true;
		},

		_reLayout: function() {
			var self = this;
			var wrap = $(this.wrapperClass);

			var refreshLayout = function() {
				wrap.imagesLoaded(function() {
					if ( $('body').hasClass('fl-builder-active') ) {
						return;
					}
					if ( ! self.masonry ) {
						self._gridLayoutMatchHeight();
					}

					setTimeout(function() {
						if ( wrap.data( 'isotope' ) ) {
							wrap.isotope('destroy');
						}
						//wrap.isotope( self.filterData );
						self.layoutRefreshed = true;
						self._gridLayout();
						self._initPagination();
					}, 500);
				});
			};

			// Search and Filter.
			$(document).on( 'sf:ajaxfinish', '.searchandfilter', refreshLayout );

			// FacetWP.
			$(document).on( 'facetwp-refresh', function() {
				if ( 'undefined' !== typeof FWPBB && 'undefined' !== typeof FWPBB.modules ) {
					FWPBB.modules[ self.settings.id ] = self.settings;
				}
			} );
			$(document).on( 'facetwp-loaded', function() {
				if ( 'undefined' !== typeof FWPBB && 'undefined' !== typeof FWPBB.modules ) {
					FWPBB.modules[ self.settings.id ] = self.settings;
				}

				refreshLayout();
			} );

			// WP Grid Builder.
			$(window).on( 'wpgb.loaded', function() {
				if ( 'undefined' === typeof WP_Grid_Builder ) {
					return;
				}
				if ( 'undefined' === typeof WP_Grid_Builder['on'] ) {
					return;
				}

				WP_Grid_Builder.on( 'init', function( wpgb ) {
					if ( wpgb.facets && 'undefined' !== typeof wpgb.facets['on'] ) {
						wpgb.facets.on( 'appended', refreshLayout );
					}
				} );
			} );
		}
	};

})(jQuery);
