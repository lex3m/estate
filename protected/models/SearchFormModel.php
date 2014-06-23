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
 * This is the model class for table "{{search_from}}".
 *
 * The followings are the available columns in table '{{search_from}}':
 * @property integer $id
 * @property integer $status
 * @property integer $obj_type_id
 * @property string $field
 * @property integer $sorter
 */
class SearchFormModel extends ParentModel
{
    const OBJ_TYPE_ID_DEFAULT = 0;

    const STATUS_STANDARD = 1;
    const STATUS_NOT_REMOVE = 2;
    const STATUS_NEW_FIELD = 3;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_form}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('status, obj_type_id, field, sorter', 'required'),
			array('status, obj_type_id, sorter, compare_type, formdesigner_id', 'numerical', 'integerOnly'=>true),
			array('field', 'length', 'max'=>100),
			array('id, status, obj_type_id, field, sorter', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'formdesigner' => array(self::BELONGS_TO, 'FormDesigner', 'formdesigner_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'obj_type_id' => 'Obj Type',
			'field' => 'Field',
			'sorter' => 'Sorter',
		);
	}

    public function scopes(){
        return array(
            'sort' => array(
                'order' => 'sorter ASC',
            ),
        );
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('status',$this->status);
		$criteria->compare('obj_type_id',$this->obj_type_id);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('sorter',$this->sorter);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchFormModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getLabel() {
        return self::getLabelByField($this->field);
    }

    public static function getLabelByField($field){
        if($field == SearchForm::SEARCH_LOCATION){
            if((issetModule('location') && param('useLocation', 1))){
                return tc('Country') . ' / ' . tc('Region') . ' / ' . tc('City');
            } else {
                return tc('City');
            }
        }

        $elements = SearchForm::getSearchFields();
        if(isset($elements[$field])){
            return tc($elements[$field]['translate']);
        } else {
            return tc('Search by '.$field);
        }
    }
}
