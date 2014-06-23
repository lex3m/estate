<?php

class CharCounter extends CWidget {

	public $target = 'textarea';
	public $count = 100;
	public $config = array();

	// function to init the widget
	public function init() {
		$this->publishAssets();
	}

	public function run() {
		$config = CJavaScript::encode($this->config);
		Yii::app()->clientScript->registerScript($this->getId(), "
			$('$this->target').charCounter($this->count, $config);
		", CClientScript::POS_READY);
	}

	// function to publish and register assets on page
	public function publishAssets() {
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if (is_dir($assets)) {
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile($baseUrl.'/jquery.charcounter.js', CClientScript::POS_END);
		} else {
			throw new Exception('CharCounter - Error: Couldn\'t find assets to publish.');
		}
	}

}