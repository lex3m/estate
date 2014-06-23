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

class ApartmentsComplainReason extends ParentModel {
	private static $_allReasons;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_complain_reason}}';
	}

	public function rules() {
		return array(
			array('name', 'i18nRequired'),
			array('sorter', 'numerical', 'integerOnly' => true),
			array('name', 'i18nLength', 'max' => 255),
			array('id, sorter, date_updated', 'safe', 'on' => 'search'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

	public function i18nFields() {
		return array(
			'name' => 'varchar(255) not null',
		);
	}

	public function getName() {
		return $this->getStrByLang('name');
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'name' => tt('Name'),
			'sorter' => 'Sorter',
			'date_updated' => 'Date Updated',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$tmp = 'name_' . Yii::app()->language;
		$criteria->compare($tmp, $this->$tmp, true);
		$criteria->order = 'sorter ASC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function beforeSave() {
		if ($this->isNewRecord) {
			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter + 1;
		}

		return parent::beforeSave();
	}

	public static function getAllReasons($val = null) {
		if (self::$_allReasons === null) {
			$sql = 'SELECT name_' . Yii::app()->language . ' AS name, id
                    FROM {{apartment_complain_reason}}
                    ORDER BY sorter';

			$results = Yii::app()->db->createCommand($sql)->queryAll();

			self::$_allReasons = CHtml::listData($results, 'id', 'name');
		}
		if ($val && array_key_exists($val, self::$_allReasons))
			return self::$_allReasons[$val];

		return self::$_allReasons;
	}
}