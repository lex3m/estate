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


class ImagesWidget extends CWidget {

	public $objectId;
	public $images;
	public $withMain = false;

	public function getViewPath($checkTheme=false){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'images';
		}
		return Yii::getPathOfAlias('application.modules.images.views');
	}

	public function run() {
		$this->registerAssets();

		if(!$this->images){
			$sql = 'SELECT id, file_name, comment, id_object, file_name_modified, is_main FROM {{images}} WHERE id_object=:id ORDER BY sorter';
			$this->images = Images::model()->findAllBySql($sql, array(':id' => $this->objectId));
		}

		$this->render('widgetImages', array(
			'images' => $this->images,
		));
	}

	public function registerAssets(){
		$assets = dirname(__FILE__).'/../assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);

		if(is_dir($assets)){
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerCssFile($baseUrl . '/prettyphoto/css/prettyPhoto.css');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/prettyphoto/js/jquery.prettyPhoto.js');
			Yii::app()->clientScript->registerScript('prettyPhotoInit', '
				$("a[rel^=\'prettyPhoto\']").prettyPhoto(
					{
						animation_speed: "fast",
						slideshow: 10000,
						hideflash: true,
						social_tools: "",
						gallery_markup: "",
						slideshow: 3000,
						autoplay_slideshow: false,
						deeplinking: false
						/*slideshow: false*/
					}
				);
			', CClientScript::POS_READY);
		} else {
			throw new Exception('Image - Error: Couldn\'t find assets folder to publish.');
		}
	}
}