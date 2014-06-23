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

class MainController extends ModuleAdminController {
	public $modelName = 'ImageSettings';

	public function actionIndex(){
		$model = new $this->modelName;

		if(isset($_POST[$this->modelName])){
			$model->attributes = $_POST[$this->modelName];

			if($model->validate()){
				$model->save();

				Yii::app()->configuration->init();

				Yii::app()->user->setFlash('success', tt('success_saved', 'service'));
			}
		}

		$this->render('index', array('model' => $model));
	}

	public function actionConvert(){
		@set_time_limit(0);
		@ini_set('max_execution_time', 0);

		$sql = 'SELECT id, owner_id FROM {{apartment}} WHERE 1';
		$res = Yii::app()->db->createCommand($sql)->queryAll();
		$ids = CHtml::listData($res, 'id', 'owner_id');

		$sql = 'SELECT pid, imgsOrder FROM {{galleries}} WHERE 1';
		$res = Yii::app()->db->createCommand($sql)->queryAll();

		if($res){
			foreach($res as $item){
				$images = unserialize($item['imgsOrder']);
				if(!isset($ids[$item['pid']])){
					continue;
				}
				if($images){
					$cnt = 0;
					foreach($images as $image => $name){
						$filePath = Yii::getPathOfAlias('webroot.uploads.apartments.'.$item['pid'].'.pictures').'/'.$image;
						Images::addImage($filePath, $item['pid'], ($cnt == 0), $ids[$item['pid']]);
						$cnt++;
					}
				}
			}
		}
	}

}