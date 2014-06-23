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

class ReferenceValues extends ParentModel{

	public $oldRefId = 0;

	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{apartment_reference_values}}';
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

	public function rules(){
		return array(
			array('reference_category_id', 'required'),
			array('title', 'i18nRequired'),
			array('reference_category_id, sorter, for_sale, for_rent', 'numerical', 'integerOnly'=>true),
			array('title', 'i18nLength', 'max'=>255),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
        return array(
            'title' => 'varchar(255) not null',
		);
	}

	public function relations(){
		Yii::app()->getModule('referencecategories');
		return array(
			'category' => array(self::BELONGS_TO, 'ReferenceCategories', 'reference_category_id',
					'order' => 'category.sorter ASC',
					'select' => 'category.title_'.Yii::app()->language,
				),
		);
	}

	public function attributeLabels()
	{
		return array(
			'title' => tt('Reference value'),
			'reference_category_id' => tt('Reference category'),
            'for_sale' => tt('For sale'),
            'for_rent' => tt('For rent')
		);
	}

	public function search(){
		$criteria=new CDbCriteria;

		$criteria->compare($this->getTableAlias().'.title_'.Yii::app()->language,$this->{'title_'.Yii::app()->language},true);

		$criteria->with = array('category');
		$criteria->order = 'category.sorter ASC, t.sorter ASC';

        if(isset($_GET['ReferenceValues']['category_filter']) && $_GET['ReferenceValues']['category_filter']){
            $criteria->compare('reference_category_id', $_GET['ReferenceValues']['category_filter']);
            //$_GET['ReferenceValues_page'] = 1;
        }
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public function afterFind(){
		$this->oldRefId = $this->reference_category_id;
		parent::afterFind();
	}

	public function beforeSave(){
		if($this->reference_category_id != $this->oldRefId && $this->oldRefId != 0 && !$this->isNewRecord){
			$sql = 'UPDATE '.$this->tableName().' SET sorter=sorter-1 WHERE sorter > "'.$this->sorter.'" AND reference_category_id="'.$this->oldRefId.'"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		if($this->isNewRecord || ($this->reference_category_id != $this->oldRefId && $this->oldRefId != 0)){
			$sql = 'SELECT MAX(sorter) FROM '.$this->tableName().' WHERE reference_category_id = "'.$this->reference_category_id.'"';
			$maxSorter = Yii::app()->db
				->createCommand($sql)
				->queryScalar();
			$this->sorter = $maxSorter+1;
		}

		return parent::beforeSave();
	}

	public function afterDelete(){
		$sql = 'DELETE FROM {{apartment_reference}} WHERE reference_value_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		return parent::afterDelete();
	}

    public static function returnForStatusHtml($data, $for_field, $tableId = '', $onclick = 1, $ignore = 0){
        if($ignore && $data->id == $ignore){
            return '';
        }
        $url = Yii::app()->controller->createUrl("activate",
            array(
                'id' => $data->id,
                'action' => ($data->$for_field == 1 ? 'deactivate' : 'activate'),
                'field' => $for_field
            ));
        $img = CHtml::image(
            Yii::app()->request->baseUrl.'/images/'.($data->$for_field ? '' : 'in').'active.png',
            Yii::t('common', $data->$for_field ? 'Inactive' : 'Active'),
            array('title' => Yii::t('common', $data->$for_field ? 'Deactivate' : 'Activate'))
        );
        $options = array();
        if($onclick){
            $options = array(
                'onclick' => 'ajaxSetStatus(this, "'.$tableId.'"); return false;',
            );
        }
        return '<div align="center">'.CHtml::link($img, $url, $options).'</div>';
    }

	public static function getDependency(){
        return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment_reference_values}}');
    }

}