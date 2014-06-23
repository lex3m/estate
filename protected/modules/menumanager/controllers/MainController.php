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

class MainController extends ModuleUserController{
	public $modelName = 'Menu';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionIndex(){
		if(Yii::app()->user->getState("isAdmin")){
			$this->redirect(array('/menumanager/backend/main/admin'));
			return;
		}
		$this->redirect(array('/site/index'));
	}

	public function actionView($id = 0, $url = ''){
		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}
		$model = $this->loadModel($id);

		if($model){
			if(Yii::app()->request->getParam('is_ajax')){
				$this->renderPartial('/view', array('model' => $model), false, true);
			}else{
				$this->render('/view', array('model' => $model));
			}
		} else {
			Yii::app()->user->setFlash('error', tc('Page not found.'));
			$this->redirect(array('/site/index'));
		}
	}
}