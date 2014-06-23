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

class Service extends ParentModel {
	const SERVICE_ID = 1;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{service}}';
	}

	public function rules() {
		return array(
			array('page, is_offline, allow_ip', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'page' => tt('Page', 'service'),
			'is_offline' => tt('Closed_maintenance', 'service'),
			'allow_ip' => tt('Allow_ip', 'service'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('page', $this->page, true);
		$criteria->compare('is_offline', $this->is_offline, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'date_created DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}
}