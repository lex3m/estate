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

	public $modelName = 'ReferenceValues';
	public $maxSorters = array();

	public function actionView($id){
		$this->redirect(array('admin'));
	}
	public function actionIndex(){
		$this->redirect(array('admin'));
	}

	public function actionAdmin(){
		$sql = 'SELECT reference_category_id, MAX(sorter) as sorter FROM {{apartment_reference_values}} GROUP BY reference_category_id';
		$sorters = Yii::app()->db->createCommand($sql)->queryAll();
		foreach($sorters as $sorter){
			$this->maxSorters[$sorter['reference_category_id']] = $sorter['sorter'];
		}

		if(isset($_GET['ReferenceValues']['category_filter'])){
			$this->params['currentCategory'] = intval($_GET['ReferenceValues']['category_filter']);
		}
		else{
			$this->params['currentCategory'] = 0;
		}

		parent::actionAdmin();

	}

	public function getCategories($withoutEmpty = 0){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as lang FROM {{apartment_reference_categories}} ORDER BY sorter ASC';
		$categories = Yii::app()->db->createCommand($sql)->queryAll();

		if(!$withoutEmpty)
			$return[0] = '';
		foreach($categories as $category){
			$return[$category['id']] = $category['lang'];
		}
		return $return;
	}

}
