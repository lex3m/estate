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

class MainController extends ModuleUserController {
	public $modelName = 'ApartmentsComplain';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionComplain($isFancy = 0) {
		$id = Yii::app()->request->getParam('id', 0);

		if (!$id)
			throw404();

		$model = new $this->modelName;

		$modelApartment = Apartment::model()->findByPk($id);
		if (!$modelApartment)
			throw404();

		if(isset($_POST[$this->modelName])){
			$model->attributes = $_POST[$this->modelName];

			$model->apartment_id = $id;
			$model->session_id = Yii::app()->session->sessionId;
			$model->user_id = 0;

			if(!Yii::app()->user->isGuest){
				$model->email = Yii::app()->user->email;
				$model->name = Yii::app()->user->username;
				$model->user_id = Yii::app()->user->id;
			}

			if ($model->validate()) {
				if ($this->checkAlreadyComplain($model->apartment_id, $model->user_id, $model->session_id)) {
					if ($model->save(false)) {
						$notifier = new Notifier;
						$notifier->raiseEvent('onNewComplain', $model);

						Yii::app()->user->setFlash('success', tt('Thanks_for_complain', 'apartmentsComplain'));
						$model = new $this->modelName; // clear fields
					}
				}
				else
					Yii::app()->user->setFlash('notice', tt('your_already_post_complain', 'apartmentsComplain'));
			}
		}

		if($isFancy){
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('complain_form', array(
				'model' => $model,
				'apId' => $id,
				'isFancy' => true,
				'modelApartment' => $modelApartment,
			), false, true);
		}
		else{
			$this->render('complain_form', array('model' => $model, 'apId' => $id, 'modelApartment' => $modelApartment));
		}
	}

	public function checkAlreadyComplain($apartmentId = 0, $userId = 0, $sessionId = 0) {
		if (!$apartmentId)
			return false;

		if ($userId) { // авторизированный пользователь
			$result = ApartmentsComplain::model()->findByAttributes(array('user_id' => $userId, 'apartment_id' => $apartmentId));
			if ($result)
				return false;
		}
		elseif ($sessionId) { // гость
			$result = ApartmentsComplain::model()->findByAttributes(array('session_id' => $sessionId, 'apartment_id' => $apartmentId));
			if ($result)
				return false;
		}
		return true;
	}
}