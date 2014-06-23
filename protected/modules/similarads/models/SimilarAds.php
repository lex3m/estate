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

class SimilarAds extends CActiveRecord {

	public $similarAdsModulePath;
	public $assetsPath;

	public function init() {
		$this->preparePaths();
		//$this->publishAssets();
	}

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment}}';
	}

	public function preparePaths() {
		$this->similarAdsModulePath = dirname(__FILE__) . '/../';
		$this->assetsPath = $this->similarAdsModulePath . '/assets';
	}

	public function publishAssets() {
		if (is_dir($this->assetsPath)) {
			$baseUrl = Yii::app()->assetManager->publish($this->assetsPath);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/similarads.css');
			Yii::app()->clientScript->registerCssFile($baseUrl . '/jcarousel/skins/tango/skin.css');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/jcarousel/lib/jquery.jcarousel.min.js', CClientScript::POS_END);
		}
	}

	public function getSimilarAds($inCriteria = null){
		if($inCriteria === null){
			$criteria = new CDbCriteria;
			$criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
			if (param('useUserads'))
				$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);
			$criteria->order = $this->getTableAlias().'.id ASC';
		} else {
			$criteria = $inCriteria;
		}

		Yii::import('application.modules.apartments.helpers.apartmentsHelper');

		$similarAds = array();
		$similarAds['apartments'] = Apartment::model()
			->cache(param('cachingTime', 1209600), Apartment::getImagesDependency())
			->with(array('images'))
			->findAll($criteria);

		return (is_array($similarAds['apartments']) && count($similarAds['apartments'])) ? $similarAds['apartments'] : '';
	}
}