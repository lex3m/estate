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

class CustomYMap {
	private static $_instance;
	protected $scripts = array();
	protected static $icon = array();

	/**
	 * @return CustomYMap
	 */
	public static function init(){
		self::$icon['href'] = Yii::app()->request->baseUrl."/images/house.png";
		self::$icon['size'] = array('x' => 32, 'y' => 37);
		self::$icon['offset'] = array('x' => -16, 'y' => -35);

		if (!isset(self::$_instance)) {
			$className = __CLASS__;
			self::$_instance = new $className;
		}
		return self::$_instance;
	}

	public function processScripts($applyList = false){
		if($applyList){
			$this->scripts[] = '
				placemarksYMap = placemarksAll;
				if(typeof list !== "undefined"){
					list.apply();
				}
			';
		}

		// end of ymaps.ready(function () {
		$this->scripts[] = '
			    });
			});
		';

		// publish scripts
		echo CHtml::script(implode("\n", $this->scripts));
	}

	public static function getLangForMap(){
		# язык в RFC 3066
		switch(Yii::app()->language) {
			case 'ru':
				$langCode = 'ru-RU';
				break;
			case 'uk':
				$langCode = 'uk-UA';
				break;
			case 'tr':
				$langCode = 'tr-TR';
				break;
			default:
				$langCode = 'en-US';
		}

		if (issetModule('lang') && !isFree()) {
			$langInfo = Lang::model()->find('name_iso = :name_iso', array('name_iso' => Yii::app()->language));
			if ($langInfo && isset($langInfo->name_rfc3066))
				$langCode = $langInfo->name_rfc3066;
		}

		return $langCode;
	}

	public function createMap(){
		# 'yandex#publicMap' и 'yandex#publicMapHybrid' доступны только для России и Украины
		$yMapTypes = '"yandex#map", "yandex#satellite", "yandex#hybrid"';

		if(Yii::app()->language == 'ru' || Yii::app()->language == 'uk'){
			$yMapTypes .= ', "yandex#publicMap", "yandex#publicMapHybrid"';
		}

		$this->scripts[] = '
			var markers = [];
		';

		$this->scripts[] = '
			var globalYMap;
			var placemark;

			$(function(){
            ymaps.ready(function () {
				var placemarksAll = [];

				var map = new ymaps.Map("ymap", {
					center: ['.param("module_apartments_ymapsCenterY", 55.75411314653655).', '.param("module_apartments_ymapsCenterX", 37.620717508911184).'],
					zoom: '.param("module_apartments_ymapsZoomApartment", 15).'
				});

				var typeSelector = new ymaps.control.TypeSelector({
					mapTypes: [
						'.$yMapTypes.'
					]
				});
				typeSelector.setMinWidth(200);

				map.controls.add(typeSelector);
				map.controls.add("mapTools");
				map.controls.add("zoomControl");
				map.controls.add("scaleLine");
				map.controls.add("searchControl");

				map.behaviors.enable("scrollZoom");

				globalYMap = map;
		';
    }

    public function setCenter($lat, $lng) {
	    $this->scripts[] = '
			map.setCenter(['.$lng.', '.$lat.']);
		';
	}

	public function setZoom($zoom) {
		$this->scripts[] = '
			map.setZoom('.$zoom.', {checkZoomRange:true});
		';
	}

	public function setBounds($lat_min, $lat_max, $lng_min, $lng_max) {
		$this->scripts[] = '
			map.setBounds([
				['.$lng_min.', '.$lat_min.'],
				['.$lng_max.', '.$lat_max.']
			])
		';
    }

	public function setClusterer() {
		$this->scripts[] = '
			var clusterIcons=[{
				href: "http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/images/m1.png",
				size: [53,52],
				offset: [0,0]
			}],
			clusterer = new ymaps.Clusterer({gridSize: 53, minClusterSize: 2, clusterIcons: clusterIcons});

			clusterer.add(markers);
			map.geoObjects.add(clusterer);
		';
	}

	public function withoutClusterer() {
		$this->scripts[] = '
		for(var key in markers){
			map.geoObjects.add(markers[key]);
		}
		';
	}

	public function setGeoCenter($city) {
		$ymapsCenterX = param("module_apartments_ymapsCenterX", 37.620717508911184);
		$ymapsCenterY = param("module_apartments_ymapsCenterY", 55.75411314653655);

		$this->scripts[] = '
			var geocoder = ymaps.geocode("'.$city.'", {kind: "locality", results: 1});
			geocoder.then(
				function (res) {
					if (res.geoObjects.getLength()) {
						var point = res.geoObjects.get(0);
						map.setCenter(point.geometry.getCoordinates());
					}
					else {
						map.setCenter(['.$ymapsCenterX.', '.$ymapsCenterY.']);
					}
				},
				function (error) {
					/*alert("Возникла ошибка: " + error.message);*/
					map.setCenter(['.$ymapsCenterX.', '.$ymapsCenterY.']);
				}
			)
		';
	}

	public function changeZoom($zoom, $operator = '-') {
		$this->scripts[] = '
			var oldMapZoom = map.getZoom();
			var newMapZoom = oldMapZoom '.$operator.$zoom.';
			map.setZoom(newMapZoom, {checkZoomRange:true});
		';
    }

	public function addMarker($lat, $lng, $content, $multyMarker = 0, $model = null) {
		$content = $this->filterContent($content);

		$clusterCaption = '';
		if ($model) {
			$clusterCaption = CJavaScript::quote($model->getTitle());
		}
		$draggable = ((Yii::app()->user->getState('isAdmin') || param('useUserads', 1) && (!Yii::app()->user->isGuest && Yii::app()->user->id == $model->owner_id) ) && !$multyMarker) ? ", draggable: true" : "";

		$this->setIconType($model);

		$this->scripts[] = '
			placemark = new ymaps.Placemark(
				['.$lng.', '.$lat.'], {
				balloonContent: "'.$content.'",
				clusterCaption: "'.$clusterCaption.'"
				}, {
					iconImageHref: "'.self::$icon['href'].'",
					iconImageSize: ['.self::$icon['size']['x'].', '.self::$icon['size']['y'].'],
					iconImageOffset: ['.self::$icon['offset']['x'].', '.self::$icon['offset']['y'].'],
					hideIconOnBalloonOpen: false,
					balloonShadow: true,
					balloonCloseButton: true,
					iconMaxWidth: 300
					'.$draggable.'
				}
			);

			'.(($multyMarker) ? '' : 'map.geoObjects.add(placemark); placemark.balloon.open(); ').
			'markers.push(placemark);
			placemarksAll['.$model->id.'] = placemark;
			';
	}

	public function filterContent($content){
		$content = preg_replace('/\r\n|\n|\r/', "\\n", $content);
		$content = preg_replace('/(["\'])/', '\\\\\1', $content);

		return $content;
	}

	public function actionYmap($id, $model, $inMarker){

		$centerX = param('module_apartments_ymapsCenterX', 37.620717508911184);
		$centerY = param('module_apartments_ymapsCenterY', 55.75411314653655);
		$defaultCity = param('defaultCity', 'Москва');

		if($model->city && $model->city->name){
			$centerX = 0;
			$centerY = 0;
			$defaultCity = $model->city->name;
		}

		$this->createMap();

		// If we have already created marker - show it
		if ($model->lat && $model->lng) {
			$this->setCenter($model->lat, $model->lng);
			$this->setZoom(param('module_apartments_ymapsZoomApartment', 15));

			// Preparing InfoWindow with information about our marker.
			$this->addMarker($model->lat, $model->lng, $inMarker, 0, $model);

			if(Yii::app()->user->getState('isAdmin') || param('useUserads', 1) && !Yii::app()->user->isGuest && Yii::app()->user->id == $model->owner_id){
				$this->scripts[] = '
					placemark.events.add("dragend", function (e) {
						var coordsDragend = placemark.geometry.getCoordinates();

						var coordsDragendLat = coordsDragend[1];
						var coordsDragendLng = coordsDragend[0];

						$.ajax({
							type:"POST",
							url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
							data:({lat: coordsDragendLat, lng: coordsDragendLng}),
							cache:false
						})
					});
				';
		    }
		}
		else {
			if(Yii::app()->user->getState('isAdmin') || param('useUserads', 1) && !Yii::app()->user->isGuest && Yii::app()->user->id == $model->owner_id){
				if ($centerX && $centerY) {
					$this->setCenter($centerY, $centerX);
				} else {
					$this->setGeoCenter($defaultCity);
				}
				$this->setZoom(param('module_apartments_ymapsZoomCity', 11));
				$this->setIconType($model);

				$this->addMarker($centerY, $centerX, $inMarker, 0, $model);

				$inMarker = $this->filterContent($inMarker);

				$this->scripts[] = '
					var onClick = function(e) {
						var coordsMapClick = e.get("coordPosition");

						var coordsDragendLat = coordsMapClick[1];
						var coordsDragendLng = coordsMapClick[0];

						placemark = new ymaps.Placemark(
							[coordsDragendLng, coordsDragendLat], {
								balloonContent: "'.$inMarker.'"
							}, {
								iconImageHref: "'.self::$icon['href'].'",
								iconImageSize: ['.self::$icon['size']['x'].', '.self::$icon['size']['y'].'],
								iconImageOffset: ['.self::$icon['offset']['x'].', '.self::$icon['offset']['y'].'],
								hideIconOnBalloonOpen: false,
								balloonShadow: true,
								balloonCloseButton: true,
								iconMaxWidth: 300,
								draggable: true
							}
						);

						map.geoObjects.add(placemark);

						$.ajax({
							type:"POST",
							url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
							data:({lat: coordsDragendLat, lng: coordsDragendLng}),
							cache:false
						});

						placemark.balloon.open();
						map.events.remove("click", onClick);

						placemark.events.add("dragend", function (e) {
							var coordsDragend = placemark.geometry.getCoordinates();

							var coordsDragendLat = coordsDragend[1];
							var coordsDragendLng = coordsDragend[0];

							$.ajax({
								type:"POST",
								url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
								data:({lat: coordsDragendLat, lng: coordsDragendLng}),
								cache:false
							})
						});
					};
					map.events.add("click", onClick);
				';
			}
		}

		$this->processScripts();
		return true;
	}

	public function setIconType($model) {
		// каждому типу свой значок
		if (isset($model->objType->icon_file) && $model->objType->icon_file) {
			self::$icon['href'] = Yii::app()->getBaseUrl().'/'.$model->objType->iconsMapPath.'/'.$model->objType->icon_file;
			self::$icon['size'] = array('x' => ApartmentObjType::MAP_ICON_MAX_WIDTH, 'y' => ApartmentObjType::MAP_ICON_MAX_HEIGHT);
			/*$icon['offset'] = array('x' => -16, 'y' => -2);*/
			self::$icon['offset'] = array('x' => -16, 'y' => -35);
		}
	}
}