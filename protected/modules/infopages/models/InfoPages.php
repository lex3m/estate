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

class InfoPages extends ParentModel {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	const MAIN_PAGE_ID = 1;

    const POSITION_BOTTOM = 1;
    const POSITION_TOP = 2;

    public static function getPositionList(){
        return array(
            self::POSITION_BOTTOM => tt('Bottom', 'infopages'),
            self::POSITION_TOP => tt('Top', 'infopages'),
        );
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{infopages}}';
	}

	public function rules() {
		return array(
			array('title', 'i18nRequired'),
			array('title', 'i18nLength', 'max' => 255),
			array('active, widget, widget_data, widget_position', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('active', 'safe', 'on' => 'search'),
		);
	}

	public function relations(){
		return array(
			'menuPage' => array(self::HAS_MANY, 'Menu', 'pageId'),
			'menuPageOne' => array(self::HAS_ONE, 'Menu', 'pageId'),
		);
	}

	public function i18nFields(){
		return array(
			'title' => 'varchar(255) not null',
			'body' => 'text not null',
		);
	}

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'body'
		);
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

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'active' => tc('Status'),
			'title' => tt('Page title'),
			'body' => tt('Page body'),
			'date_created' => tt('Creation date'),
			'widget' => tt('Widget', 'infopages'),
			'widget_position' => tt("Widget's position", 'infopages'),
		);
	}

	public function getUrl() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'InfoPages');

			if($seo){
				$field = 'url_'.Yii::app()->language;
                if($seo->direct_url){
                    return Yii::app()->getBaseUrl(true) . '/' . $seo->$field . ( param('urlExtension') ? '.html' : '' );
                }
				return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
			'id' => $this->id,
		));
	}

	public function search() {
		$criteria = new CDbCriteria;

        $titleField = 'title_'.Yii::app()->language;
		$criteria->compare($titleField, $this->$titleField, true);
        $bodyField = 'body_'.Yii::app()->language;
		$criteria->compare($bodyField, $this->$bodyField, true);

		$criteria->compare($this->getTableAlias().'.active', $this->active, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function getWidgetOptions($widget = null){
		$arrWidgets =  array(
			'' => tc('No'),
			'news' => tc('News'),
			'apartments' => tc('Listing'),
			'viewallonmap' => tc('Search for listings on the map'),
			'contactform' => tc('The form of the section "Contact Us"'),
			'randomapartments' => tc('Listing (random)'),
			'specialoffers' => tc('Special offers'),
		);

		if ($widget && array_key_exists($widget, $arrWidgets))
			return $arrWidgets[$widget];

		return $arrWidgets;
	}

	public static function getInfoPagesAddList() {
		$return = array();
		$result = InfoPages::model()->findAll('active = '.self::STATUS_ACTIVE);
		if ($result) {
			foreach($result as $item) {
				$return[$item->id] = $item->getStrByLang('title');
			}
		}

		return $return;
	}

	public function getTitle(){
		$return = CHtml::encode($this->getStrByLang('title'));

		if (Yii::app()->user->getState('isAdmin')) {
			$href = array();
			switch ($this->id) {
				case 2:
					$href = array('/news/backend/main/admin');
					break;
				case 4:
					$href = array('/articles/backend/main/admin');
					break;
			}
			if($href){
				$return .= ' ['.CHtml::link('Управление разделом', $href).']';
			}
		}

		return $return;
	}

	public function getBody(){
		return $this->getStrByLang('body');
	}

	public function beforeSave(){
        $this->widget_data='';
        if($this->widget == 'apartments' && isset($_POST['filter'])){
            $this->widget_data = CJSON::encode($_POST['filter']);
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
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "InfoPages"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		return parent::beforeDelete();
	}

    private $_filter;

    public function getCriteriaForAdList(){
        $criteria = new CDbCriteria();
        if($this->widget_data){
            $this->_filter = CJSON::decode($this->widget_data);

            if(issetModule('location') && param('useLocation', 1)){
                $this->setForCriteria($criteria, 'country_id', 'loc_country');
                $this->setForCriteria($criteria, 'region_id', 'loc_region');
                $this->setForCriteria($criteria, 'city_id', 'loc_city');
            } else {
                $this->setForCriteria($criteria, 'city_id', 'city_id');
            }

            $this->setForCriteria($criteria, 'type', 'type');
            $this->setForCriteria($criteria, 'obj_type_id', 'obj_type_id');
        }

        //deb($criteria);

        return $criteria;
    }

    private function setForCriteria($criteria, $key, $field){
        if(isset($this->_filter[$key]) && $this->_filter[$key]){
            $criteria->compare($field, $this->_filter[$key]);
        }
    }
}