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

class Reference extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_reference}}';
	}

	public function rules() {
		return array(
			array('reference_id, reference_value_id, apartment_id', 'required'),
			array('reference_id, reference_value_id, apartment_id', 'numerical', 'integerOnly' => true),
			array('id, reference_id, reference_value_id, apartment_id', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {

		Yii::app()->getModule('referencecategories');
		Yii::app()->getModule('referencevalues');

		return array(
			'category' => array(self::HAS_ONE, 'ReferenceCategories', '', 'on' => 'reference.reference_id = category.id'),
			'value' => array(self::HAS_ONE, 'ReferenceValues', '', 'on' => 'reference.reference_id = category.id'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'reference_id' => 'Reference',
			'reference_value_id' => 'Reference Value',
			'apartment_id' => 'Apartment',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('reference_id', $this->reference_id);
		$criteria->compare('reference_value_id', $this->reference_value_id);
		$criteria->compare('apartment_id', $this->apartment_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

}