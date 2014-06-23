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

class SocialauthModel extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{socialauth}}';
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

	public function rules() {
		return array(
			array('name, value', 'required'),
			array('name, value', 'length', 'max' => 255),
		);
	}

    public function getTitle(){
        return tt($this->name);
    }

	public function attributeLabels() {
		return array(
			//'title_ru' => SocialauthModule::t('Name'),
			'value' => SocialauthModule::t('Value'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('value', $this->value, true);

        $section_filter = Yii::app()->request->getQuery('section_filter', 'all');

        if($section_filter != 'all'){
            $criteria->compare('section', $section_filter);
        }

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'section',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

    public static function getAdminValue($model){
        if($model->type == 'bool') {
            $url = Yii::app()->controller->createUrl("activate",
                array(
                    'id' => $model->id,
                    'action' => ($model->value == 1 ? 'deactivate' : 'activate'),
                ));
            $img = CHtml::image(
                Yii::app()->request->baseUrl.'/images/'.($model->value ? '' : 'in').'active.png',
                Yii::t('common', $model->value ? 'Inactive' : 'Active'),
                array('title' => Yii::t('common', $model->value ? 'Deactivate' : 'Activate'))
            );

            $options = array(
                'onclick' => 'ajaxSetStatus(this, "socialauth-table"); return false;',
            );

            return '<div align="left">'.CHtml::link($img, $url, $options).'</div>';
        } else {
			if(demo()){
				return tc('Hidden in demo mode');
			} else {
            	return utf8_substr($model->value, 0, 55);
			}
        }
    }

    public static function getVisible($type){
        return $type == 'text';
    }

	public static function getSocialParamValue($param = '') {
		if ($param) {
			return Yii::app()->db->createCommand()
					->select('value')
					->from('{{socialauth}}')
					->where('name = "'.$param.'"')
					->queryScalar();
		}
	}
}