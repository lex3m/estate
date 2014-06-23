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
	public $modelName = 'User';
	public $scenario = 'backend';

	public function actionCreate(){
		$model=new $this->modelName;
		if($this->scenario){
			$model->scenario = $this->scenario;
		}

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->validate()){
				$model->setPassword();
				$model->save(false);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array_merge(
				array('model'=>$model),
				$this->params
		));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);
		$model->scenario = 'update';

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];

			if (isset($_POST[$this->modelName]['password']) && $_POST[$this->modelName]['password'])
				if (demo()) {
					Yii::app()->user->setFlash('error', tc('Sorry, this action is not allowed on the demo server.'));
					unset($model->password, $model->salt);
					$this->redirect(array('update','id'=>$model->id));
				} else
					$model->scenario = 'changePass';
			else
				unset($model->password, $model->salt);

			if($model->validate()) {
				if ($model->scenario == 'changePass')
					$model->setPassword();

				if($model->save(false)){
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}
		$this->render('update', array('model'=>$model));
	}

	public function actionView($id){
		if ($id == 1) {
			$this->redirect(array('admin'));
		}

		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public static function returnStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		if($ignore && ((is_array($ignore) && in_array($data->id, $ignore)) || $data->id == $ignore)){
			return '<div align="center">'.
			$img = CHtml::image(
					Yii::app()->request->baseUrl.'/images/'.($data->active?'':'in').'active_grey.png',
					Yii::t('common', $data->active?'Active':'Inactive')).
				'</div>';
		}

		$url = Yii::app()->controller->createUrl("activate", array("id" => $data->id, 'action' => ($data->active==1?'deactivate':'activate') ));

		$img = CHtml::image(
			Yii::app()->request->baseUrl.'/images/'.($data->active?'':'in').'active.png',
			Yii::t('common', $data->active?'Active':'Inactive'),
			array('title' => Yii::t('common', $data->active?'Deactivate':'Activate'))
		);
		$options = array();
		if($onclick){
			$options = array(
				'onclick' => 'ajaxSetStatus(this, "'.$tableId.'"); return false;',
			);
		}
		return '<div align="center">'.CHtml::link($img,$url, $options).'</div>';
	}

	public function actionActivate(){
		$field = isset($_GET['field']) ? $_GET['field'] : 'active';

		$action = $_GET['action'];
		$id = $_GET['id'];

		if(!(!$id && $action === null)){
			$model = $this->loadModel($id);

			if($this->scenario){
				$model->scenario = $this->scenario;
			}

			if($model) {
				$model->$field = ($action == 'activate'?1:0);
				$model->update(array($field));

				User::destroyUserSession($model->id);
			}
		}

		if(!Yii::app()->request->isAjaxRequest){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}
}