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

class WindowTo extends ParentModel {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_window_to}}';
	}

    public function rules() {
   		return array(
   			array('title', 'i18nLength', 'max' => 255),
   			array('title', 'i18nRequired'),
   			array('id', 'safe', 'on' => 'search'),
			array($this->getI18nFieldSafe(), 'safe'),
   		);
   	}

   public function i18nFields(){
       return array(
           'title' => 'varchar(255) not null',
       );
   }

	public function relations() {
		return array(
		);
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => null,
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'title' => tt('Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('title_'.Yii::app()->language, $this->{'title_'.Yii::app()->language}, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function getTitle() {
        $title = 'title_' . Yii::app()->language;
        return $this->$title;
	}

	public function afterDelete(){
		$sql = 'UPDATE {{apartment}} SET window_to="0" WHERE window_to="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		return parent::afterDelete();
	}

	static function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}
}