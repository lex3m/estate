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
 * This is the model class for table "{{formdesigner}}".
 *
 * The followings are the available columns in table '{{formdesigner}}':
 * @property integer $id
 * @property string $field
 * @property integer $is_i18n
 * @property integer $visible
 */
class FormDesigner extends ParentModel
{
    const TYPE_DEFAULT = 0;
    const TYPE_REFERENCE = 1;
    const TYPE_TEXT = 2;
    const TYPE_TEXT_AREA = 3;
    const TYPE_TEXT_AREA_WS = 4;
    const TYPE_INT = 5;
    const TYPE_FLOAT = 6;

    const RULE_NO_REQUIRED = 0;
    const RULE_REQUIRED = 1;
    const RULE_REQUIRED_NUMERICAL = 2;
    const RULE_NUMERICAL = 3;

    const VIEW_IN_GENERAL = 1;
    const VIEW_IN_EXTENDED = 2;

    public $type;
    public $name;

    const VISIBLE_OWNER_OR_ADMIN = 1;

    const COMPARE_EQUAL = 0;
    const COMPARE_LIKE = 1;
    const COMPARE_FROM = 2;
    const COMPARE_TO = 3;

    public static function getCompareList(){
        return array(
            self::COMPARE_EQUAL => tt('compare equal', 'formeditor'),
            self::COMPARE_LIKE => tt('compare like', 'formeditor'),
            self::COMPARE_FROM => tt('compare from', 'formeditor'),
            self::COMPARE_TO => tt('compare to', 'formeditor'),
        );
    }

    public static function getTypesList(){
        return array(
            self::TYPE_REFERENCE => tt('type reference', 'formeditor'),
            self::TYPE_TEXT => tt('type text', 'formeditor'),
            self::TYPE_TEXT_AREA => tt('type text area', 'formeditor'),
            self::TYPE_INT => tt('type INT', 'formeditor'),
            //self::TYPE_FLOAT => tt('type FLOAT', 'formeditor'),
            //self::TYPE_TEXT_AREA_WS => tt('type text area with wyswig', 'formeditor'),
        );
    }

    public function getTypeName(){
        $list = self::getTypesList();
        return isset($list[$this->type]) ? $list[$this->type] : '?';
    }

    public static function getRulesList(){
        return array(
            self::RULE_NO_REQUIRED => tt('value no required'),
            self::RULE_REQUIRED => tt('value required'),
            self::RULE_REQUIRED_NUMERICAL => tt('value required and must be numerical'),
            self::RULE_NUMERICAL => tt('value must be numerical'),
        );
    }

    public static function getViewInList(){
        return array(
            self::VIEW_IN_GENERAL => tt('Display in general.'),
            self::VIEW_IN_EXTENDED => tt('Display in extended.'),
        );
    }

