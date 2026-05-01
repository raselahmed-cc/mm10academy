window.onLoadPPGoogleMap = function() {};

(function ($) {
	PPGoogleMap = function (settings) {
		this.id                = settings.id;
		this.nodeClass         = '.fl-node-' + this.id;
		this.mapElement        = $(this.nodeClass).find('.pp-google-map');
		this.mapObjects        = {};
		this.scrollZoom        = settings.scrollZoom;
		this.dragging          = settings.dragging;
		this.streetView        = settings.streetView;
		this.zoomControl       = settings.zoomControl;
		this.fullscreenControl = settings.fullscreenControl;
		this.mapType           = settings.mapType;
		this.mapTypeControl    = settings.mapTypeControl;
		this.markerAnimation   = settings.markerAnimation;
		this.mapSkin           = settings.mapSkin;
		this.mapStyleCode      = ( '' != settings.mapStyleCode ) ? JSON.parse( settings.mapStyleCode ) : '';
		this.isBuilderActive   = settings.isBuilderActive;
		this.markerData        = settings.markerData;
		this.markerName        = settings.markerName;
		this.markerPoint       = settings.markerPoint;
		this.markerImage       = settings.markerImage;
		this.markerImageWidth   = settings.markerImageWidth;
		this.markerImageHeight  = settings.markerImageHeight;
		this.markerLinks       = settings.markerLinks;
		this.infoWindowText    = settings.infoWindowText;
		this.enableInfo        = settings.enableInfo;
		this.zoomType          = settings.zoomType;
		this.maxZoom          = settings.maxZoom;
		this.mapZoom           = settings.mapZoom;
		this.hideTooltip       = settings.hideTooltip;
		this.settings			= settings;

		if ( 'undefined' === typeof google && $('#pp-google-map-js').length === 0 ) {
			this._initApi();
		} else {
			setTimeout( function() {
				this._init();
			}.bind( this ), 1000 );
		}
	}

	PPGoogleMap.prototype = {
		_initApi: function() {
			var d = document, s = 'script', id = 'pp-google-map';
			var js, fjs = d.getElementsByTagName(s)[0];
			
			if (d.getElementById(id)) return;
			
			js = d.createElement(s); js.id = id;
			js.async = true;
			js.src = this.settings.apiUrl;
			fjs.parentNode.insertBefore(js, fjs);

			setTimeout( function() {
				this._init();
			}.bind( this ), 1000 );
		},

		_init: function () {
			if ( typeof this.markerData[0] === 'undefined' ) {
				return false;
			}

			var lat = this.markerData[0]['latitude'],
				long = this.markerData[0]['longitude'];

			this.latlng = new google.maps.LatLng( lat, long );

			this.mapOptions = {
				zoom:              this.mapZoom,
				center:            this.latlng,
				mapTypeId:         this.mapType,
				mapTypeControl:    this.mapTypeControl,
				streetViewControl: this.streetView,
				zoomControl:       this.zoomControl,
				fullscreenControl: this.fullscreenControl,
				gestureHandling:   this.scrollZoom,
				styles:            this.mapStyleCode,
				draggable:         ( $( document).width() > 641 ) ? true : this.dragging,
				gestureHandling:   this.scrollZoom,
			}

			if ( this.maxZoom && ! isNaN( parseInt( this.maxZoom ) ) ) {
				this.mapOptions.maxZoom = this.maxZoom;
			}

			if ( 'drop' == this.markerAnimation ) {
				this.markerAnimation = google.maps.Animation.DROP;
			} else if ( 'bounce' == this.markerAnimation ) {
				this.markerAnimation = google.maps.Animation.BOUNCE;
			} else {
				this.markerAnimation = '';
			}

			if ( typeof this.mapElement[0] === 'undefined' ) {
				return false;
			}

			this.mapObjects = {
				map: new google.maps.Map( this.mapElement[0], this.mapOptions ),
				markers: [],
				infoWindows: []
			};

			google.maps.event.addListenerOnce(this.mapObjects.map, 'idle', function() {
				document.getElementsByTagName('iframe')[0].title = 'Map';
			});

			this._initMarkers();
		},

		_initMarkers: function() {
			var bounds      = new google.maps.LatLngBounds();

			for (i = 0; i < this.markerData.length; i++) {

				var icon 		= '',
					lat 		= this.markerData[i]['latitude'],
					lng 		= this.markerData[i]['longitude'],
					title 		= this.markerName[i],
					icon_type 	= this.markerPoint[i],
					icon_url 	= this.markerImage[i],
					icon_width 	= this.markerImageWidth[i],
					icon_height = this.markerImageHeight[i],
					link        = 'undefined' !== typeof this.markerLinks ? this.markerLinks[i] : [''];

				if ( lat != '' && lng != '' ) {

					if ( 'custom' === icon_type ) {

						icon = {
							url: icon_url
						};

						if ( '' !== icon_width && '' !== icon_height ) {
							icon['scaledSize'] = new google.maps.Size(icon_width, icon_height);
							icon['origin'] = new google.maps.Point(0, 0); // origin
    						//icon['anchor'] = new google.maps.Point(0, 0); // anchor
						}
					}
					if ( 'auto' === this.zoomType ) {	
						var loc = new google.maps.LatLng(lat, lng);
						bounds.extend(loc);
						this.mapObjects.map.fitBounds(bounds);
					}

					var marker = new google.maps.Marker({
						position:	new google.maps.LatLng(lat, lng),
						map: 		this.mapObjects.map,
						title: 		title,
						icon: 		icon,
						animation: 	this.markerAnimation,
						url:        link
					});

					this.mapObjects.markers.push( marker );

					if ( 'yes' != this.enableInfo[i] && '' !== link[0] ) {
						google.maps.event.addListener( marker, 'click', function() {
							window.open( this.url[0], this.url[1] );
						});
					}

					this._initInfoWindow( marker, i );
				}
			}

			// Marker clustering
			if ( 'undefined' !== typeof MarkerClusterer ) {
				var markerCluster = new MarkerClusterer( this.mapObjects.map, this.mapObjects.markers, {
					imagePath: this.settings.markerClusterImagesURL
				} );
			}
		},

		_initInfoWindow: function( marker, index ) {
			var infoWin 	= new google.maps.InfoWindow();
			var infoWinText = this.infoWindowText[index];

			if ( '' != infoWinText && 'yes' == this.enableInfo[index] ) {
				var contentString = '<div class="pp-infowindow-content">';
					contentString += infoWinText;
					contentString += '</div>';

				var infoWin = new google.maps.InfoWindow({
					content: contentString,
				});

				infoWin.open(this.mapObjects.map, marker);

			}
			// Event that closes the Info Window with a click on the map.
			google.maps.event.addListener( this.mapObjects.map, 'click', ( function ( infowindow ) {
				return function () {
					infowindow.close();
				}
			})(infoWin));

			if ( 'yes' === this.hideTooltip ) {
				infoWin.close();
			};

			if ( '' != infoWinText && 'yes' == this.enableInfo[index] ) {
				var self = this;
				google.maps.event.addListener( marker, 'click', (function ( marker, i ) {
					return function () {
						var contentString = '<div class="pp-infowindow-content">';
							contentString += self.infoWindowText[i];
							contentString += '</div>';

						infoWin.setContent( contentString );

						infoWin.open( self.mapObjects.map, marker );
					}
				})(marker, index));
			}

			this.mapObjects.infoWindows.push( infoWin );
		},

		_autoZoom: function () {
			var map = this.mapObjects.map;
			for (i = 0; i < this.markerData.length; i++) {

				var lat = this.markerData[i]['latitude'],
					lng = this.markerData[i]['longitude'];

				if ( lat != '' && lng != '') {
					var latlng = [
						new google.maps.LatLng( lat, lng ),
					]; 
				}

			}
			var latlngbounds = new google.maps.LatLngBounds();
			for (var i = 0; i < latlng.length; i++) {
				latlngbounds.extend(latlng[i]);
			}
			map.fitBounds(latlngbounds);
		}
	}

})(jQuery);