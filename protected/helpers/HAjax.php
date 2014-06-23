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

class HAjax {
	const STATUS_OK = 'ok';
	const STATUS_NONE = 'none';
	const STATUS_ERROR = 'error';

	private static $_loadedScripts;

	public static function getImgLoadingBig(){
		return Yii::app()->theme->baseUrl.'/images/ajax/loading_big.gif';
	}

	public static function jsonError($msg = 'Error'){
        $msg = $msg == 'Error' ? tc('Error') : $msg;
		echo CJSON::encode(array(
			'status' => self::STATUS_ERROR,
			'msg' => $msg
		));
		Yii::app()->end();
	}


	public static function jsonOk($msg = 'Success', $params = array()){
        $msg = $msg == 'Success' ? tc('Success') : $msg;
		$params = CMap::mergeArray(array(
			'status' => self::STATUS_OK,
			'msg' => $msg
		), $params);

		echo CJSON::encode($params);
		Yii::app()->end();
	}

	public static function jsonNone(){
		echo CJSON::encode(array(
			'status' => self::STATUS_NONE,
		));
		Yii::app()->end();
	}

	public static function implodeModelErrors($model, $glue = '<br><br>'){
		if(empty($model->errors) || !is_array($model->errors)){
			return '';
			//throw new CException('HAjax::implodeModelErrors - нет модели');
		}

		$errorArray = array();

		foreach($model->errors as $field => $errors){
			$errorArray[] = implode($glue, $errors);
		}

		return implode($glue, $errorArray);
	}

	public static function loadScrips($viewUrl = '', $scripts){

		foreach($scripts as $script){
			$jsUrl = $viewUrl . '/' . $script . '.js';
			echo "<script src=\"$jsUrl\"></script>";
		}

	}
}
