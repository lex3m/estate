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

class Booking extends CFormModel {
	public $apartment_id;
	public $user_id;
	public $date_start;
	public $date_end;
	public $time_in;
	public $time_out;
	public $status;

	public $username;
	public $comment;
	public $useremail;
	public $useremailSearch;
	public $tostatus;
	public $ownerEmail;

	public $phone;
	public $email;
	public $dateCreated;
	public $password;

	public $time_inVal;
	public $time_outVal;

	public $activatekey;
	public $activateLink;

	public $verifyCode;

    public $type;

	public static function getYiiDateFormat() {
		$return = 'MM/dd/yyyy';
		if (Yii::app()->language == 'ru') {
			$return = 'dd.MM.yyyy';
		}
		return $return;
	}

	public function rules() {
		return array(
			array('date_start, date_end, time_in, time_out, ' . (Yii::app()->user->isGuest ? 'useremail, phone, username' : ''), 'required', 'on' => 'bookingform'),
			array('status, time_in, time_out', 'numerical', 'integerOnly' => true),
			array('useremail, username, comment, phone','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('useremail', 'email'),
			array('date_start, date_end', 'date', 'format' => self::getYiiDateFormat(), 'on' => 'bookingform'),
			array('date_start, date_end', 'myDateValidator', 'on' => 'bookingform'),
			array('useremail', 'myUserEmailValidator', 'on' => 'bookingform'),
			array('useremail, username', 'length', 'max' => 128),
			array('comment', 'length', 'max' => 1024),
			array('phone', 'required'),
			array('date_start, date_end, date_created, status, useremailSearch, apartment_id, id', 'safe', 'on' => 'search'),

			array('verifyCode', (Yii::app()->user->isGuest) ? 'required' : 'safe'),
			array('verifyCode', 'captcha', 'allowEmpty'=> !(Yii::app()->user->isGuest)),
		);
	}

	public function myUserEmailValidator() {
		if (Yii::app()->user->isGuest) {
			$model = User::model()->findByAttributes(array('email' => $this->useremail));
			if ($model) {
				$this->addError('useremail',
					Yii::t('module_booking', 'User with such e-mail already registered. Please <a title="Login" href="{n}">login</a> and try again.',
						Yii::app()->createUrl('/site/login')));
			}
		}
	}

	public function myDateValidator($param) {
		$dateStart = CDateTimeParser::parse($this->date_start, self::getYiiDateFormat()); // format to unix timestamp
		$dateEnd = CDateTimeParser::parse($this->date_end, self::getYiiDateFormat()); // format to unix timestamp

		if ($param == 'date_start' && $dateStart < CDateTimeParser::parse(date('Y-m-d'), 'yyyy-MM-dd')) {
			$this->addError('date_start', tt('Wrong check-in date', 'booking'));
		}
		if ($param == 'date_end' && $dateEnd <= $dateStart) {
			$this->addError('date_end', tt('Wrong check-out date', 'booking'));
		}

		if(issetModule('bookingcalendar')) {
			$result = Yii::app()->db->createCommand()
				->select('id')
				->from('{{booking_calendar}}')
				->where('apartment_id = "'.$this->apartment_id.'" AND status = "'.Bookingcalendar::STATUS_BUSY.'" AND
						UNIX_TIMESTAMP(date_start) > "'.$dateStart.'" AND UNIX_TIMESTAMP(date_end) < "'.$dateEnd.'"')
				->queryScalar();

			if ($param == 'date_start' && $result) {
				$this->addError('date', tt('You chose dates in the range of which there are busy days', 'bookingcalendar'));
			}
		}
	}

	public function attributeLabels() {
		return array(
			'date_start' => tt('Check-in date', 'booking'),
			'date_end' => tt('Check-out date', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'time_in' => tt('Check-in time', 'booking'),
			'time_out' => tt('Check-out time', 'booking'),
			'comment' => tt('Comment', 'booking'),
			'username' => tt('Your name', 'booking'),
			'status' => tt('Status', 'booking'),
			'useremail' => Yii::t('common', 'E-mail'),
			'useremailSearch' => tt('User e-mail', 'booking'),
			'sum' => tt('Booking price', 'booking'),
			'date_created' => tt('Creation date', 'booking'),
			'dateCreated' => tt('Creation date', 'booking'),
			'apartment_id' => tt('Apartment ID', 'booking'),
			'id' => tt('ID', 'apartments'),
			'phone' => Yii::t('common', 'Your phone number'),
			'verifyCode' => tc('Verify Code'),
		);
	}

	public static function getDate($mysqlDate, $full = 0) {
		if (!$full) {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd');
		}
		else {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd hh:mm:ss');
		}
		return Yii::app()->dateFormatter->format(self::getYiiDateFormat(), $date);
	}

	public static function getJsDateFormat() {
		$dateFormat = 'dd.mm.yy';
		if (Yii::app()->language != 'ru') {
			$dateFormat = 'mm/dd/yy';
		}
		return $dateFormat;
	}
}