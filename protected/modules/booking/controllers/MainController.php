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
	public $modelName = 'Booking';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionBookingform($isFancy = 0){
		Yii::app()->getModule('apartments');

		$this->modelName = 'Apartment';
		$apartment = $this->loadModel();
		$this->modelName = 'Booking';

		$booking = new Booking;
		$booking->scenario = 'bookingform';

		if(isset($_POST['Booking'])){
			$booking->attributes=$_POST['Booking'];
			$booking->apartment_id = $apartment->id;

			if($booking->validate()){
                $booking->time_inVal = $this->getI18nTimeIn($booking->time_in);
                $booking->time_outVal = $this->getI18nTimeOut($booking->time_out);

				if (issetModule('bookingtable')) {
					Bookingtable::addRecord($booking);
				}

				$notifier = new Notifier();

                $types = Apartment::getI18nTypesArray();
                $booking->type = $types[Apartment::TYPE_RENTING];

				$ownerApartment = User::model()->findByPk($apartment->owner_id);

				$booking->ownerEmail = $ownerApartment->email;

				$notifier->raiseEvent('onNewBooking', $booking, array('user' => $ownerApartment));

				Yii::app()->user->setFlash('success', tt('Operation successfully complete. Your order will be reviewed by owner.'));
				$this->redirect($apartment->getUrl());
			}
		}

		$user = null;
		if(!Yii::app()->user->isGuest){
			$user = User::model()->findByPk(Yii::app()->user->getId());
		}

		if($isFancy){
			$this->excludeJs();

			$this->renderPartial('bookingform', array(
				'apartment' => $apartment,
				'model' => $booking,
				'isFancy' => true,
				'user' => $user,
			), false, true);
		}
		else{
			$this->render('bookingform', array(
				'apartment' => $apartment,
				'model' => $booking,
				'isFancy' => false,
				'user' => $user,
			));
		}
	}

	public function actionMainform($isFancy = 0){
		$model = new SimpleformModel;
		$model->scenario = 'forrent';

		if(isset($_POST['SimpleformModel'])){
			$request = Yii::app()->request;
			$isForBuy = $request->getPost('isForBuy', 0);

			$model->attributes = $_POST['SimpleformModel'];

			if ($isForBuy) {
				$model->scenario = 'forbuy';
			}


			if($model->validate()){
               /* if($_POST['SimpleformModel']['type'] != Apartment::TYPE_RENTING){
                    $model->time_inVal = NULL;
                    $model->time_outVal = NULL;
                    $model->date_start = NULL;
                    $model->date_end = NULL;
                }elseif ($model->time_in || $model->time_out) {
					$model->time_inVal = $this->getI18nTimeIn($model->time_in);
					$model->time_outVal = $this->getI18nTimeOut($model->time_out);
				}*/

				//$types = Apartment::getI18nTypesArray();
				//$model->type = $types[$model->type];

				$notifier = new Notifier;
                $notifier->raiseEvent('onNewBooking', $model);

				Yii::app()->user->setFlash('success', tt('Operation successfully complete. Your order will be reviewed by administrator.'));
			}
		}

		$user = null;
		if(!Yii::app()->user->isGuest){
			$user = User::model()->findByPk(Yii::app()->user->getId());
		}

		$type = Apartment::getTypesWantArray();

		if($isFancy){
			$this->excludeJs();

			$this->renderPartial('simpleform', array(
				'model' => $model,
				'type' => $type,
				'user' => $user,
				'isFancy' => true,
			), false, true);
		} else {
			$this->render('simpleform', array(
				'model' => $model,
				'type' => $type,
				'user' => $user,
				'isFancy' => false,
			));
		}
	}

	public function createUser($email, $username = '', $phone = '', $activateKey = '', $isActive = false){
		$model = new User;
		$model->email = $email;
		if($username)
			$model->username = $username;
		if($phone)
			$model->phone = $phone;
		if ($isActive)
 			$model->active = 1;
		if ($activateKey)
			$model->activatekey = $activateKey;

		$password = $model->randomString();
		$model->setPassword($password);

		$return = array();

		if($model->save()){
			$return = array(
				'email' => $model->email,
				'username' => $model->username,
				'password' => $password,
				'id' => $model->id,
				'active' => $model->active,
				'activateKey' => $activateKey,
				'activateLink' => Yii::app()->createAbsoluteUrl('/site/activation', array('key' => $activateKey))
			);
		}
		return $return;
	}

	public function getTimesIn(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_times_in}}';

		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}

	public function getI18nTimeIn($time_in){
		$result = array();
		$default = Lang::getDefaultLang();
		$admin = Lang::getAdminMailLang();
		$current = Yii::app()->language;

		$sql = 'SELECT title_'.$default.' as title FROM {{apartment_times_in}} WHERE id='.$time_in;
		$result['default'] = Yii::app()->db->createCommand($sql)->queryScalar();

		if ($admin != $default) {
			$sql = 'SELECT title_'.$admin.' as title FROM {{apartment_times_in}} WHERE id='.$time_in;
			$result['admin'] = Yii::app()->db->createCommand($sql)->queryScalar();
		} else
			$result['admin'] = $result['default'];

		if ($current != $default) {
			$sql = 'SELECT title_'.$current.' as title FROM {{apartment_times_in}} WHERE id='.$time_in;
			$result['current'] = Yii::app()->db->createCommand($sql)->queryScalar();
		} else
			$result['current'] = $result['default'];


		return $result;
	}

	public function getTimesOut(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_times_out}}';

		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}

	public function getI18nTimeOut($time_out){
		$result = array();
		$default = Lang::getDefaultLang();
		$admin = Lang::getAdminMailLang();
		$current = Yii::app()->language;

		$sql = 'SELECT title_'.$default.' as title FROM {{apartment_times_in}} WHERE id='.$time_out;
		$result['default'] = Yii::app()->db->createCommand($sql)->queryScalar();

		if ($admin != $default) {
			$sql = 'SELECT title_'.$admin.' as title FROM {{apartment_times_in}} WHERE id='.$time_out;
			$result['admin'] = Yii::app()->db->createCommand($sql)->queryScalar();
		} else
			$result['admin'] = $result['default'];

		if ($current != $default) {
			$sql = 'SELECT title_'.$current.' as title FROM {{apartment_times_in}} WHERE id='.$time_out;
			$result['current'] = Yii::app()->db->createCommand($sql)->queryScalar();
		} else
			$result['current'] = $result['default'];


		return $result;
	}

	public function getExistRooms(){
		return Apartment::getExistsRooms();
	}

	public function generateActivateKey() {
		return md5(uniqid());
	}
}