    public function behaviors(){
        return array(
            'AutoTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => NULL,
                'updateAttribute' => 'date_updated',
            ),
        );
    }

    public $saveObjTypes = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FormDesigner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{formdesigner}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
        $rules = array(
            array('type, is_i18n, visible, reference_id, rules, view_in, compare_type', 'numerical', 'integerOnly'=>true),
            array('tip, label', 'i18nLength', 'max'=>255),
            array('measure_unit', 'length', 'max'=>30),
            array('type', 'checkType'),
            array('compare_type', 'checkCompareType'),
            array($this->getI18nFieldSafe() . ', saveTypes, saveObjTypes, objTypesArray', 'safe'),
        );

        if($this->scenario == 'advanced'){
            $rules[] = array('label', 'i18nRequired');
        }

        return $rules;
	}

    public function checkType(){
        if($this->scenario == 'advanced' && $this->type == self::TYPE_REFERENCE){
            if(!$this->reference_id){
                $this->addError('reference_id', tt('You must select a category of the reference'));
            }else{
                $sql = "SELECT count(id) FROM {{apartment_reference_values}} WHERE reference_category_id=:id";
                $count = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $this->reference_id));
                if(!$count){
                    $this->addError('reference_id', tt('In this category there are no values'));
                }
            }
        }
    }

    public function checkCompareType(){
        if($this->scenario == 'advanced' && $this->type != self::TYPE_INT
            && ($this->rules == self::RULE_NUMERICAL || $this->rules == self::RULE_REQUIRED_NUMERICAL)){
            $this->addError('compare_type', tt('Such comparison is possible only for numeric fields'));
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'objTypes' => array(self::MANY_MANY, 'ApartmentObjType', '{{formdesigner_obj_type}}(formdesigner_id, obj_type_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'field' => tt('Field', 'formdesigner'),
			'is_i18n' => 'Is I18n',
			'visible' => tt('Visible only to the owner, admin', 'formdesigner'),
            'filterObjTypes' => tt('Object type', 'apartments'),
            'objTypesArray' => tt('Show for property types', 'formdesigner'),
            'tip' => tt('Tip', 'formdesigner'),
            'measure_unit' => tt('Measure unit', 'formdesigner'),
            'type' => tt('Type', 'formdesigner'),
            'reference_id' => tt('Reference', 'formdesigner'),
            'rules' => tt('Validation rules for a field', 'formdesigner'),
            'view_in' => tt('Display in', 'formdesigner'),
            'compare_type' => tt('Comparison in the search', 'formdesigner'),
            'label' => tt('Label', 'formdesigner'),
		);
	}

    public function i18nFields(){
        return array(
            'tip' => 'varchar(255) not null',
            'label' => 'varchar(255) not null',
        );
    }

    public function getTip() {
        return $this->getStrByLang('tip');
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('is_i18n',$this->is_i18n);
		$criteria->compare('visible',$this->visible);

		return new CustomActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

    public function getVisibleHtml(){
        $url = Yii::app()->controller->createUrl("visible", array( "id" => $this->id ));

        $img = CHtml::image(
            Yii::app()->request->baseUrl.'/images/'.($this->visible ? '' : 'in').'active.png',

            Yii::t('common', $this->visible ? 'Active' : 'Inactive'),

            array('title' => Yii::t('common', $this->visible ? 'Deactivate' : 'Activate'))
        );

        $options = array(
            'onclick' => 'ajaxSetVisibleForm(this); return false;',
        );

        return '<div align="center">'.CHtml::link($img, $url, $options).'</div>';
    }

    public function getTypesHtml(){

        $objTypesName = array();

        foreach($this->objTypes as $type){
            $objTypesName[] = $type->name;
        }

        $html = '<div align="center">'.implode(', ', $objTypesName).'</div>';
//        $html .= CHtml::link(tc('Configure'),
//            Yii::app()->createUrl('/formdesigner/backend/main/setup', array('id' => $this->id)),
//            array('class' => 'tempModal'));

        return CHtml::tag('div', array('id' => 'form_el_'.$this->id), $html);
    }

    public function visibleForm(){
        $objTypes = array();

        foreach($this->objTypes as $type){
            $objTypes[] = $type->id;
        }

        if(array_intersect($this->filterObjTypes, $objTypes)){
            return true;
        }

        return false;
    }

	public function afterSave() {
		if ($this->scenario == 'save_types') {
			$sql = "DELETE FROM {{formdesigner_obj_type}} WHERE formdesigner_id=:formdesigner_id";
			Yii::app()->db->createCommand($sql)->execute(array(
				':formdesigner_id' => $this->id,
			));
		}

		if($this->saveObjTypes){
			foreach($this->saveObjTypes as $typeID){
				$formDesignerType = new FormDesignerObjType();
				$formDesignerType->formdesigner_id = $this->id;
				$formDesignerType->obj_type_id = $typeID;
				$formDesignerType->save();
			}
		}

		return parent::afterSave();
	}

    public static function getDependency(){
        return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{formdesigner}}');
    }

    private static $_cache;
    private static $_cacheByView;
    private static $_cacheNewFields = array();

    public static function canShow($field, Apartment $apartment) {
        if(!isset(self::$_cache)){
            self::setCache();
        }

        if(!isset(self::$_cache[$field])){
            return true;
        }

        if(self::$_cache[$field]['visible'] == self::VISIBLE_OWNER_OR_ADMIN && !Yii::app()->user->getState('isAdmin')
            && $apartment->owner_id != Yii::app()->user->id){
            return false;
        }

        return in_array($apartment->obj_type_id, self::$_cache[$field]['objTypes']);
    }

    public static function getCacheByView(){
        if(!isset(self::$_cache)){
            self::setCache();
        }

        return self::$_cacheByView;
    }

    private static function setCache(){
        $fields = FormDesigner::model()
            ->cache(param('cachingTime', 1209600), self::getDependency())
            ->with(array('objTypes'))
            ->findAll();

        /** @var $field FormDesigner */
        foreach($fields as $field){
            if($field->type != self::TYPE_DEFAULT){
                self::$_cacheNewFields[] = $field;

                if($field->view_in){
                    self::$_cacheByView[$field->view_in][] = $field;
                }
            }

            self::$_cache[$field->field]['visible'] = $field->visible;
            self::$_cache[$field->field]['tip'] = $field->getTip();
            self::$_cache[$field->field]['label'] = $field->getLabel();

            self::$_cache[$field->field]['objTypes'] = array();
            foreach($field->objTypes as $type){
                self::$_cache[$field->field]['objTypes'][] = $type->id;
            }
        }
    }

    public static function getNewFields(){
        if(!isset(self::$_cache)){
            self::setCache();
        }

        return self::$_cacheNewFields;
    }

    public function getObjTypesArray()
    {
        if (!$this->saveObjTypes){
            if($this->isNewRecord){
                $this->saveObjTypes = array_keys(ApartmentObjType::getList());
            } else {
                $this->saveObjTypes = CHtml::listData($this->objTypes, 'id', 'id');
            }
        }

        return $this->saveObjTypes;
    }

    public function setObjTypesArray($value){
        $this->saveObjTypes = $value;
    }

    public static function getTipForm($field){
        if(!isset(self::$_cache)){
            self::setCache();
        }

        if(!isset(self::$_cache[$field])){
            return '';
        }

        return '<div class="form_tip">'.self::$_cache[$field]['tip'].'</div>';
    }

    public static function getLabelForm($field){
        if(!isset(self::$_cache)){
            self::setCache();
        }

        if(!isset(self::$_cache[$field])){
            return '';
        }

        return (isset(self::$_cache[$field]['label'])) ? self::$_cache[$field]['label'] : '';
    }

    public function getLabel(){
        if($this->type == self::TYPE_DEFAULT){
            return Apartment::model()->getAttributeLabel($this->field);
        } else {
            return $this->getStrByLang('label');
        }
    }

    public function getUpdateUrl(){
        return $this->type == self::TYPE_DEFAULT ? Yii::app()->createUrl('/formdesigner/backend/main/update', array('id' => $this->id)) :
            Yii::app()->createUrl('/formeditor/backend/main/update', array('id' => $this->id));
    }

    public function beforeDelete(){
        $fieldName = $this->field;

        $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='{{apartment}}' AND COLUMN_NAME='{$fieldName}' AND table_schema = DATABASE()";
        $fieldExist = Yii::app()->db->createCommand($sql)->queryScalar();

        if ($fieldExist) {
            Yii::app()->db->createCommand("ALTER TABLE {{apartment}} DROP `{$fieldName}`")->execute();
        }

        Yii::app()->db->createCommand("DELETE FROM {{search_form}} WHERE field=:field")->execute(array(':field' => $fieldName));

        Yii::app()->cache->flush();

        return parent::beforeDelete();
    }

    public function getTranslateModel(){
        if($this->type == self::TYPE_DEFAULT || $this->isNewRecord){
            return NULL;
        }
        tc('Search by '.$this->field);
        $model = TranslateMessage::model()->findByAttributes(array(
            'category' => 'common',
            'message' => 'Search by '.$this->field
        ));
        return $model;
    }

    public static function getListByCategoryID($categoryID){
        $tmp = 'title_' . Yii::app()->language;
        $sql = "SELECT id, $tmp AS name FROM {{apartment_reference_values}} WHERE reference_category_id = :id";
        $items = Yii::app()->db->createCommand($sql)
            ->queryAll(true, array(
                ':id' => $categoryID
            ));
        return CHtml::listData($items, 'id', 'name');
    }

    public static function isShowForAnything($field){
        $sql = "SELECT f.id FROM {{formdesigner}} f INNER JOIN {{formdesigner_obj_type}} fo ON fo.formdesigner_id = f.id WHERE field=:field";
        return Yii::app()->db->createCommand($sql)->queryScalar(array(':field' => $field));
    }
}