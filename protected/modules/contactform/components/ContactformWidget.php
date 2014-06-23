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

class ContactformWidget extends CWidget {
	public $page;

	public function getViewPath($checkTheme=false){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'contactform';
		}
		return Yii::getPathOfAlias('application.modules.contactform.views');
	}

	public function run() {
		Yii::import('application.modules.contactform.models.ContactForm');
		$model = new ContactForm;
		$model->scenario = 'insert';

		if(isset($_POST['ContactForm'])){
			$model->attributes=$_POST['ContactForm'];

			if(!Yii::app()->user->isGuest){
				$model->email = Yii::app()->user->email;
				$model->username = Yii::app()->user->username;
			}

			if($model->validate()){
				$notifier = new Notifier;
				$notifier->raiseEvent('onNewContactform', $model);

				Yii::app()->user->setFlash('success', tt('Thanks_for_message', 'contactform'));
				$model = new ContactForm; // clear fields
			} else {
                $model->unsetAttributes(array('verifyCode'));
				Yii::app()->user->setFlash('error', tt('Error_send', 'contactform'));
			}
		}

		$this->render('widgetContactform', array('model' => $model));
	}
}