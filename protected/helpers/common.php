<?php

/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.8.1
 * 	copyright			:	(c) 2014 Monoray
 * 	website				:	http://www.monoray.ru/
 * 	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ********************************************************************************************* */

function setLang() {
	if(isFree()){
		return;
	}
	$app = Yii::app();
	$user = $app->user;

	$lang = Lang::getDefaultLang();
	$app->setLanguage($lang);
	$activeLangs = Lang::getActiveLangs();

	if (isset($_GET['lang'])) {
		$tmplang = $_GET['lang'];
		if (isset($activeLangs[$tmplang])) {
			$lang = $tmplang;
			$app->setLanguage($lang);
		}
		setLangCookie($lang);
		/*
		* другой код, например обновление кеша некоторых компонентов, которые изменяются при смене языка
		*/
	} else {
		if ($user->hasState('_lang')) {
			$tmplang = $user->getState('_lang');

			if (isset($activeLangs[$tmplang])) {
				$lang = $tmplang;
				$app->setLanguage($lang);
			} else {
				setLangCookie($lang);
			}

		} else {
			if (isset($app->request->cookies['_lang'])) {
				$tmplang = $app->request->cookies['_lang']->value;
				if (isset($activeLangs[$tmplang])) {
					$lang = $tmplang;
					$app->setLanguage($lang);
				} else {
					setLangCookie($lang);
				}
			}
		}
	}

	Lang::getActiveLangs(false, true);
}

function setLangCookie($lang) {
	if (isset(Yii::app()->request->cookies['_lang']) && Yii::app()->request->cookies['_lang']->value == $lang) {
		return true;
	}
	Yii::app()->user->setState('_lang', $lang);
	$cookie = new CHttpCookie('_lang', $lang);
	$cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
	Yii::app()->request->cookies['_lang'] = $cookie;
}

function setCurrency() {
	if (isset($_GET['currency'])) {
		setCurrencyCookie(CHtml::encode($_GET['currency']));
	}

	// Админ деактивирует валюту, а у пользователя есть кука с уже деактивированной валютой.
	// Надо сбросить ему куку.
	if (isset(Yii::app()->request->cookies['_currency'])) {
		$charCode = Yii::app()->request->cookies['_currency']->value;
		$activeCurrency = Currency::getActiveCurrency();

		if (!isset($activeCurrency[$charCode])) {
			setCurrencyCookie(Currency::getDefaultCurrencyModel()->char_code);
		}
	}
}

function setCurrencyCookie($charCode) {
	$cookie = new CHttpCookie('_currency', $charCode);
	$cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
	Yii::app()->request->cookies['_currency'] = $cookie;
}

function param($name, $default = null) {
	if (isset(Yii::app()->params[$name])) {
		return Yii::app()->params[$name];
	} else {
		return $default;
	}
}

function tt($message, $module = null, $lang = NULL) {
	if ($module === null) {
		if (Yii::app()->controller->module) {
			return Yii::t('module_' . Yii::app()->controller->module->id, $message, array(), NULL, $lang);
		}
		return Yii::t(TranslateMessage::DEFAULT_CATEGORY, $message, array(), NULL, $lang);
	}
	if ($module == TranslateMessage::DEFAULT_CATEGORY) {
		return Yii::t(TranslateMessage::DEFAULT_CATEGORY, $message, array(), NULL, $lang);
	}
	return Yii::t('module_' . $module, $message, array(), NULL, $lang);
}

function tc($message) {
	return Yii::t(TranslateMessage::DEFAULT_CATEGORY, $message);
}

function isActive($string) {
	$menu_active = Yii::app()->user->getState('menu_active');
	if ($menu_active == $string) {
		return true;
	} elseif (!$menu_active) {
		if (isset(Yii::app()->controller->module->id) && Yii::app()->controller->module->id == $string) {
			return true;
		}
	}
	return false;
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		if ($objects && is_array($objects)) {
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") {
						rrmdir($dir . "/" . $object);
					} else {
						@unlink($dir . "/" . $object);
					}
				}
			}
		}
		reset($objects);
		@rmdir($dir);
	}
}

