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

class News extends ParentModel {
	public $title;
	public $dateCreated;
	public $dateCreatedLong;
	public $supportedExt = 'jpg, png, gif';

	public $newsImage;
	public $maxImageSize;
	public $maxImageSizeMb;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{news}}';
	}

	public function rules() {
		return array(
			array('title, body', 'i18nRequired'),
			array('title', 'i18nLength', 'max' => 128),
			array(
				'newsImage', 'file',
				'types' => $this->supportedExt,
				'maxSize' => $this->maxImageSize,
				'tooLarge' => Yii::t('module_apartments', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $this->maxImageSizeMb)),
				'allowEmpty' => true,
			),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
        return array(
            'title' => 'varchar(255) not null',
            'body' => 'text not null',
			'announce' => 'text not null',
        );
    }

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'body'
		);
	}

	public function	init(){
		$fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
		$fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));
		$this->maxImageSize = min($fileMaxSize);
		$this->maxImageSizeMb = round($this->maxImageSize / (1024*1024));


		parent::init();
	}

    public function getTitle(){
        return $this->getStrByLang('title');
    }

    public function getBody(){
        return $this->getStrByLang('body');
    }

	public function getAnnounce(){
		return $this->getStrByLang('announce');
	}

	public function relations(){
		return array(
			'image' => array(self::BELONGS_TO, 'NewsImage', 'image_id'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'title' => tt('News title', 'news'),
			'body' => tt('News body', 'news'),
			'date_created' => tt('Creation date', 'news'),
			'dateCreated' => tt('Creation date', 'news'),
			'announce' => tt('Announce', 'news'),
			'newsImage' => tt('Image for news', 'news'),
		);
	}

	public function getUrl() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'News');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				return Yii::app()->createAbsoluteUrl('/news/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->createAbsoluteUrl('/news/main/view', array(
			'id' => $this->id,
		));
	}

	public function search() {
		$criteria = new CDbCriteria;

        $titleField = 'title_'.Yii::app()->language;
		$criteria->compare($titleField, $this->$titleField, true);
        $bodyField = 'body_'.Yii::app()->language;
		$criteria->compare($bodyField, $this->$bodyField, true);

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

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	protected function afterFind() {
		$dateFormat = param('newsModule_dateFormat', 0) ? param('newsModule_dateFormat') : param('dateFormat', 'd.m.Y H:i:s');
		$this->dateCreated = date($dateFormat, strtotime($this->date_created));
		$this->dateCreatedLong = Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), CDateTimeParser::parse($this->date_created, 'dd MM hh:mm:ss'));
		return parent::afterFind();
	}

	public function beforeSave(){
		if($this->newsImage){
			if($this->image){
				$this->image->delete();
			}
			$image = new NewsImage();
			$image->imageInstance = $this->newsImage;
			$image->save();
			if($image->id){
				$this->image_id = $image->id;
			}
		}

		return parent::beforeSave();
	}


	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		return parent::afterSave();
	}

	public function beforeDelete() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "News"';
			Yii::app()->db->createCommand($sql)->execute();
		}
		if($this->image){
			$this->image->delete();
		}

		$sql = 'DELETE FROM {{comments}} WHERE model_id=:id AND model_name="News"';
		Yii::app()->db->createCommand($sql)->execute(array(':id' => $this->id));

		return parent::beforeDelete();
	}

	public function getAllWithPagination($inCriteria = null){
		if($inCriteria === null){
			$criteria = new CDbCriteria;
			$criteria->order = 't.date_created DESC';
		} else {
			$criteria = $inCriteria;
		}

		$pages = new CPagination($this->count($criteria));
		$pages->pageSize = param('moduleNews_newsPerPage', 10);
		$pages->applyLimit($criteria);

		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{news}}');

		$criteria->with = array('image');
		$items = $this->cache(param('cachingTime', 1209600), $dependency)->findAll($criteria);

		return array(
			'items' => $items,
			'pages' => $pages,
		);
	}
}