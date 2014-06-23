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
	public function init() {
		parent::init();

		$specialOfferPage = Menu::model()->findByPk(Menu::SPECIALOFFERS_ID);
		if ($specialOfferPage) {
			if ($specialOfferPage->active == 0) {
				throw404();
			}
		}
	}

	public function actionIndex(){
        Yii::app()->user->setState('searchUrl', NULL);

		Yii::app()->getModule('apartments');

		$criteria = new CDbCriteria;
		$criteria->condition = 'is_special_offer = 1';

        if(isset($_GET['is_ajax'])){
            $this->renderPartial('index', array(
                'criteria' => $criteria,
            ), false, true);
        }else{
            $this->render('index', array(
                'criteria' => $criteria,
            ));
        }
	}
}