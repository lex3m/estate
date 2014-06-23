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

class CustomOSMap {

	private static $_instance;
	private static $jsVars;
	private static $jsCode;
	protected static $icon = array();

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

	public static function createMap($isAppartment = false){

		self::$jsVars = '

		var mapOSMap;
		var markerClusterOSMap;

		var markersOSMap = [];
		var markersForClasterOSMap = [];
		var latLngList = [];

		';

		self::$jsCode = '

		var zoomOSMap = '.($isAppartment ? param('module_apartments_osmapsZoomApartment', 15) : param('module_apartments_osmapsZoomCity', 11)).';
		mapOSMap = L.map("osmap").setView(['.param('module_apartments_osmapsCenterY', 55.75411314653655).', '.param('module_apartments_osmapsCenterX', 37.620717508911184).'], zoomOSMap);

		L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
		attribution: "&copy; <a href=\'http://osm.org/copyright\'>OpenStreetMap</a> contributors"
		}).addTo(mapOSMap);

		';

	}

	public static function addMarker($model, $inMarker, $draggable = 'false'){
		if(!$model){
			return false;
		}

		if($model->lat && $model->lng) {
			self::setIconType($model);

			self::$jsCode .= '
				var markerIcon = L.icon({
					iconUrl: "'.self::$icon['href'].'",
					iconSize: ['.self::$icon['size']['x'].', '.self::$icon['size']['y'].'],
					className : "marker-icon-class"
				});

				markersOSMap['.$model->id.'] = L.marker(['.$model->lat.', '.$model->lng.'], {icon: markerIcon, draggable : '.$draggable.'})
					.addTo(mapOSMap)
					.bindPopup("'.CJavaScript::quote($inMarker).'");

				latLngList.push(['.$model->lat.', '.$model->lng.']);
				markersForClasterOSMap.push(markersOSMap['.$model->id.']);
			';

		}
	}

	public static function clusterMarkers(){
		self::$jsCode .= '
			if(markersForClasterOSMap.length > 0){
				var markersCluster = L.markerClusterGroup({spiderfyOnMaxZoom: false, showCoverageOnHover: false, zoomToBoundsOnClick: true, removeOutsideVisibleBounds: true, maxClusterRadius: 30});

				for (var i = 0, markerCluster = markersForClasterOSMap.length; i < markerCluster; i++) {
					markersCluster.addLayer(markersForClasterOSMap[i]);
				}

				mapOSMap.addLayer(markersCluster);

				/*markersCluster.on("clusterclick", function (a) {
					a.layer.zoomToBounds();
				});*/

				mapOSMap.fitBounds(latLngList, {reset: true});
				//mapOSMap.fitBounds(new L.LatLngBounds(latLngList), {padding: [50, 50]});
			}
		';
	}

	public static function setCenter(){
		self::$jsCode .= '
			if(latLngList.length > 0){
				if (latLngList.length == 1) {
					//console.log(new L.LatLngBounds(latLngList));
					mapOSMap.setView(latLngList[0]);
				}
				else {
					mapOSMap.fitBounds(latLngList,{reset: true});
					//mapOSMap.fitBounds(new L.LatLngBounds(latLngList), {reset: true});
				}
			}
		';
	}

	public static function render(){
		//echo CHtml::tag('div', array('id' => 'OSMMap', 'style' => 'width: 100%; height: 586px;'), '', true);
		echo CHtml::script(self::$jsVars);
		echo CHtml::script(PHP_EOL . '$(function(){' . self::$jsCode . '});');
	}


	public static function actionOSmap($id, $model, $inMarker){
		$isOwner = self::isOwner($model);

		// If we have already created marker - show it

		if ($model->lat && $model->lng) {
			self::createMap(true);
			self::$jsCode .= '

			';

			$draggable = $isOwner ? 'true' : 'false';

			self::addMarker($model, $inMarker, $draggable);

			if($isOwner){
				self::$jsCode .= '
					markersOSMap['.$model->id.'].on("dragend", function(event) {
						var marker = event.target;
						var result = marker.getLatLng();

						if (result) {
							$.ajax({
								type:"POST",
								url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
								data:({lat: result.lat, lng: result.lng}),
								cache:false
							});
						}
					});
				';
			}

		} else {
			if(!$isOwner){
				return '';
			}

			$coordinates = NULL;

			if ($coordinates) {
				$model->lat = $coordinates->lat;
				$model->lng = $coordinates->lng;
			} else {
				$model->lat = param('module_apartments_osmapsCenterY', 55.75411314653655);
				$model->lng = param('module_apartments_osmapsCenterX', 37.620717508911184);
			}

			self::actionOSmap($id, $model, $inMarker);
			return false;
		}

		self::setCenter();
		self::render();
	}

	private static function isOwner($model){
		return Yii::app()->user->getState('isAdmin') || param('useUserads', 1) && !Yii::app()->user->isGuest && Yii::app()->user->id == $model->owner_id;
	}

	public static function setIconType($model) {
		// каждому типу свой значок
		if (isset($model->objType->icon_file) && $model->objType->icon_file) {
			self::$icon['href'] = Yii::app()->getBaseUrl().'/'.$model->objType->iconsMapPath.'/'.$model->objType->icon_file;
			self::$icon['size'] = array('x' => ApartmentObjType::MAP_ICON_MAX_WIDTH, 'y' => ApartmentObjType::MAP_ICON_MAX_HEIGHT);
			/*$icon['offset'] = array('x' => -16, 'y' => -2);*/
			self::$icon['offset'] = array('x' => -16, 'y' => -35);
		}
	}
}