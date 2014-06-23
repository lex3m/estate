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
	public $modelName = 'Publication';
    public $layout='//layouts/inner';

	public function filters() {
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
				'actions' => array('index'),
				'users'=>array('*'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}

	/*public function actionView($id){
        $this->layout='pdf_online';
        $model = $this->loadModel($id);
        $this->render('view',array(
            'model'=>$model,
        ));
	}*/


    public function actionIndex(){
        $publications = Publication::model()->findAll();
        $this->render('index',array(
            'publications'=>$publications,
        ));
    }
}