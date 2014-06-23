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

class SendMailForm extends CFormModel {
	public $senderName;
	public $senderEmail;
	public $senderPhone;
	public $body;
	public $verifyCode;

	public $ownerId;
	public $ownerEmail;
	public $ownerName;

	public $apartmentUrl;

	public function rules()	{
		return array(
			array('senderName, senderEmail, body', 'required'),
			array('senderEmail', 'email'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest),
			array('senderPhone', 'safe'),
			array('senderName, senderEmail', 'length', 'max' => 128),
			array('senderPhone', 'length', 'max' => 16, 'min' => 5),
			array('body', 'length', 'max' => 1024),
		);
	}

	public function attributeLabels() {
		return array(
			'senderName' => tt('user_request_name', 'apartments'),
			'senderEmail' => tt('user_request_email', 'apartments'),
			'senderPhone' => tt('user_request_phone', 'apartments'),
			'body' => tt('user_request_message', 'apartments'),
			'verifyCode' => tt('user_request_ver_code', 'apartments'),
		);
	}
}