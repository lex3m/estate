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
	public $modelName = 'News';

	public function actionCreate(){
		$model = new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->newsImage = CUploadedFile::getInstance($model,'newsImage');
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->newsImage = CUploadedFile::getInstance($this->_model,'newsImage');
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update', array('model'=>$model));
	}

    public function actionProduct(){

        //NewsProduct::getProductNews();
        Yii::app()->user->setState('menu_active', 'news.product');

        $model = NewsProduct::model();
      		$result = $model->getAllWithPagination();

      		$this->render('news_product', array(
      			'items' => $result['items'],
      			'pages' => $result['pages'],
      		));
    }

	public function actionDeleteImg() {
		$newsId = Yii::app()->request->getParam('id');
		$imageId = Yii::app()->request->getParam('imId');

		if ($newsId && $imageId) {
			$newsModel = News::model()->findByPk($newsId);
			if ($newsModel->image_id != $imageId)
				throw404();

			$newsModel->image_id = 0;
			$newsModel->update('image_id');
			
			$imageModel = NewsImage::model()->findByPk($imageId);
			$imageModel->delete();

			$this->redirect(array('/news/backend/main/update', 'id' => $newsId));
		}
		throw404();
	}
}