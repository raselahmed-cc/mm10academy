;(function($) {

	PPPricingTable = function( settings ) {
		this.id 			= settings.id;
		this.nodeClass 		= '.fl-node-' + this.id;
		this.wrapperClass 	= this.nodeClass + ' .pp-pricing-table';
		this.settings 		= settings;

		this._init();
	};

	PPPricingTable.prototype = {
		_init: function()
		{
			if ( this.settings.dualPricing ) {
				this._initDualPricing();
			}

			/* Tooltips */
			$(this.nodeClass + ' .pp-pricing-item-tooltip-icon').on( 'click', this._showHelpTooltip.bind( this ) );
			$('body').on('click', this._hideHelpTooltip);
		},

		_initDualPricing: function()
		{
			var self = this;

			$(this.wrapperClass).find('.pp-pricing-table-button').on('click', function(e) {
				e.preventDefault();

				var activePrice = $(this).data('activate-price');

				$(this).parent('.pp-pricing-table-buttons').find('.pp-pricing-table-button').removeClass('pp-pricing-button-active');
				$(this).addClass( 'pp-pricing-button-active' );

				if ( 'primary' === activePrice ) {
					$(self.wrapperClass).find('.pp-pricing-table-price.pp-price-primary').show();
					$(self.wrapperClass).find('.pp-pricing-table-price.pp-price-secondary').hide();
				}
				if ( 'secondary' === activePrice ) {
					$(self.wrapperClass).find('.pp-pricing-table-price.pp-price-primary').hide();
					$(self.wrapperClass).find('.pp-pricing-table-price.pp-price-secondary').show();
				}

				$(self.wrapperClass).find('.pp-pricing-package-button').each(function() {
					var link = $(this).data( activePrice + '-link' );
					$(this).attr('href', link);
				});
			});

			$(window).on('hashchange', function() {
				this._hasChange();
			}.bind( this ) );
		},

		_hasChange: function()
		{
			var hash = location.hash;

			if ( '#pricing-1' === hash || '#pricing-2' === hash ) {
				var id = hash.split('-')[1];
				if ( $(this.wrapperClass).find('.pp-pricing-button-' + id).length > 0 ) {
					$(this.wrapperClass).find('.pp-pricing-button-' + id).trigger('click');
				}
			}
		},

		_showHelpTooltip: function(e) {
			this._hideHelpTooltip();
			$(e.target).closest('.pp-pricing-item-tooltip').find('.pp-pricing-item-tooltip-text').fadeIn(200);
			e.stopPropagation();
		},

		_hideHelpTooltip: function() {
			$('.fl-module-pp-pricing-table .pp-pricing-item-tooltip-text').fadeOut(200);
		},
	};

})(jQuery);