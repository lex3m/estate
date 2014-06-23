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

class Bookingtable extends ParentModel {
	const STATUS_NEW = 0;
	const STATUS_VIEWED = 1;
	const STATUS_CONFIRM = 2;
	const STATUS_NOT_CONFIRM = 3;

	private static $_statuses_arr;

	public $dateStart = array();
	public $dateEnd = array();
	public $status = array();

	public $dateStartDb = array();
	public $dateEndDb = array();
	public $statusDb = array();

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{booking_table}}';
	}

	public function rules() {
		return array(
			array('date_start, date_end', 'required'),
			array('apartment_id', 'required', 'on'=>'insert'),
			array('active', 'numerical', 'min' => 1),
			array('username, email', 'length', 'max' => 128),
			array('phone', 'length', 'max' => 15),
			array('username, email, comment, phone','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('id, active, apartment_id, username, email, phone, date_start, date_end, time_in, time_out, comment, date_created', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		$relation = array();
		$relation['apartment'] = array(self::BELONGS_TO, 'Apartment', 'apartment_id'/*, 'order'=>'apartment.id DESC'*/);
		$relation['timein'] = array(self::BELONGS_TO, 'TimesIn', 'time_in');
		$relation['timeout'] = array(self::BELONGS_TO, 'TimesOut', 'time_out');
		return $relation;
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'active' => tc('Status'),
			'date_start' => tt('Check-in date', 'booking'),
			'date_end' => tt('Check-out date', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'time_in' => tt('Check-in time', 'booking'),
			'time_out' => tt('Check-out time', 'booking'),
			'comment' => tt('Comment', 'booking'),
			'username' => tt('User name', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'date_created' => tt('Creation date', 'booking'),
			'dateCreated' => tt('Creation date', 'booking'),
			'apartment_id' => tt('Apartment ID', 'booking'),
			'id' => tt('ID', 'apartments'),
			'phone' => Yii::t('common', 'Phone number'),
			'verifyCode' => tc('Verify Code'),
		);
	}

	public static function getAllStatuses(){
		return array(
			self::STATUS_NEW => tt('Status new', 'booking'),
			self::STATUS_VIEWED => tt('Status view', 'booking'),
			self::STATUS_CONFIRM => tt('Status confirm', 'booking'),
			self::STATUS_NOT_CONFIRM => tt('Status not confirm', 'booking'),
		);
    }

	public static function getStatus($status){
        if(!isset(self::$_statuses_arr)){
            self::$_statuses_arr = self::getAllStatuses(NULL, true);
        }
        return self::$_statuses_arr[$status];
    }

	public function search($isUserView = false){
		$criteria = new CDbCriteria;

		if ($isUserView) {
			$criteria->addCondition('apartment.owner_id = :owner_id');
			$criteria->params[':owner_id'] = Yii::app()->user->id;

			$criteria->with['apartment'] = array(
				'select' => 'apartment.owner_id',
				'together' => true
			);
		}

		$criteria->compare($this->getTableAlias().'.id', $this->id);
		$criteria->compare($this->getTableAlias().'.active', $this->active);
		$criteria->compare($this->getTableAlias().'.apartment_id', $this->apartment_id, true);
		$criteria->compare($this->getTableAlias().'.username', $this->username, true);
		$criteria->compare($this->getTableAlias().'.email', $this->email, true);
		$criteria->compare($this->getTableAlias().'.phone', $this->phone, true);
		$criteria->compare($this->getTableAlias().'.date_start', $this->date_start, true);
		$criteria->compare($this->getTableAlias().'.date_end', $this->date_end, true);
		$criteria->compare($this->getTableAlias().'.time_in', $this->time_in);
		$criteria->compare($this->getTableAlias().'.time_out', $this->time_out);
		$criteria->compare($this->getTableAlias().'.comment', $this->comment, true);
		$criteria->compare($this->getTableAlias().'.date_created', $this->date_created, true);

		$criteria->order = $this->getTableAlias().'.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function isUserAd($apartmentId = null, $ownerId = null) {
		if ($apartmentId && $ownerId) {
			if (Apartment::model()->findByAttributes(array('id' => $apartmentId, 'owner_id' => $ownerId)))
				return true;
			return false;

		}
		return false;
	}

	public static function addRecord(Booking $booking) {
		$dateStart = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($booking->date_start, Booking::getYiiDateFormat()));
		$dateEnd = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($booking->date_end, Booking::getYiiDateFormat()));

		$model = new Bookingtable;
		$model->active = self::STATUS_NEW;
		$model->apartment_id = $booking->apartment_id;
		$model->username = $booking->username;
		$model->email = $booking->useremail;
		$model->phone = $booking->phone;
		$model->date_start = $dateStart;
		$model->date_end = $dateEnd;
		$model->time_in = $booking->time_in;
		$model->time_out = $booking->time_out;
		$model->comment = $booking->comment;

		$model->save(false);
	}

	public static function getCountNew($isUserView = false) {
		if ($isUserView) {
            $sql = "SELECT COUNT(b.id) FROM {{booking_table}} b "
                ." INNER JOIN {{apartment}} a ON b.apartment_id = a.id"
                ." WHERE b.active = ".self::STATUS_NEW . " AND a.owner_id = " . Yii::app()->user->id;
		} else {
            $sql = "SELECT COUNT(id) FROM {{booking_table}} WHERE active = ".self::STATUS_NEW;
        }

        return (int) Yii::app()->db->createCommand($sql)->queryScalar();
	}
}
