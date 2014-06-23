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

class ContactForm extends CFormModel {
	public $name;
	public $email;
	public $body;
	public $verifyCode;
	public $phone;
	public $useremail;
	public $username;

	public function rules()	{
		return array(
			array('name, email, body', 'required'),
			array('email', 'email'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest),
			array('phone', 'safe'),
			array('name, email', 'length', 'max' => 128),
			array('phone', 'length', 'max' => 16, 'min' => 5),
			array('body', 'length', 'max' => 1024),
		);
	}

	public function attributeLabels() {
		return array(
			'name' => tt('Name', 'contactform'),
			'email' => tt('Email', 'contactform'),
			'phone' => tt('Phone', 'contactform'),
			'body' => tt('Body', 'contactform'),
			'verifyCode' => tt('Verification Code', 'contactform'),
		);
	}
}