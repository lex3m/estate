<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *    version                :    1.8.1
 *    copyright            :    (c) 2014 Monoray
 *    website                :    http://www.monoray.ru/
 *    contact us            :    http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

/**
 * This is the model class for table "{{notifier}}".
 *
 * The followings are the available columns in table '{{notifier}}':
 * @property integer $id
 * @property integer $status
 * @property string $event
 * @property string $subject_ru
 * @property string $subject_en
 * @property string $subject_de
 * @property string $body_ru
 * @property string $body_en
 * @property string $body_de
 */
class NotifierModel extends ParentModel
{
    const STATUS_NO_SEND = 0;
    const STATUS_SEND_ADMIN = 1;
    const STATUS_SEND_USER = 2;
    const STATUS_SEND_ALL = 3;

    public static $_statuses;

    public static function getStatusList(){
        if(!isset(self::$_statuses)){
            self::$_statuses = array(
                self::STATUS_NO_SEND => tt('anyone'),
                self::STATUS_SEND_ADMIN => tt('administrator'),
                self::STATUS_SEND_USER => tt('user'),
                self::STATUS_SEND_ALL => tt('and the user, and the administrator'),
            );
        }

        return self::$_statuses;
    }

    public function getStatusName() {
        self::getStatusList();

        if($this->onlyAdmin){
            return tt('Only admin');
        }
        return isset(self::$_statuses[$this->status]) ? self::$_statuses[$this->status] : '';
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{notifier}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('subject, subject_admin', 'i18nLength', 'max' => 255),
			array('id, status', 'numerical', 'integerOnly'=>true),
			array('event', 'length', 'max'=>50),

            array($this->getI18nFieldSafe(), 'safe'),

			array('id, status, event', 'safe', 'on'=>'search'),
		);
	}

    public function i18nFields(){
        return array(
            'subject' => 'varchar(255) not null',
            'subject_admin' => 'varchar(255) not null',
            'body' => 'text not null',
            'body_admin' => 'text not null',
        );
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

    public function scopes(){
        return array(
            'active' => array(
                'condition' => 'status > 0',
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => tc('Status'),
			'event' => 'Event',
			'subject' => tc('Subject'),
			'body' => tc('Body'),
            'subject_admin' => tc('Subject'),
            'body_admin' => tc('Body'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;


		$criteria->compare('id',$this->id);
		$criteria->compare('status',$this->status);
		$criteria->compare('event',$this->event,true);

        $subjectLang = 'subject_'.Yii::app()->language;
		$criteria->compare($subjectLang,$this->$subjectLang,true);

        $bodyLang = 'subject_'.Yii::app()->language;
		$criteria->compare($bodyLang,$this->$bodyLang,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => param('adminPaginationPageSizeBig', 60),
            ),
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getSubject() {
        return $this->getStrByLang('subject');
    }

    public function canSendAdmin() {
        return in_array($this->status, array(self::STATUS_SEND_ADMIN, self::STATUS_SEND_ALL));
    }

    public function canSendUser() {
        return in_array($this->status, array(self::STATUS_SEND_USER, self::STATUS_SEND_ALL));
    }

    public function getRulesFieldsString($rules, $key = 'admin'){
        if(isset($rules[$key][$this->event]['fields'])){
            $fields = $rules[$key][$this->event]['fields'];
            if(isset($rules[$key][$this->event]['i18nFields'])){
                $fields = CMap::mergeArray($fields, $rules[$key][$this->event]['i18nFields']);
            }

            $fieldsScope = array();
            foreach($fields as $field){
                $fieldsScope[] = '{'.$field.'}';
            }
            $fieldsScope[] = '{fullhost}';
            return tt('The variables are available in this template') . ': ' . implode(', ', $fieldsScope);
        }
    }
}
