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

class ApartmentObjType extends ParentModel {
	public $iconsMapPath = 'uploads/iconsmap';

	public $supportExt = 'jpg, png, gif';
	public $fileMaxSize = 2097152; /* 1024 * 1024 * 2 - 2 MB */
	public $iconUpload;

	const MAP_ICON_MAX_HEIGHT = 37;
	const MAP_ICON_MAX_WIDTH = 32;

	public function init() {
		$fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
		$fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));

		$this->fileMaxSize = min($fileMaxSize);
		parent ::init();
	}

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() 	{
		return '{{apartment_obj_type}}';
	}

	public function rules()	{
		return array(
   			array('name', 'i18nRequired'),
			array(
				'icon_file', 'file',
				'types' => "{$this->supportExt}",
				'maxSize' => $this->fileMaxSize,
				'tooLarge' => Yii::t('module_slider', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $this->fileMaxSize)),
				'allowEmpty' => true
			),
			array('sorter', 'numerical', 'integerOnly'=>true),
   			array('name', 'i18nLength', 'max'=>255),

   			array('id, sorter, date_updated', 'safe', 'on'=>'search'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function relations()
    {
        return array(
            'apartment'=>array(self::HAS_MANY, 'Apartment', 'obj_type_id'),
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
			'icon_file' => tt('icon_file_maps'),
		);
	}

    public function search(){
        $criteria=new CDbCriteria;

        $criteria->compare('name_'.Yii::app()->language, $this->{'name_'.Yii::app()->language}, true);
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

    public function afterSave() {
        if($this->isNewRecord){
            if(issetModule('formdesigner')){
                Yii::import('application.modules.formdesigner.models.*');
                $forms = FormDesigner::model()->findAll();
                foreach($forms as $form){
                    $formType = new FormDesignerObjType();
                    $formType->formdesigner_id = $form->id;
                    $formType->obj_type_id = $this->id;
                    $formType->save();
                }
            }

            $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT));
            foreach($searchFields as $field){
                $newSearch = new SearchFormModel();
                $newSearch->attributes = $field->attributes;
                $newSearch->obj_type_id = $this->id;
                $newSearch->save();
            }
        }

        return parent::afterSave();
    }

    public function beforeDelete(){
	    if($this->model()->count() <= 1){
		    echo 1;
		    return false;
	    }

	    if ($this->icon_file) {
		    $iconPath = Yii::getPathOfAlias('webroot').'/'.$this->model()->iconsMapPath.'/'.$this->icon_file;
		    if (file_exists($iconPath))
			    @unlink($iconPath);
	    }

        $db = Yii::app()->db;

        $sql = "SELECT id FROM ".$this->tableName()." WHERE id != ".$this->id." ORDER BY sorter ASC";
        $type_id = (int) $db->createCommand($sql)->queryScalar();

        $sql = "UPDATE {{apartment}} SET obj_type_id={$type_id}, active=0 WHERE obj_type_id=".$this->id;
        $db->createCommand($sql)->execute();

        if(issetModule('formdesigner')){
            $sql = "DELETE FROM {{formdesigner_obj_type}} WHERE obj_type_id=".$this->id;
            $db->createCommand($sql)->execute();
        }

        $sql = "DELETE FROM {{search_form}} WHERE obj_type_id=".$this->id;
        $db->createCommand($sql)->execute();

        return parent::beforeDelete();
    }

    private static $_cacheList;

    public static function getList(){
        if(empty(self::$_cacheList)){
            self::$_cacheList = CHtml::listData(ApartmentObjType::model()->findAll(), 'id', 'name');
        }

        return self::$_cacheList;
    }

    public static function getNameById($id) {
        $list = self::getList();

        return isset($list[$id]) ? $list[$id] : '';
    }

    public function getUrlIcon() {
        if($this->icon_file){
            $iconUrl = Yii::app()->getBaseUrl().'/'.$this->iconsMapPath.'/'.$this->icon_file;
        }else{
            $iconUrl = Yii::app()->getBaseUrl()."/images/house.png";
        }
        return $iconUrl;
    }

    public function getImageIcon() {
        return CHtml::image($this->getUrlIcon());
    }
}