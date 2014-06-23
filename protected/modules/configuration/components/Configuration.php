<?php
/**********************************************************************************************
*	copyright			:	(c) 2014 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
***********************************************************************************************/

class Configuration extends CComponent {

	public $cachingTime;
	public static $cacheName = 'module_configuration_model';

	public function init(){
		$this->cachingTime = param('cachingTime', 5184000); // default caching for 60 days
		if (oreInstall::isInstalled()) {
			$this->loadConfig();
		}
	}

	private function loadConfig() {
		Yii::import('application.modules.configuration.models.ConfigurationModel');
		$model = Yii::app()->cache->get(self::$cacheName);
		if($model === false) {
			$model = ConfigurationModel::model()->findAll();
			Yii::app()->cache->set(self::$cacheName, $model, $this->cachingTime);
		}
		foreach ($model as $key) {
			Yii::app()->params[$key->name] = $key->value;
		}
	}

	public static function clearCache(){
		Yii::app()->cache->delete(self::$cacheName);
	}
}
