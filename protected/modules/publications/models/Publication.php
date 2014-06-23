<?php

/**
 * This is the model class for table "{{publications}}".
 *
 * The followings are the available columns in table '{{publications}}':
 * @property integer $id
 * @property string $name
 * @property string $document
 * @property string $date
 * @property string $snapshot
 */
class Publication extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{publications}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
    public $document_file;
    public $image;

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, document, date, snapshot', 'required'),
            array('document_file', 'file', 'types'=>'pdf'),
            array('image', 'file', 'types'=>'jpg, gif, png'),
			array('name', 'length', 'max'=>500),
			array('document, snapshot', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, document, date, snapshot', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' =>  tc('Name'),
			'document' => tc('Choose document'),
            'document_file' => tc('Choose document'),
			'date' => tc('Date'),
			'snapshot' => tc('Choose snapshot for document'),
            'image' => tc('Choose snapshot for document'),
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('document',$this->document,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('snapshot',$this->snapshot,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Publication the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
