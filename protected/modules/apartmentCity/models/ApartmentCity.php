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

class ApartmentCity extends ParentModel
{
    private static $_activeCity;
    private static $_allCity;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_city}}';
	}

	public function rules()	{
		return array(
			array('name', 'i18nRequired'),
			array('sorter', 'numerical', 'integerOnly'=>true),
			array('name', 'i18nLength', 'max'=>255),
			array('id, sorter, date_updated', 'safe', 'on'=>'search'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
        return array(
            'name' => 'varchar(255) not null',
        );
    }

    public function getName(){
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

    public function search(){
        $criteria=new CDbCriteria;

        $tmp = 'name_'.Yii::app()->language;
        $criteria->compare($tmp, $this->$tmp, true);
        $criteria->order = 'sorter ASC';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>param('adminPaginationPageSize', 20),
            ),
        ));
    }

    public function beforeSave(){
        if($this->isNewRecord){
            $maxSorter = Yii::app()->db->createCommand()
                ->select('MAX(sorter) as maxSorter')
                ->from($this->tableName())
                ->queryScalar();
            $this->sorter = $maxSorter+1;
        }

        return parent::beforeSave();
    }

    public static function getActiveCity(){
        if(self::$_activeCity === null){
            $ownerActiveCond = '';

             if (param('useUserads'))
                $ownerActiveCond = ' AND ap.owner_active = '.Apartment::STATUS_ACTIVE.' ';

            $sql = 'SELECT ac.name_'.Yii::app()->language.' AS name, ac.id AS id
                    FROM {{apartment}} ap, {{apartment_city}} ac
                    WHERE ac.id = ap.city_id
                    AND ap.type IN ('.implode(',', Apartment::availableApTypesIds()).')
                    AND ap.active = '.Apartment::STATUS_ACTIVE.' '.$ownerActiveCond.'
                    ORDER BY ac.sorter';

            $results = Yii::app()->db->createCommand($sql)->queryAll();

            self::$_activeCity = CHtml::listData($results, 'id', 'name');
        }
        return self::$_activeCity;
    }

    public static function getAllCity(){
        if(self::$_allCity === null){
	        $sql = 'SELECT name_'.Yii::app()->language.' AS name, id
                    FROM {{apartment_city}}
                    ORDER BY sorter';

            $results = Yii::app()->db->createCommand($sql)->queryAll();

            self::$_allCity = CHtml::listData($results, 'id', 'name');
        }
        return self::$_allCity;
    }

    public function beforeDelete(){
        if($this->model()->count() <= 1){
            return false;
        }

        $sql = "UPDATE {{apartment}} SET city_id=0, active=0 WHERE city_id=".$this->id;
        Yii::app()->db->createCommand($sql)->execute();

        return parent::beforeDelete();
    }
}