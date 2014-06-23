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

class InstallForm extends CFormModel {

    public $agreeLicense;

    public $dbHost = 'localhost';
    public $dbPort = '3306';
    public $dbUser = 'root';
    public $dbPass;
	public $dbName;
	public $dbPrefix = 'ore_';
	public $language;

	public $adminName;
	public $adminPass;
	public $adminEmail;

    public function init() {
		$this->language = Yii::app()->language;
        return parent::init();
    }

	public function getLangs(){
		return array(
			'en' => 'English / Английский / Englisch',
			'ru' => 'Russian / Русский / Russisch',
			'de' => 'German / Немецкий / Deutsch',
		);
	}

	public function rules()	{
		return array(
			array('dbUser, dbHost, dbName, adminPass, adminEmail, adminName, dbPrefix', 'required'),
			array('agreeLicense', 'required', 'requiredValue' => true, 'message'=> tFile::getT('module_install', 'You should agree with "The license agreement"')),
			array('adminEmail', 'email'),
			array('dbUser, dbPass, dbName', 'length', 'max' => 30),
			array('dbHost', 'length', 'max' => 50),
			array('adminPass', 'length', 'max' => 20, 'min' => 6),
            array('dbPort', 'length', 'max' => 5),
			array('dbPort', 'numerical', 'allowEmpty' => true, 'integerOnly' => true),
			array('dbPrefix', 'length', 'max' => 7, 'min' => 1),
			array('dbPrefix', 'match', 'pattern' => '#^[a-zA-Z0-9_]{1,7}$#', 'message'=> tFile::getT('module_install', 'It is allowed to use the characters "a-zA-Z0-9_" without spaces')),
			array('dbPrefix, dbPort', 'safe'),
			array('language', 'in' ,'range' => array('en', 'ru', 'de'), 'allowEmpty' => false),
		);
	}

	public function attributeLabels() {
		if(isFree()){
			$lang = tFile::getT('module_install', 'Site language');
		} else {
			$lang = tFile::getT('module_install', 'Preferred site language');
		}

		return array(
            'agreeLicense' => tFile::getT('module_install', 'I agree with').' ' . CHtml::link(tFile::getT('module_install', 'License agreement'), '#',
                                                            array('onclick'=>'$("#licensewidget").dialog("open"); return false;')).
															(($this->language == 'de') ? ' zu' : ''),
            'dbHost' => tFile::getT('module_install', 'Database server'),
            'dbPort' => tFile::getT('module_install', 'Database port'),
            'dbUser' => tFile::getT('module_install', 'Database user name'),
            'dbPass' => tFile::getT('module_install', 'Database user password'),
            'dbName' => tFile::getT('module_install', 'Database name'),
            'dbPrefix' => tFile::getT('module_install', 'Prefix for tables'),
            'adminName' => tFile::getT('module_install', 'Administrator name'),
            'adminPass' => tFile::getT('module_install', 'Administrator password'),
            'adminEmail' => tFile::getT('module_install', 'Administrator email'),
			'language' => $lang,
		);
	}
}