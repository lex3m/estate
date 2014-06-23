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

class ModuleUserController extends Controller{
	public $metroStations;
	public $userListingId;

	public $cityActive;

	public $layout='//layouts/inner';
	public $params = array();
	private $_model;
	public $modelName;

    public $newFields;


	public function getViewPath($checkTheme=false){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName();
		}
		return Yii::getPathOfAlias('application.modules.'.$this->getModule($this->id)->getName().'.views');
	}

	public function beginWidget($className,$properties=array()){
		if($className == 'CustomForm'){
			$className = 'CActiveForm';
		}
		if($className == 'CustomGridView'){
			$className = 'CGridView';
		}
		return parent::beginWidget($className,$properties);
	}

	public function widget($className,$properties=array(),$captureOutput=false){
		if($className == 'bootstrap.widgets.TbButton'){
			if(isset($properties['htmlOptions'])){
				return CHtml::submitButton($properties['label'], $properties['htmlOptions']);
			} else {
				return CHtml::submitButton($properties['label']);
			}
		}

	    return parent::widget($className,$properties,$captureOutput);
	}

	public function filters(){
		return array(
			'accessControl', // perform access control for CRUD operations
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				'users'=>array('*'),
			),
		);
	}

	public function init(){
		parent::init();
		//$this->metroStations = SearchForm::stationsInit();
		$this->cityActive = SearchForm::cityInit();
	}

	public function actionView($id = 0, $url = ''){
//		if(Yii::app()->user->getState('isAdmin')){
//			$this->redirect(array('backend/main/view', 'id' => $id));
//		}

		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}
		$model = $this->loadModel($id, 1);

		$this->render('view',array(
			'model'=>$model,
		));
	}

	public function actionIndex(){
		$dataProvider=new CActiveDataProvider($this->modelName);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function loadModel($id = null, $resetScope = 0) {
		if($this->_model===null) {
			if($id == null){
				if(isset($_GET['id'])) {
					$model = new $this->modelName;
					if($resetScope){
						$this->_model=$model->resetScope()->findByPk($_GET['id']);
					}else{
						$this->_model=$model->findByPk($_GET['id']);
					}
				}
			}
			else{
				$model = new $this->modelName;
				if($resetScope){
					$this->_model=$model->resetScope()->findByPk($id);
				}else{
					$this->_model=$model->findByPk($id);
				}
			}

			if($this->_model===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}
		}
		return $this->_model;
	}

	public function loadModelWith($with) {
		if($this->_model===null) {
			if(isset($_GET['id'])) {
				$model = new $this->modelName;
				$this->_model = $model->with($with)->findByPk($_GET['id']); //findByPk($_GET['id']);
			}
			if($this->_model===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}
		}
		return $this->_model;
	}


	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->modelName.'-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function sliderImages(){
		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{slider}}');
		$sql = 'SELECT url FROM {{slider}} ORDER BY sorter';
		$items = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryColumn();
		return $this->renderPartial('_slider_image', array('items' => $items), true);
	}
}