class oreInstall {
	static $isInstalled = null;
	public static function isInstalled(){
		if(self::$isInstalled === null){
			self::$isInstalled = file_exists(ALREADY_INSTALL_FILE);
		}
		return self::$isInstalled;
	}
}

function issetModule($module, $raw = false) {
	if(!oreInstall::isInstalled()){
		$raw = true;
	}
	if(!$raw){
		$modules = ConfigurationModel::getModulesList();
		if(in_array($module, $modules)){
			if(!param('module_enabled_'.$module)){
				return false;
			}
		}
	}

	if (is_array($module)) {
		foreach ($module as $module_name) {
			if (!isset(Yii::app()->modules[$module_name])) {
				return false;
			}
		}
		return true;
	}
	return isset(Yii::app()->modules[$module]);
}

function deb($mVal) {
	CVarDumper::dump($mVal, 10, true);
}

function logs($mVal) {
	$file = fopen(ROOT_PATH . '/uploads/logs.txt', 'a+');
	$sLogs = date("d.m.y H:i : ") . var_export($mVal, true) . "\n";
	fwrite($file, $sLogs);
	fclose($file);
}

function throw404() {
	throw new CHttpException(404, 'The requested page does not exist.');
}

function showMessage($messageTitle, $messageText, $breadcrumb = '', $isEnd = true) {
	Yii::app()->controller->render('//site/message', array('breadcrumb' => $breadcrumb,
		'messageTitle' => $messageTitle,
		'messageText' => $messageText));

	if ($isEnd) {
		Yii::app()->end();
	}
}

function modelName() {
	return Yii::app()->controller->id;
}

function toBytes($str) {
	$val = trim($str);
	$last = strtolower($str[strlen($str) - 1]);
	switch ($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}

function getReq($field, $default = '') {
	return isset($_REQUEST[$field]) ? $_REQUEST[$field] : $default;
}

function demo(){
	if(defined('IS_DEMO') && IS_DEMO){
		return true;
	} else {
		return false;
	}
}

function getGA(){
	if(demo() && defined('GA_CODE')){
		return '<script type="text/javascript">'.GA_CODE.'</script>';
	} else {
		return '';
	}
}

function getJivo(){
	if(demo() && defined('JIVO_CODE')){
		return '<script type="text/javascript">'.JIVO_CODE.'</script>';
	} else {
		return '';
	}
}

function isFree(){
	if(defined('IS_FREE') && IS_FREE){
		return true;
	} else {
		return false;
	}
}

function formatBytes($size, $precision = 2) {
	$base = log($size) / log(1024);
	$suffixes = array('', 'k', 'M', 'G', 'T');

	return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}

/**
 *Create a new folder with given permissions
 * @param string $newdir - the name for new folder
 * @param string $rights - permission to be set on folder - default 0777
 * return void
 */
function newFolder($newdir, $rights=0777) {
	$old_mask = umask(0);
	if(!file_exists($newdir)){
		if(!mkdir($newdir, $rights, true)){
			umask($old_mask);
			return false;
		} else {
			umask($old_mask);
			return true;
		}
	} else {
		umask($old_mask);
		return true;
	}
}

/**
 * Remove the directory and its content (all files and subdirectories).
 * @param string $dir the directory name
 * return void
 */
function rmrf($dir) {
	$rmDirs = glob($dir);

	if (is_array($rmDirs) && count($rmDirs)) {
		foreach ($rmDirs as $file)
		{
			if (is_dir($file)) {
				rmrf("$file/*");
				rmdir($file);
			} else {
				@unlink($file);
			}
		}
	}
}

/**
 *Remove a file from a directory
 * @param string $dir - the directory name
 * @param string $file - the file name
 */
function deleteFile($dir, $file) {
	$dfile = $dir.$file;
	if(file_exists($dfile))
		return @unlink($dfile);
	return true;
}

/**
 * return string
 * Remove extension of a given file.
 * @param string $filename - the file name
 */
function removeExtension($fileName) {
	$ext = strrchr($fileName, '.');
	if($ext !== false)
		$fileName = substr($fileName, 0, -strlen($ext));
	return $fileName;
}