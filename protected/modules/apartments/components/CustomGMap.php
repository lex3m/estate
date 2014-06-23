<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.8.1
*	copyright			:	(c) 2014 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
*
* This file is part of CMS Open Real Estate
*
* Open Real Estate is free software. This work is licensed under a GNU GPL.
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
***********************************************************************************************/

class CustomGMap {

	private static $jsVars;
	private static $jsCode;

	public static function createMap($isAppartment = false){

		self::$jsVars = '
		var mapGMap;
		var fenWayPanorama;

		var markersGMap = [];
		var markersForClasterGMap = [];
		var infoWindowsGMap = [];
		var latLngList = [];

		var markerClusterGMap;

		';

		self::$jsCode = '

		var centerMapGMap = new google.maps.LatLng('.param('module_apartments_gmapsCenterY', 55.75411314653655).', '.param('module_apartments_gmapsCenterX', 37.620717508911184).');

        mapGMap = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: '. ($isAppartment ? param('module_apartments_gmapsZoomApartment', 15) : param('module_apartments_gmapsZoomCity', 11)) .',
            center: centerMapGMap,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

		';

	}

	public static function addMarker($model, $inMarker, $draggable = 'false'){

		if(!$model){
			return false;
		}

		if($model->lat && $model->lng) {

			self::$jsCode .= '
				var latLng'.$model->id.' = new google.maps.LatLng('.$model->lat.', '.$model->lng.');

				latLngList.push(latLng'.$model->id.');

				markersGMap['.$model->id.'] = new google.maps.Marker({
					position: latLng'.$model->id.',
					title: "'.CJavaScript::quote($model->getStrByLang('title')).'",
					icon: "'.$model->getMapIconUrl().'",
					map: mapGMap,
					draggable: '.$draggable.'
				});

				markersForClasterGMap.push(markersGMap['.$model->id.']);

				infoWindowsGMap['.$model->id.'] = new google.maps.InfoWindow({
					content: "'.CJavaScript::quote($inMarker).'"
				});

				google.maps.event.addListener(markersGMap['.$model->id.'], "click", function() {
				   infoWindowsGMap['.$model->id.'].open(mapGMap, markersGMap['.$model->id.']);
				});

			';

		}
	}

	public static function clusterMarkers(){
		self::$jsCode .= 'markerClusterGMap = new MarkerClusterer(mapGMap, markersForClasterGMap);';
	}

	public static function setCenter(){
		self::$jsCode .= '
			if(latLngList.length > 0){
				//  Create a new viewpoint bound
				var bounds = new google.maps.LatLngBounds ();
				//  Go through each...
				for (var i = 0, LtLgLen = latLngList.length; i < LtLgLen; i++) {
					//  And increase the bounds to take this point
					bounds.extend (latLngList[i]);
				}
				//  Fit these bounds to the map
				mapGMap.fitBounds(bounds);
			}
		';
	}

	public static function render(){
		echo CHtml::tag('div', array('id' => 'googleMap', 'style' => 'width: 100%; height: 586px;'), '', true);

        $js1 = 'https://maps.google.com/maps/api/js?v=3.5&sensor=false&callback=initGmap&language='.Yii::app()->language;
        $js2 = 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js';

        self::$jsVars .= "\n loadScript('$js1', true);\n loadScript('$js2', true);\n";

        //echo CHtml::script(self::$jsVars);
        echo CHtml::script(PHP_EOL . self::$jsVars . PHP_EOL . 'function initGmap() { ' . self::$jsCode . ' }');
	}


	public static function actionGmap($id, $model, $inMarker, $withPanorama = false){

		$isOwner = self::isOwner($model);

		// If we have already created marker - show it
		if ($model->lat && $model->lng) {
			self::createMap(true);
			self::$jsCode .= '
				mapGMap.setCenter(new google.maps.LatLng('.$model->lat.', '.$model->lng.'));
			';

			$draggable = $isOwner ? 'true' : 'false';

			self::addMarker($model, $inMarker, $draggable);

			if($isOwner){
				self::$jsCode .= '
					google.maps.event.addListener(markersGMap['.$model->id.'], "dragend", function (event) { $.ajax({
						type: "POST",
						url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
						data: ({"lat": event.latLng.lat(), "lng": event.latLng.lng()}),
						cache:false
					}); });
				';
			}

		// If we don't have marker in database - make sure user can create one
		} else {
			if(!$isOwner){
				return '';
			}

			$coordinates = NULL;

			/*if($model->city && $model->city->name){
				$result = Geocoding::getGeocodingInfoJsonGoogle($model->city->name, '');


				if ($result && isset($result->status) && $result->status == 'OK') {
					$coordinates = isset($result->results[0]) ? $result->results[0]->geometry->location : '';
				}
			}*/

			if ($coordinates) {
				$model->lat = $coordinates->lat;
				$model->lng = $coordinates->lng;
			} else {
				$model->lat = param('module_apartments_gmapsCenterY', 37.620717508911184);
				$model->lng = param('module_apartments_gmapsCenterX', 55.75411314653655);
			}

			self::actionGmap($id, $model, $inMarker);
			return false;
		}

        if($withPanorama){
            self::$jsCode .= '

                    var fenWayPanorama = new google.maps.LatLng('.$model->lat.', '.$model->lng.');

					if (($("#gmap-panorama").length > 0)) {
						var streetViewService = new google.maps.StreetViewService();
						streetViewService.getPanoramaByLocation(fenWayPanorama, 30, function (streetViewPanoramaData, status) {
							if (status === google.maps.StreetViewStatus.OK) {
								$("#gmap-panorama").show().css("visibility", "visible");
								google.maps.event.addDomListener(window, "load", initializeGmapPanorama);
							} else {
								$("#gmap-panorama").hide().css("visibility", "hidden");
							}
						});
					}
            ';
        }

		self::render();
	}

	private static function isOwner($model){
		return Yii::app()->user->getState('isAdmin') || param('useUserads', 1) && !Yii::app()->user->isGuest && Yii::app()->user->id == $model->owner_id;
	}
}