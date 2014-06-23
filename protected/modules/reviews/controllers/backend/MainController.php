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


class MainController extends ModuleAdminController{
	public $modelName = 'Reviews';
	public $redirectTo = array('admin');

	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id)
		));
	}

	public function actionAdmin(){
		$this->getMaxSorter();
		$this->getMinSorter();
		parent::actionAdmin();
	}

	public function actionCreate(){
		Yii::app()->user->setState('menu_active', 'reviews.create');
		parent::actionCreate();
	}
}