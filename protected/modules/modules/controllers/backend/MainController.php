<?php
/**********************************************************************************************
*	copyright			:	(c) 2014 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
***********************************************************************************************/

class MainController extends ModuleAdminController {
	public $defaultAction='admin';

	public function actionAdmin(){
		$this->render('modules');
	}

	public function actionManipulate($type, $module){
		if($type == 'enable'){
			ConfigurationModel::updateValue('module_enabled_'.$module, 1);
		} else {
			ConfigurationModel::updateValue('module_enabled_'.$module, 0);
		}
		$this->redirect(array('admin'));
	}

}
