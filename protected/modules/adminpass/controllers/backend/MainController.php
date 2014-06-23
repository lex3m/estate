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
	public $modelName = 'User';

	public function actionIndex(){

		$model=$this->loadModel(Yii::app()->user->id);

		if(isset($_POST[$this->modelName])){
			$model->scenario = 'changeAdminPass';

			$model->old_password = $_POST[$this->modelName]['old_password'];
			if($model->validatePassword($model->old_password)){
				if(demo()){
					Yii::app()->user->setFlash('error', tc('Sorry, this action is not allowed on the demo server.'));
					$this->redirect(array('index'));
				}

				$model->attributes=$_POST[$this->modelName];
				if($model->validate()){
					$model->setPassword();
					$model->save(false);
					Yii::app()->user->setFlash('success', Yii::t('module_usercpanel', 'Your password successfully changed.'));
					$this->redirect(array('index'));
				}
			} else {
				Yii::app()->user->setFlash('error', Yii::t('module_adminpass', 'Wrong admin password! Try again.'));
				$this->redirect(array('index'));
			}
		}
		$this->render('index', array('model' => $model));
	}
}