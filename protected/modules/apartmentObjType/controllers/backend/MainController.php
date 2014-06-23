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

class MainController extends ModuleAdminController{
	public $modelName = 'ApartmentObjType';

	public function actionView($id){
		$this->redirect(array('admin'));
	}
	public function actionIndex(){
		$this->redirect(array('admin'));
	}

	public function actionAdmin(){
		$this->getMaxSorter();
		parent::actionAdmin();
	}

	public function actionCreate(){
		$model = new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->validate()) {

				$model->iconUpload = CUploadedFile::getInstance($model, 'icon_file');
				if ($model->iconUpload) {
					$iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';

					//$model->icon_file = $model->iconUpload->name;
					$model->icon_file = md5(uniqid()).'.'.$model->iconUpload->extensionName;

					// загружаем и ресайзим иконку
					$model->iconUpload->saveAs($iconUploadPath.$model->icon_file);

					Yii::import('application.extensions.image.Image');
					$icon = new Image($iconUploadPath.$model->icon_file);

					$icon->resize(ApartmentObjType::MAP_ICON_MAX_WIDTH, ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					$icon->save();
				}

				if($model->save(false)){
					$this->redirect(array('admin'));
				}
			}
		}

		$this->render('create',array_merge(
			array('model'=>$model),
			$this->params
		));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$isUploadIcon = false;

			$iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';
			$model->iconUpload = CUploadedFile::getInstance($model, 'icon_file');

			if ($model->iconUpload)
				$isUploadIcon = true;

			if ($isUploadIcon) {
				if ($model->icon_file) { // если уже есть - удаляем старую иконку
					$oldIconPath = $iconUploadPath.$model->icon_file;
					if (file_exists($oldIconPath)) {
						@unlink($oldIconPath);
					}
				}
			}

			$model->attributes=$_POST[$this->modelName];

			if($model->validate()) {
				if ($isUploadIcon) {
					//$model->icon_file = $model->iconUpload->name;
					$model->icon_file = md5(uniqid()).'.'.$model->iconUpload->extensionName;

					// загружаем и ресайзим иконку
					$model->iconUpload->saveAs($iconUploadPath.$model->icon_file);

					Yii::import('application.extensions.image.Image');
					$icon = new Image($iconUploadPath.$model->icon_file);

					$icon->resize(ApartmentObjType::MAP_ICON_MAX_WIDTH, ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					$icon->save();
				}

				if($model->save(false)){
					$this->redirect(array('admin'));
				}
			}
		}

		$this->render('update',
			array_merge(
				array('model'=>$model),
				$this->params
			)
		);
	}

    public function actionDelete($id){

        // Не дадим удалить последний тип
        if(ApartmentObjType::model()->count() <= 1){
            if(!isset($_GET['ajax'])){
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
            Yii::app()->end();
        }

        parent::actionDelete($id);
    }

	public function actionDeleteIcon($id = null) {
	    if ($id) {
		 	$model = $this->loadModel($id);
		    if ($model->icon_file) {
			    $iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';

			    $oldIconPath = $iconUploadPath.$model->icon_file;
			    if (file_exists($oldIconPath)) {
				    @unlink($oldIconPath);
			    }

			    $model->icon_file = '';
			    $model->update(array('icon_file'));
		    }
	    }
		$this->redirect(array('update', 'id' => $id));
	}
}
