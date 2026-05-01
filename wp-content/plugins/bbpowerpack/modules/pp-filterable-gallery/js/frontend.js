(function($) {
	PPFilterableGallery = function(settings) {
		this.settings       = settings;
		this.nodeClass      = '.fl-node-' + settings.id;
		this.wrapperClass   = this.nodeClass + ' .pp-photo-gallery';
		this.itemClass      = this.wrapperClass + ' .pp-gallery-item';
		this.masonry		= this.settings.layout === 'masonry' ? true : false;
		this.paginationEl   = $(this.nodeClass).find('.pp-filterable-gallery-pagination');

		if ( this._hasItems() ) {
			this._initLayout();
		}
	};

	PPFilterableGallery.prototype = {
		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		itemClass       : '',
		filterData		: {},
		postClass       : '',
		gallery         : null,
		matchHeight		: false,
		masonry			: false,

		_hasItems: function() {
			return $(this.itemClass).length > 0;
		},

		_initLayout: function() {
			this._initFilterData();
			this._gridLayout();
			this._initPagination();

			this._hashChange();

			$( window ).on( 'hashchange', this._hashChange.bind( this ) );
		},

		_hashChange: function() {
			setTimeout(function() {
				var hash = location.hash.split('/')[0].replace('!', '');
				if( hash && $(hash).length > 0 ) {
					if ( $(hash).parent().hasClass('pp-gallery-filters') ) {
						$(hash).trigger('click');
					}
				}
			}, 200);
		},

		_initFilterData: function() {
			var filterData = {
				itemSelector: '.pp-gallery-item',
				percentPosition: true,
				transitionDuration: '0.6s',
				isOriginLeft: ! $('body').hasClass( 'rtl' )
			};

			if ( ! this.masonry ) {
				filterData = $.extend( {}, filterData, {
					layoutMode: 'fitRows',
					fitRows: {
						gutter: '.pp-photo-space'
					  },
				} );
			} else {
				filterData = $.extend( {}, filterData, {
					masonry: {
						columnWidth: '.pp-gallery-item',
						gutter: '.pp-photo-space'
					},
				} );
			}

			this.filterData = filterData;
		},

		_gridLayout: function() {
			var node 			= $(this.nodeClass);
			var wrap 			= $(this.wrapperClass);
			var items 			= $(this.itemClass);
			var filterData 		= this.filterData;
			var filters 		= wrap.isotope(filterData);
			var filtersWrap 	= node.find('.pp-gallery-filters');
			var filterToggle 	= node.find('.pp-gallery-filters-toggle');
			var isMasonry		= this.masonry;
			
			wrap.imagesLoaded( function() {

				if ( wrap.find( '.pp-gallery-overlay' ).length > 0 ) {
					var imgW = wrap.find( '.pp-gallery-img' ).outerWidth();
					wrap.find( '.pp-gallery-overlay' ).css('max-width', imgW + 'px');
				}

			} );

			filterToggle.off('click').on('click', function () {
				filtersWrap.toggleClass('pp-gallery-filters-open');
			});

			filtersWrap.on('click', '.pp-gallery-filter-label', function() {
				var filterVal = $(this).attr('data-filter');
				filters.isotope({ filter: filterVal });

				filtersWrap.find('.pp-gallery-filter-label').removeClass('pp-filter-active');
				$(this).addClass('pp-filter-active');
				
				filterToggle.find('span.toggle-text').html($(this).text());
				if (filtersWrap.hasClass('pp-gallery-filters-open')) {
					filtersWrap.removeClass('pp-gallery-filters-open');
				}

				$(document).trigger( 'pp_filterable_gallery_filter_change', [$(this), node] );
				$(window).trigger( 'resize' );
			});

			wrap.imagesLoaded( function() {
				node.find('.pp-filter-active').trigger('click');
				if ( isMasonry ) {
					wrap.isotope('layout');
				}

				items.css('visibility', 'visible');
			} );
		},

		_initPagination: function() {
			if ( ! this.paginationEl.length ) {
				return;
			}

			var self     = this;
			var $items   = $( $(this.nodeClass).find( 'script.pp-filterable-gallery-items' ).html() );
			var perPage  = this.paginationEl.data( 'per-page' );
			var offset   = 0;
			var rendered = perPage;

			this.paginationEl.find('a').on( 'click', function(e) {
 				e.preventDefault();

				for( var i = offset; i < rendered; i++ ) {
					$(self.wrapperClass).isotope( 'insert', $items[ i ] );
					offset += 1;
				}

				rendered += offset;

				if ( $items.length < offset ) {
					offset = $items.length;
				}

				if ( $items.length == offset ) {
					self.paginationEl.remove();
				}
			} );
		}
	};

})(jQuery);
