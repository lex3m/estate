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
	public $modelName = 'Publication';

    public $filter = array(
        'country_id' => 0,
        'region_id' => 0,
        'city_id' => 0,
        'type' => 0,
    );

    public function getFilterValue($key){
        return isset($this->filter[$key]) ? $this->filter[$key] : 0;
    }

	public function actionCreate(){
		$model = new $this->modelName;

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];

            $model->image=CUploadedFile::getInstance($this->modelName,'image');
            $model->document_file=CUploadedFile::getInstance($this->modelName,'document_file');
            $model->document=$model->document_file;
            $model->snapshot=$model->image;
            $model->date = date('Y-m-d');
            print_r($model->getErrors());
			if($model->save()){
				//$this->redirect(array('view','id'=>$model->id));
                $model->document_file->saveAs(Yii::app()->request->baseUrl.'media/publications/docs/'.$model->document);
                $model->image->saveAs(Yii::app()->request->baseUrl.'media/publications/snapshots/'.$model->image);
				$this->redirect(array('admin'));
			}
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

        if($model->widget == 'apartments' && $model->widget_data){
            $this->filter = CJSON::decode($model->widget_data);
        }

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				//$this->redirect(array('view','id'=>$model->id));
				$this->redirect(array('admin'));
			}
		}

		$this->render('update', array('model'=>$model));
	}

	public function actionDelete($id){
		if($id == InfoPages::MAIN_PAGE_ID){
			Yii::app()->user->setFlash('error', tt('backend_menumanager_main_admin_noDeleteSystemItem', 'menumanager'));
			$this->redirect('admin');
		}

		if (Yii::app()->cache->get('menu'))
			Yii::app()->cache->delete('menu');

		parent::actionDelete($id);
	}

    public function actionDeletePublication($id){
        $model = $this->loadModel($id);
        $doc = $model->document;
        $snapshot = $model->snapshot;
        if ($model->delete()){
            unlink(Yii::app()->request->baseUrl.'media/publications/docs/'. $doc);
            unlink(Yii::app()->request->baseUrl.'media/publications/snapshots/'. $snapshot);
        }
    }
}