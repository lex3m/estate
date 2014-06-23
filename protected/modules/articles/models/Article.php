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

class Article extends ParentModel {
	public $title;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{articles}}';
	}

	public function rules(){
		return array(
		    array('page_title, page_body' , 'i18nRequired'),
		    array('page_title', 'i18nLength', 'min'=>2, 'max'=>255),
		    array('page_body' , 'i18nLength', 'min'=>2),
		    array('date_updated', 'safe', 'on'=>'search'),
			array($this->getI18nFieldSafe(), 'safe')
		);
	}

    public function i18nFields(){
        return array(
            'page_title' => 'varchar(255) not null',
            'page_body' => 'text not null',
        );
    }

	public function seoFields() {
		return array(
			'fieldTitle' => 'page_title',
			'fieldDescription' => 'page_body'
		);
	}

    public function getPage_title(){
        return $this->getStrByLang('page_title');
    }

    public function getPage_body(){
        return $this->getStrByLang('page_body');
    }

	public function attributeLabels(){
		return array(
			'page_title' => tt('Title / Question'),
			'page_body' => tt('Body / Answer'),
			'date_updated' => tc('Date updated'),
			'active' => tt('Status'),
		);
	}

	public function search(){

		$criteria=new CDbCriteria;
        $tmp = 'page_title_'.Yii::app()->language;
        $criteria->compare($tmp, $this->$tmp, true);

        $tmp = 'page_body_'.Yii::app()->language;
        $criteria->compare($tmp, $this->$tmp, true);

		$criteria->order = 'sorter ASC';
		return new CActiveDataProvider($this, array(
		    'criteria'=>$criteria,
			'sort' => array(
				'defaultOrder' => 'date_updated DESC',
			),
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
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

	public function afterFind(){
		$this->title = $this->page_title;
	}

	public function beforeSave(){
		if($this->isNewRecord){
			$this->active = 1;

			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				//->where('active=1')
				->from('{{articles}}')
				->queryScalar();
			$this->sorter = $maxSorter+1;
		}
		return parent::beforeSave();
	}

	public function beforeDelete() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "Article"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		$sql = 'DELETE FROM {{comments}} WHERE model_id=:id AND model_name="Article"';
		Yii::app()->db->createCommand($sql)->execute(array(':id' => $this->id));

		return parent::beforeDelete();
	}

	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		return parent::afterSave();
	}

	public function getUrl(){
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'Article');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				return Yii::app()->createAbsoluteUrl('/articles/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->createAbsoluteUrl('/articles/main/view', array(
			'id'=>$this->id,
		));
	}

	public static function getCacheDependency(){
		return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{articles}}');
	}

	public static function getRel($id, $lang){
		$model = self::model()->resetScope()->findByPk($id);

		$title = 'page_title_'.$lang;
		$model->title = $model->$title;

		return $model;
	}
}