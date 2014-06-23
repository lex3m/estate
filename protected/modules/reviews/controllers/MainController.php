<?php
/**********************************************************************************************
 *                            CMS Open Business Card
 *                              -----------------
 *	version				:	1.8.1
 *	copyright			:	(c) 2014 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Business Card
 *
 * Open Business Card is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Business Card is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/


class MainController extends ModuleUserController {
	public $modelName = 'Reviews';

	public function init() {
		parent::init();

		$reviewsPage = Menu::model()->findByPk(Menu::REVIEWS_ID);
		if ($reviewsPage) {
			if ($reviewsPage->active == 0) {
				throw404();
			}
		}
	}

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionIndex(){
		$criteria=new CDbCriteria;
		//$criteria->order = 'sorter';
		$criteria->order = 'date_created DESC';
		$criteria->condition = 'active='.Reviews::STATUS_ACTIVE;

		$pages = new CPagination(Reviews::model()->count($criteria));
		$pages->pageSize = param('module_reviews_itemsPerPage', 10);
		$pages->applyLimit($criteria);

		$reviews = Reviews::model()->cache(param('cachingTime', 1209600), Reviews::getCacheDependency())->findAll($criteria);

		$this->render('index',array(
			'reviews' => $reviews, 'pages' => $pages
		));
	}

	public function actionAdd($isFancy = 0){
		$model = new Reviews;

		if(isset($_POST[$this->modelName])){
			$model->attributes = $_POST[$this->modelName];

			if($model->validate()){
				if ($model->save(false)) {

					$model->name = CHtml::encode($model->name);
					$model->body = CHtml::encode($model->body);

					$notifier = new Notifier;
					$notifier->raiseEvent('onNewReview', $model);

					if (Yii::app()->user->getState('isAdmin'))
						Yii::app()->user->setFlash('success', tt('success_send_not_moderation'));
					else
						Yii::app()->user->setFlash('success', tt('success_send'));
					$this->redirect(array('index'));
				}
				$model->unsetAttributes(array('name', 'body','verifyCode'));
			}
			else {
				Yii::app()->user->setFlash('error', tt('failed_send'));
			}
			$model->unsetAttributes(array('verifyCode'));
		}
		if($isFancy){
			$this->excludeJs();
			$this->renderPartial('add', array('model'=>$model), false, true);
		} else {
			$this->render('add', array('model'=>$model));
		}
	}
}