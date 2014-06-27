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

class Apartment extends ParentModel {

	public $title;

	public $metroStations;
	public $ownerEmail;
	public $ownerUsername;
	private $_stationsTitle = 0;
    public $in_currency;

    const TYPE_RENT = 1;
    const TYPE_SALE = 2;
    const TYPE_RENTING = 3;
    const TYPE_BUY = 4;
	const TYPE_CHANGE = 5;
    const TYPE_DEFAULT = 1;
    /**Additional apartment types**/
    const TYPE_MORTGAGE = 6;
    const TYPE_PRIVATISATION = 7;


    private static $_type_arr;
	private static $_apartment_arr;
    public $price_old;

    const PRICE_SALE = 1;
    const PRICE_PER_HOUR = 2;
    const PRICE_PER_DAY = 3;
    const PRICE_PER_WEEK = 4;
    const PRICE_PER_MONTH = 5;
    const PRICE_RENTING = 6;
    const PRICE_BUY = 7;
    const PRICE_CHANGE = 8;
    /**Additional price types**/
    const PRICE_MORTGAGE = 9;
    const PRICE_PRIVATISATION = 10;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_MODERATION = 2;
	const STATUS_DRAFT = 3;

    private static $_price_arr;

	public $videoUpload;
	public $video_file;
	public $video_html;

	public $panoramaFile;
    public $references;

    public $period_activity;
    public $popularity;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment}}';
	}

	public function behaviors(){
		return array(
			'ERememberFiltersBehavior' => array(
				'class' => 'application.components.behaviors.ERememberFiltersBehavior',
				'defaults'=>array(),
				'defaultStickOnClear'=>false
			),
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		$video = ApartmentVideo::model();
		$panorama = ApartmentPanorama::model();
		$rules = array(
			//array('price', 'required'),
			//array('price ', 'numerical', 'min' => 1),
			array('price', 'priceValidator', 'except'=>'video_file, video_html, panorama'),
            array('location_id', 'locationRequired', 'except'=>'video_file, video_html, panorama'),
			array('title', 'i18nRequired', 'except'=>'video_file, video_html, panorama'),
			array('price, price_to, floor, floor_total, window_to, type, price_type, obj_type_id, city_id, activity_always', 'numerical', 'integerOnly' => true),
			array('square, land_square', 'numerical'),
			array('type', 'numerical', 'min' => 1),
			array('price_to', 'priceToValidator'),
			array('berths', 'length', 'max' => 255),
			array('title', 'i18nLength', 'max' => 255),
			array('lat, lng', 'length', 'max' => 25),
			array('phone', 'length', 'max' => 15),
			array('id', 'safe', 'on' => 'search'),
			array('floor', 'myFloorValidator'),
			array('is_price_poa', 'boolean'),
			array('in_currency, owner_active, num_of_rooms, is_special_offer, is_free_from, is_free_to, active, metroStations, note, period_activity, sublocation_id', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('city_id, owner_active, active, type, obj_type_id, ownerEmail, ownerUsername, popularity', 'safe', 'on' => 'search'),

			array('video_html', 'checkHtmlCode'/*, 'on' => 'video_html'*/),
			array(
				'video_file', 'file',
				'types' => $video->supportExt,
				'maxSize' => $video->fileMaxSize,
				'allowEmpty' => true,
				//'on' => 'video_file',
			),
			array(
				'panoramaFile', 'file',
				'types' => $panorama->supportedExt,
				'maxSize' => $panorama->maxSize,
				'tooLarge' => Yii::t('module_apartments', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $panorama->maxSizeMb)),
				'allowEmpty' => true,
				//'on' => 'panorama',
			),
		);

        if(issetModule('formeditor')){
            Yii::import('application.modules.formeditor.models.HFormEditor');
            $addRules = HFormEditor::getRulesForModel();
            $rules = CMap::mergeArray($rules, $addRules);
        }

		if (issetModule('location') && param('useLocation', 1)) {
			$rules[] = array('loc_city, loc_region, loc_country', 'safe', 'on' => 'search');
			$rules[] = array('loc_city, loc_region, loc_country', 'numerical', 'integerOnly' => true);
		}

		return $rules;
	}

    public function locationRequired($attribute,$params)
    {
        if($this->location_id<=0)
        {
            $this->addError($attribute, tt("Location required",'module_apartments'));
            return false;
        }
        return true;
    }

    public function requiredAdvanced($attribute,$params=null){
        $isUpdate = Yii::app()->request->getPost('is_update');
        if(!$isUpdate && $this->canShowInForm($attribute) && $this->isEmpty($this->{$attribute})){
            $this->addError($attribute, Yii::t('yii','{attribute} cannot be blank.',
                array('{attribute}'=>$this->getAttributeLabel($attribute))));
        }
    }

	public function checkHtmlCode() {
		if($this->video_html) {
			$apartmentVideoModel = new ApartmentVideo;
			$return = $apartmentVideoModel->parseVideoHTML($this->video_html);

			if (is_array($return) && isset($return[1])) {
				if ($return[1] == 'error') {
					$this->addError('video_html', tt('incorrect_youtube_code', 'apartments'));
				}
			}
		}
	}

	public function priceValidator($attribute, $params){
		if(!$this->is_price_poa){
			if(!$this->price && !$this->price_to){
				$this->addError('price', Yii::t('common', '{label} cannot be blank.', array('{label}' => $this->getAttributeLabel($attribute))));
			}
		}
	}

	public function priceToValidator(){
		if($this->price_to && $this->price){
			if($this->price_to < $this->price){
				$this->addError('price', tt('priceToValidatorText', 'apartments'));
			}
		}
	}

	public function i18nFields(){
		return array(
			'title' => 'text not null',
			'address' => 'varchar(255) not null',
			'description' => 'text not null',
			'description_near' => 'text not null',
			'exchange_to' => 'text not null'
		);
	}

	public function seoFields(){
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'description'
		);
	}

    public function currencyFields(){
        return array('price', 'price_to');
    }

	public function myFloorValidator($attribute,$params){
		if($this->floor && $this->floor_total){
			if($this->floor > $this->floor_total)
			$this->addError('floor', tt('validateFloorMoreTotal', 'apartments'));
		}
	}

	public function relations() {
        Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
        Yii::import('application.modules.apartmentCity.models.ApartmentCity');
		$relations = array(
			'objType' => array(self::BELONGS_TO, 'ApartmentObjType', 'obj_type_id'),

			'city' => array(self::BELONGS_TO, 'ApartmentCity', 'city_id'),

            'location' => array(self::BELONGS_TO, 'Location', 'location_id'), 

			'windowTo' => array(self::BELONGS_TO, 'WindowTo', 'window_to'),

			'images' => array(self::HAS_MANY, 'Images', 'id_object', 'order' => 'images.sorter'),

			/*'countImages' => array(self::STAT, 'Images', 'id_object'),*/
            'stats' => array(self::HAS_MANY, 'Statistic', 'apartment_id'),

			'user' => array(self::BELONGS_TO, 'User', 'owner_id'),

			'video' => array(self::HAS_MANY, 'ApartmentVideo', 'apartment_id',
				'order' => 'video.id ASC',
			),
			'panorama' => array(self::HAS_MANY, 'ApartmentPanorama', 'apartment_id',
				'order' => 'panorama.id ASC',
			),
		);

		if(issetModule('bookingcalendar')) {
			//$bookingCalendar = new Bookingcalendar; // for publish assets
			$relations['bookingCalendar'] = array(self::HAS_MANY, 'Bookingcalendar', 'apartment_id');
		}
		if(issetModule('paidservices')){
			$relations['paids'] = array(self::HAS_MANY, 'ApartmentPaid', 'apartment_id');
		}

		if (issetModule('location') && param('useLocation', 1)) {
			$relations['locCountry'] = array(self::BELONGS_TO, 'Country', 'loc_country');
			$relations['locRegion'] = array(self::BELONGS_TO, 'Region', 'loc_region');
			$relations['locCity'] = array(self::BELONGS_TO, 'City', 'loc_city');
		}

		if(issetModule('seo')){
			$relations['seo'] = array(self::HAS_ONE, 'SeoFriendlyUrl', 'model_id', 'on' => 'model_name="Apartment"');
		}

		return $relations;
	}

    public function scopes()
    {
        return array(
            'onlyAuthOwner' => array(
                'condition' => $this->getTableAlias() . '. owner_id = ' . Yii::app()->user->id
            ),
        );
    }

	public function getUrl() {
        if(issetModule('seo')){
            return $this->getRelationUrl();
        } else {
            return self::getUrlById($this->id);
        }
	}

	public function getRelationUrl(){
		if(issetModule('seo')){
			$seo = $this->seo;
			if($this->seo){
				$field = 'url_'.Yii::app()->language;
				return Yii::app()->createAbsoluteUrl('/apartments/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->createAbsoluteUrl('/apartments/main/view', array(
			'id' => $this->id,
		));
	}

	public static function getUrlById($id){
		if(issetModule('seo')){
			$seo = SeoFriendlyUrl::getForUrl($id, 'Apartment');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				return Yii::app()->createAbsoluteUrl('/apartments/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->createAbsoluteUrl('/apartments/main/view', array(
			'id' => $id,
		));
    }

	public function attributeLabels() {
		return array(
			'id' => tt('ID', 'apartments'),
			'type' => tt('Type', 'apartments'),
			'price' => tt('Price', 'apartments'),
			'num_of_rooms' => tt('Number of rooms', 'apartments'),
			'floor' => tt('Floor', 'apartments'),
			'floor_total' => tt('Total number of floors', 'apartments'),
            'floor_all' => tt('Floor', 'apartments').'/'.tt('Total number of floors', 'apartments'),
			'square' => tt('Square', 'apartments'),
			'land_square' => tt('Land square', 'apartments'),
			'window_to' => tt('Window to', 'apartments'),
			'title' => tt('Apartment title', 'apartments'),
			'description' => tt('Description', 'apartments'),
			'description_near' => tt('What is near?', 'apartments'),
			'metro_station' => tt('Metro station', 'apartments'),
			'address' => tt('Address', 'apartments'),
			'special_offer' => tt('Special offer', 'apartments'),
			'berths' => tt('Number of berths', 'apartments'),
			'active' => tt('Status', 'apartments'),
			'metroStations' => tt('Nearest metro stations', 'apartments'),
			'is_free_from' => tt('Is free from', 'apartments'),
			'is_free_to' => tt('to', 'apartments'),
			'is_special_offer' => tt('Special offer', 'apartments'),
			'obj_type_id' => tt('Object type', 'apartments'),
			'city_id' => tt('City', 'apartments'),
			'city' => tt('City', 'apartments'),
			'owner_active' => tt('Status (owner)', 'apartments'),
			'ownerEmail' => tt('Owner email', 'apartments'),
			'ownerUsername' => tt('ownerUsername', 'apartments'),
			'exchange_to' => tt('Exchange to', 'apartments'),
			'is_price_poa' => tt('is_price_poa', 'apartments'),
			'video_file' => tt('video_file', 'apartments'),
			'video_html' => tt('video_html', 'apartments'),
			'references' => tc('References'),
			'loc_country' => tc('Country'),
			'locCountry' => tc('Country'),
			'loc_region' => tc('Region'),
			'locRegion' => tc('Region'),
			'loc_city' => tc('City'),
			'locCity' => tc('City'),
			'note' => tt('Note', 'apartments'),
            'phone' => tt('Owner phone', 'apartments'),
			'panoramaFile' => tc('A wide angle panorama-image or a ready SWF file of the panorama'),
            'period_activity' => tt("Period of listing's activity", 'apartments'),
            'location_id' => tt("Location",'apartments'),
            'sublocation_id' => tt("Sublocation",'apartments'),
		);
	}



	public function getTitle(){
		return $this->getStrByLang('title');
	}

	public function search() {

        $criteria = new CDbCriteria;
        $tmp = 'title_'.Yii::app()->language;

        $criteria->compare($this->getTableAlias().'.id', $this->id);
        $criteria->compare($this->getTableAlias().'.active', $this->active, true);

        $criteria->addCondition($this->getTableAlias().'.active<>:draft');
        $criteria->params[':draft'] = self::STATUS_DRAFT;

        $criteria->compare($this->getTableAlias().'.owner_active', $this->owner_active, true);
        if (issetModule('location') && param('useLocation', 1)) {
            $criteria->compare('loc_country', $this->loc_country);
            $criteria->compare('loc_region', $this->loc_region);
            $criteria->compare('loc_city', $this->loc_city);
        } else
            $criteria->compare('location_id', $this->city_id);

        $criteria->compare($this->getTableAlias().'.type', $this->type);
        $criteria->compare('obj_type_id', $this->obj_type_id);

        $criteria->compare($tmp, $this->$tmp, true);

        if (issetModule('userads') && param('useModuleUserAds', 1)) {
            if ($this->ownerEmail) {
                $criteria->addCondition('email LIKE "%'.$this->ownerEmail.'%"');
            }
        }
        if ($this->ownerUsername) {
            $criteria->addCondition('username LIKE "%'.$this->ownerUsername.'%"');
        }

        $criteria->addInCondition($this->getTableAlias().'.type', self::availableApTypesIds());

        $criteria->order = $this->getTableAlias().'.sorter DESC';
        $criteria->with = array('city', 'user');

        return new CustomActiveDataProvider($this, array(
            'criteria' => $criteria,
            //'sort'=>array('defaultOrder'=>'sorter'),
            'pagination'=>array(
                'pageSize'=>param('adminPaginationPageSize', 20),
            ),
        ));
    }

	public function getPriceFrom(){
		if(isFree()){
			return $this->price;
		}
	    return round(Currency::convertFromDefault($this->price), param('round_price', 2));
	}
	public function getPriceTo(){
		if(isFree()){
			return $this->price_to;
		}
	    return round(Currency::convertFromDefault($this->price_to), param('round_price', 2));
	}

	public function getCurrency(){
		if(isFree()){
			return param('siteCurrency', '$');
		}else{
			return Currency::getCurrentCurrencyName();
		}
	}

	public static function getFullInformation($apartmentId, $type = Apartment::TYPE_DEFAULT, $catId = null){

        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

		if ($catId)
			$addWhere .= ' AND reference_categories.id = '.(int) $catId.' ';

		$sql = '
			SELECT	style,
					reference_categories.title_'.Yii::app()->language.' as category_title,
					reference_values.title_'.Yii::app()->language.' as value,
					reference_categories.id as ref_id,
					reference_values.id as ref_value_id
			FROM	{{apartment_reference}} reference,
					{{apartment_reference_categories}} reference_categories,
					{{apartment_reference_values}} reference_values
			WHERE	reference.apartment_id = "'.intval($apartmentId).'"
					AND reference.reference_id = reference_categories.id
					AND reference.reference_value_id = reference_values.id
					'.$addWhere.'
			ORDER BY reference_categories.sorter, reference_values.sorter';

		// Таблица apartment_reference меняется только при измении объявления (т.е. таблицы apartment)
		// Достаточно зависимости от apartment вместо apartment_reference
		$dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.intval($apartmentId).'") as t
		');

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

		$return = array();
		foreach($results as $result){
			if(!isset($return[$result['ref_id']])){
				$return[$result['ref_id']]['title'] = $result['category_title'];
				$return[$result['ref_id']]['style'] = $result['style'];
			}
			$return[$result['ref_id']]['values'][$result['ref_value_id']] = $result['value'];
		}
		return $return;
	}

	public static function getCategories($id = null, $type = Apartment::TYPE_DEFAULT, $selected = array()){
        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

		$sql = '
			SELECT	style,
					reference_values.title_'.Yii::app()->language.' as value_title,
					reference_categories.title_'.Yii::app()->language.' as category_title,
					reference_category_id, reference_values.id
			FROM	{{apartment_reference_values}} reference_values,
					{{apartment_reference_categories}} reference_categories
			WHERE	reference_category_id = reference_categories.id AND reference_categories.type=1
			'.$addWhere.'
			ORDER BY reference_categories.sorter, reference_values.sorter';

		$dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}) as t
		');

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

		$return = array();

		if($id){
			$selected = Apartment::getFullInformation($id, $type);
		} else {
		    // При добавлении объявления
			if ($selected && count($selected)) {
				$tmp = array();
				foreach($selected as $selKey => $selVal) {
					$tmp[$selKey]['values'] = $selVal;
				}
				$selected = $tmp;
			}
		}
		if($results){
			foreach($results as $result){
				$return[$result['reference_category_id']]['title'] = $result['category_title'];
				$return[$result['reference_category_id']]['style'] = $result['style'];
				$return[$result['reference_category_id']]['values'][$result['id']]['title'] = $result['value_title'];
				if(isset($selected[$result['reference_category_id']]['values'][$result['id']] )){
					$return[$result['reference_category_id']]['values'][$result['id']]['selected'] = true;
				}
				else{
					$return[$result['reference_category_id']]['values'][$result['id']]['selected'] = false;
				}
			}
		}

		return $return;
	}

    public function getCategoriesForUpdate() {
        if(isset($_POST['category']) && is_array($_POST['category'])) {
            $this->references = Apartment::getCategories(null, $this->type);
            foreach($_POST['category'] as $cat => $cat_arr){
                foreach($cat_arr as $cat_key => $cat_value){
                    $this->references[$cat]['values'][$cat_key]['selected'] = true;
                }
            }
        } else {
            $this->references = Apartment::getCategories($this->id, $this->type);
        }

        return $this->references;
    }

    public function afterFind(){
		if(!isFree()){
        	$this->in_currency = Currency::getDefaultCurrencyModel()->char_code;
		} else {
			$this->in_currency = param('siteCurrency', '$');
		}

        if($this->activity_always){
            $this->period_activity = 'always';
        }else{
            $this->period_activity = param('apartment_periodActivityDefault', 'always');
        }

        return parent::afterFind();
    }

	public function saveCategories(){
		$sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		if(isset($_POST['category'])){
			foreach($_POST['category'] as $catId => $value){
				foreach($value as $valId => $val){
					$sql = 'INSERT INTO {{apartment_reference}} (reference_id, reference_value_id, apartment_id)
						VALUES (:refId, :refValId, :apId) ';
					$command = Yii::app()->db->createCommand($sql);
					$command->bindValue(":refId", $catId, PDO::PARAM_INT);
					$command->bindValue(":refValId", $valId, PDO::PARAM_INT);
					$command->bindValue(":apId", $this->id, PDO::PARAM_INT);
					$command->execute();
				}
			}
		}
	}

	public function beforeValidate(){
		Images::saveComments($this);
		return parent::beforeValidate();
	}

	public function beforeSave(){
		if(!$this->square){
			$this->square = 0;
		}
        /***изменено - вставляем пустые значения */
        if(!$this->title_ru){
            $this->title_ru = '';
        }

        if(!$this->description_ru){
            $this->description_ru = '';
        }

        if(!$this->description_near_ru){
            $this->description_near_ru = '';
        }

        if(!$this->address_ru){
            $this->address_ru = '';
        }

        if(!$this->date_up_search){
            $timestamp = '01/01/2100 00:00:00';
            $timestamp = DateTime::createFromFormat('d/m/Y H:i:s', $timestamp);
            $this->date_up_search = $timestamp->format('Y-m-d H:i:s');
        }

        if(!$this->is_special_offer){
            $this->is_special_offer = '';
        }

        if(!$this->is_free_from){
            $timestamp = '01/01/2000 00:00:00';
            $timestamp = DateTime::createFromFormat('d/m/Y H:i:s', $timestamp);
            $this->is_free_from = $timestamp->format('Y-m-d H:i:s');
        }

        if(!$this->is_free_to){
            $timestamp = '01/01/2100 00:00:00';
            $timestamp = DateTime::createFromFormat('d/m/Y H:i:s', $timestamp);
            $this->is_free_to = $timestamp->format('Y-m-d H:i:s');
        }

        if(!$this->exchange_to_ru){
            $this->exchange_to_ru = '';
        }

        if(!$this->note){
            $this->note = '';
        }

        if(!$this->phone){
            $this->phone = '';
        }

        if(!$this->sublocation_id){
            $this->sublocation_id = 0;
        }

        if(!$this->location_id){
            $this->location_id = '';
        }

        if(!$this->price_old){
            $this->price_old = 0;
        }
        /*************************/

		if(!$this->land_square){
			$this->land_square = 0;
		}

		if($this->isNewRecord){
			$this->owner_id = $this->owner_id ? $this->owner_id : Yii::app()->user->id;

            $userInfo = User::model()->findByPk($this->owner_id, array('select' => 'isAdmin'));
            if ($userInfo && $userInfo->isAdmin == 1) {
                $this->owner_active = self::STATUS_ACTIVE;
            }

            if($this->active != Apartment::STATUS_DRAFT){
                // if admin
                if ($userInfo && $userInfo->isAdmin == 1) {
                    $this->active = Apartment::STATUS_ACTIVE;
                } else {
                    if(param('useUseradsModeration'))
                        $this->active = self::STATUS_MODERATION;
                }
            }

			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter+1;

            if($this->obj_type_id == 0){
                $this->obj_type_id = Yii::app()->db->createCommand('SELECT MIN(id) FROM {{apartment_obj_type}}')->queryScalar();
            }
		}

		if(!isFree()){
			$defaultCurrencyCharCode = Currency::getDefaultCurrencyModel()->char_code;

			if($defaultCurrencyCharCode != $this->in_currency){

				$this->price = (int) Currency::convert($this->price, $this->in_currency, $defaultCurrencyCharCode);

				if (isset($this->price_to) && $this->price_to) {
					$this->price_to = (int) Currency::convert($this->price_to, $this->in_currency, $defaultCurrencyCharCode);
				}
			}
		}

		switch($this->type){
			case self::TYPE_SALE:
				$this->price_type = self::PRICE_SALE;
				break;

			case self::TYPE_BUY:
				$this->price_type = self::PRICE_BUY;
				break;

			case self::TYPE_RENTING:
				$this->price_type = self::PRICE_RENTING;
				break;

			case self::TYPE_CHANGE:
				$this->price_type = self::PRICE_CHANGE;
				break;

            case self::TYPE_MORTGAGE:
                $this->price_type = self::PRICE_MORTGAGE;
                break;

            case self::TYPE_PRIVATISATION:
                $this->price_type = self::PRICE_PRIVATISATION;
                break;
		}

        if(isset($_POST['set_period_activity']) && $_POST['set_period_activity'] == 1 && $this->period_activity){
            $list = self::getPeriodActivityList();
            if(isset($list[$this->period_activity])){
                if($this->period_activity == 'always'){
                    $this->activity_always = 1;
                } else {
                    $this->date_end_activity = date('Y-m-d', strtotime($this->period_activity, time()));
                    $this->activity_always = 0;
                }
            }
        }

		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->scenario == 'savecat'){
			$this->saveCategories();
            if($this->metroStations){
                $this->setMetroStations($this->metroStations);
            }
        }

		if($this->panoramaFile){
			$panorama = new ApartmentPanorama();
			$panorama->fileInstance = $this->panoramaFile;
			$panorama->apartment_id = $this->id;
			$panorama->save();
		}

		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}

		if (issetModule('socialposting') && $this->active == self::STATUS_ACTIVE && $this->owner_active = self::STATUS_ACTIVE) {
			SocialpostingModel::preparePosting($this);
		}

		$sql = 'DELETE FROM {{apartment}} WHERE active=:draft AND date_created<DATE_SUB(NOW(),INTERVAL 1 DAY)';
		Yii::app()->db->createCommand($sql)->execute(array(':draft' => self::STATUS_DRAFT));

		return parent::afterSave();
	}

	public function beforeDelete(){

		if(issetModule('seo')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND ( model_name = "Apartment" OR model_name = "UserAds" )';
			Yii::app()->db->createCommand($sql)->execute();
		}

		$sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{comments}} WHERE model_id="'.$this->id.'" AND model_name="Apartment"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{apartment_statistics}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$sql = 'DELETE FROM {{apartment_complain}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		//Images::deleteByObjectId($this);
		Images::deleteDbByObjectId($this->id);

		$dir = Yii::getPathOfAlias('webroot.uploads.objects') . '/'.$this->id;
		rrmdir($dir);

		if (issetModule('metrostations')) {
			$sql = 'DELETE FROM {{apartment_metro}} WHERE id_apartment="'.$this->id.'"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		//delete QR-code
		$qr_codes = glob( Yii::getPathOfAlias('webroot.uploads.qrcodes') .'/listing_'.$this->id.'-*.png');
		if(is_array($qr_codes) && count($qr_codes))
			array_map("unlink", $qr_codes);

		// delete video
		$sql = 'DELETE FROM {{apartment_video}} WHERE apartment_id="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();

		$pathVideo = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$this->id;
		rmrf($pathVideo);


		if(issetModule('bookingcalendar')){
			$sql = 'DELETE FROM {{booking_calendar}} WHERE apartment_id="'.$this->id.'"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		if (issetModule('comparisonList')) {
			$sql = 'DELETE FROM {{comparison_list}} WHERE apartment_id="'.$this->id.'"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		Yii::app()->cache->flush();

		return parent::beforeDelete();
	}

	public function isValidApartment($id){
		$sql = 'SELECT id FROM {{apartment}} WHERE id = :id';
		$command = Yii::app()->db->createCommand($sql);
		return $command->queryScalar(array(':id' => $id));
	}

	/*public function getMetroStations(){
		$sql = 'SELECT id_station FROM {{apartment_metro}} WHERE id_apartment="'.$this->id.'"';

		return Yii::app()->db->createCommand($sql)->queryColumn();
	}*/

	public function setMetroStations($stations){
		$sql = 'DELETE FROM {{apartment_metro}} WHERE id_apartment="'.$this->id.'"';
		Yii::app()->db->createCommand($sql)->execute();
		if(is_array($stations) && $stations){
			$values = array();
			foreach ($stations as $station) {
				$values[] = '(' . $station . ', ' . $this->id . ')';
			}

			if ($values) {
				$sql = 'INSERT INTO {{apartment_metro}} (id_station, id_apartment) VALUES ' . implode(',', $values);
				Yii::app()->db->createCommand($sql)->execute();
			}
		}
	}

	public function stationsTitle() {
        if (!issetModule('metrostations')) {
            return '';
        }

		if($this->_stationsTitle === 0){
			Yii::import('application.modules.metrostations.models.MetroStation');
			$this->metroStations = $this->getMetroStations();
			$this->_stationsTitle = MetroStation::stationsTitle($this->metroStations);
		}
		return $this->_stationsTitle;
	}

	public static function getFullDependency($id){
		return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{comments}} WHERE model_id = "'.intval($id).'" AND model_name="Apartment"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.intval($id).'"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_window_to}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
	}

	public static function getImagesDependency(){
		return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
	}

	public static function getDependency(){
		return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
	}

	public static function getExistsRooms(){
		$sql = 'SELECT DISTINCT num_of_rooms FROM {{apartment}} WHERE active='.self::STATUS_ACTIVE.' AND owner_active = '.self::STATUS_ACTIVE.' AND num_of_rooms > 0 ORDER BY num_of_rooms';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
	}

    public static function getObjTypesArray($with_all = false){
        Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
        $objTypes = array();
        $objTypeModel = ApartmentObjType::model()->findAll(array(
            'order'=>'sorter'
        ));
        foreach($objTypeModel as $type){
            $objTypes[$type->id] = $type->name;
        }
        if($with_all){
            $objTypes[0] = tt('All object', 'apartments');
        }
        return $objTypes;
    }

    public static function getCityArray($with_all = false){
        Yii::import('application.modules.apartmentCity.models.ApartmentCity');
        $cityArr = array();
        $cityModel = ApartmentCity::model()->findAll(array(
            'order'=>'sorter'
        ));
        foreach($cityModel as $city){
            $cityArr[$city->id] = $city->name;
        }
        if($with_all){
            $cityArr[0] = tt('All city', 'apartments');
        }
        return $cityArr;
    }

    public static function getTypesArray($withAll = false){
        $types = array();

		if($withAll){
            $types[0] = tt('All', 'apartments');
        }

		if (param('useTypeRent', 1)) {
			$types[self::TYPE_RENT] = tt('Rent', 'apartments');
		}
		if (param('useTypeSale', 1)) {
			$types[self::TYPE_SALE] = tt('Sale', 'apartments');
		}
		if (param('useTypeRenting', 1)) {
			$types[self::TYPE_RENTING] = tt('Rent a', 'apartments');
		}
		if (param('useTypeBuy', 1)) {
			$types[self::TYPE_BUY] = tt('Buy a', 'apartments');
		}
		if (param('useTypeChange', 1)) {
			$types[self::TYPE_CHANGE] = tt('Exchange', 'apartments');
		}
        if (param('useTypeMortgage', 1)) {
            $types[self::TYPE_MORTGAGE] = tt('Mortgage', 'apartments');
        }
        if (param('useTypePrivatisation', 1)) {
            $types[self::TYPE_PRIVATISATION] = tt('Privatisation', 'apartments');
        }
        return $types;
    }

	/** For Notifier
	 * @return array
	 */
	public static function getI18nTypesArray(){
        $types = array();

		self::fillI18nArray($types, 'current', Yii::app()->language);
		self::fillI18nArray($types, 'default', Lang::getDefaultLang());
		self::fillI18nArray($types, 'admin', Lang::getAdminMailLang());

        return $types;
    }

	private static function fillI18nArray(&$types, $field, $lang){
		if (param('useTypeRent', 1)) {
			$vs[self::TYPE_RENT] = 'Want rent property to smb';
		}
		if (param('useTypeSale', 1)) {
			$vs[self::TYPE_SALE] = 'Want sale';
		}
		if (param('useTypeRenting', 1)) {
			$vs[self::TYPE_RENTING] = 'Want rent property form smb';
		}
		if (param('useTypeBuy', 1)) {
			$vs[self::TYPE_BUY] = 'Want buy';
		}
		if (param('useTypeChange', 1)) {
			$vs[self::TYPE_CHANGE] = 'Want exchange';
		}
        if (param('useTypeMortgage', 1)) {
            $vs[self::TYPE_MORTGAGE] = 'Want mortgage';
        }
        if (param('useTypePrivatisation', 1)) {
            $vs[self::TYPE_PRIVATISATION] = 'Want privatisation';
        }

		foreach($vs as $type => $langField){
			$types[$type][$field] = tt($langField, 'apartments', $lang);
		}
	}

	public static function getTypesWantArray() {
		$types = array();

		if (param('useTypeRent', 1)) {
			$types[self::TYPE_RENT] = tt('Want rent property to smb', 'apartments');
		}
		if (param('useTypeSale', 1)) {
			$types[self::TYPE_SALE] = tt('Want sale', 'apartments');
		}
		if (param('useTypeRenting', 1)) {
			$types[self::TYPE_RENTING] = tt('Want rent property form smb', 'apartments');
		}
		if (param('useTypeBuy', 1)) {
			$types[self::TYPE_BUY] = tt('Want buy', 'apartments');
		}
		if (param('useTypeChange', 1)) {
			$types[self::TYPE_CHANGE] = tt('Want exchange', 'apartments');
		}
        if (param('useTypeMortgage', 1)) {
            $types[self::TYPE_MORTGAGE] = tt('Want mortgage', 'apartments');
        }
        if (param('useTypePrivatisation', 1)) {
            $types[self::TYPE_PRIVATISATION] = tt('Want privatisation', 'apartments');
        }

        return $types;
	}

    public static function getNameByType($type){
        if(!isset(self::$_type_arr)){
            self::$_type_arr = self::getTypesArray();
        }

		if (!in_array($type, array_keys(self::$_type_arr))) {
			return self::$_type_arr[min(Apartment::availableApTypesIds())];
		}

        return self::$_type_arr[$type];
    }

    public static function getPriceArray($type, $all = false, $with_all = false){
        if($all){
            return array(
                self::PRICE_SALE => tt('Sale price', 'apartments'),
                self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
				self::PRICE_RENTING => '',
				self::PRICE_BUY =>'',
				self::PRICE_CHANGE => '',
                self::PRICE_MORTGAGE => '',
                self::PRICE_PRIVATISATION => '',
            );
        }

        if($type == self::TYPE_SALE){
            $price = array(
                self::PRICE_SALE => tt('Sale price', 'apartments'),
            );
        }elseif($type == self::TYPE_RENT){
            $price = array(
                self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
            );
		}elseif($type == self::TYPE_RENTING){
			$price = array(
				self::PRICE_RENTING => '',
			);
		}elseif($type == self::TYPE_BUY){
			$price = array(
				self::PRICE_BUY => '',
			);
		}elseif($type == self::TYPE_CHANGE){
			$price = array(
				self::PRICE_CHANGE => '',
			);
		}elseif($type == self::TYPE_MORTGAGE){
            $price = array(
                self::PRICE_MORTGAGE => '',
            );
        }elseif($type == self::TYPE_PRIVATISATION){
            $price = array(
                self::PRICE_PRIVATISATION => '',
            );
        }

        if($with_all){
            $price[0] = tt('All');
        }
        return $price;
    }

    public static function getPriceName($price_type){
        if(!isset(self::$_price_arr)){
            self::$_price_arr = self::getPriceArray(NULL, true);
        }
        return self::$_price_arr[$price_type];
    }

	public function getPrettyPrice(){
		if ($this->is_price_poa)
			return tt('is_price_poa', 'apartments');

		$price = $this->getPriceFrom();
		$priceTo = $this->getPriceTo();
		if($this->isPriceFromTo()){
			$priceFromTo = '';
			if ($price)
				$priceFromTo =  tc('price_from').' '.$this->setPretty($price).' '.$this->getCurrency();
			if ($priceTo)
				$priceFromTo .= $priceTo ? ' '.tc('price_to').' '.$this->setPretty($priceTo).' '.$this->getCurrency() : '';

			return $priceFromTo;
		}
        return $this->setPretty($price).' '.$this->getCurrency().' '.self::getPriceName($this->price_type);
    }

    public function getOldPrettyPrice(){
        return $this->setPretty($this->price_old).' '.$this->getCurrency().' '.self::getPriceName($this->price_type);
    }

	public function isPriceFromTo(){
		return $this->type == self::TYPE_RENTING || $this->type == self::TYPE_BUY;
	}

	public function setPretty($price){
		if (!param('usePrettyPrice', 1) || Yii::app()->language != 'ru') {
			return Apartment::priceFormat($price);
		}

		if (substr($price, -6) == "000000"){
			$priceStr = substr_replace ($price, ' '.tt('million', 'apartments'), -6);
		} elseif (substr($price, -5) == "00000" && strlen($price) >= 7) {
			$priceStr = substr_replace ($price, '.', -6, 0);
			$priceStr = substr_replace ($priceStr, ' '.tt('million', 'apartments'), -5);
		} elseif (substr($price, -3) == "000"){
			$priceStr = substr_replace ($price, ' '.tt('thousand', 'apartments'), -3);
		} elseif (substr($price, -2) == "00" && strlen($price) >= 4) {
			$priceStr = substr_replace ($price, '.', -3, 0);
			$priceStr = substr_replace ($priceStr, ' '.tt('thousand', 'apartments'), -2);
		} else {
			return Apartment::priceFormat($price);
		}

		return $priceStr;
	}

	public static function priceFormat($price) {
		if (is_numeric($price)) {
			$priceDecimalsPoint = (param('priceDecimalsPoint')) ? param('priceDecimalsPoint') : ' ';
			$priceThousandsSeparator = (param('priceThousandsSeparator')) ? param('priceThousandsSeparator') : ' ';

			return number_format($price, 0, $priceDecimalsPoint, $priceThousandsSeparator);
		}

		return $price;
	}

	public static function getApTypes(){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		$sql = 'SELECT DISTINCT price_type FROM {{apartment}} WHERE type IN ('.implode(',', Apartment::availableApTypesIds()).') AND active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
	}

	public static function getSquareMinMax(){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		$sql = 'SELECT MIN(square) as square_min, MAX(square) as square_max FROM {{apartment}} WHERE type IN ('.implode(',', Apartment::availableApTypesIds()).') AND active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.'';
		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
	}

	public static function getPriceMinMax($objTypeId = 1, $all = false){
		$ownerActiveCond = '';
		if (param('useUserads'))
			$ownerActiveCond = ' AND owner_active = '.self::STATUS_ACTIVE.' ';

		if ($all)
			$sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE type IN ('.implode(',', Apartment::availableApTypesIds()).') AND active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.' AND is_price_poa = 0';
		else
			$sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE type IN ('.implode(',', Apartment::availableApTypesIds()).') AND obj_type_id = "'.$objTypeId.'" AND active = '.self::STATUS_ACTIVE.' '.$ownerActiveCond.' AND is_price_poa = 0';

		return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
	}

	public static function getModerationStatusArray($withAll = false){
		$status = array();
		if($withAll){
            $status[''] = tt('All', 'common');
        }

		$status[0] = CHtml::encode(tt('Inactive', 'common'));
		$status[1] = CHtml::encode(tt('Active', 'common'));
		$status[2] = CHtml::encode(tt('Awaiting moderation', 'common'));

		return $status;
    }

	public static function getRel($id, $lang){
		$model = self::model()->resetScope()->findByPk($id);

		$title = 'title_'.$lang;
		$model->title = $model->$title;

		return $model;
	}

    public function getAddress(){
        return $this->getStrByLang('address');
    }

    public function getDescription() {
        return $this->getStrByLang('description');
    }

    public function getDescription_Near() {
        return $this->getStrByLang('description_near');
    }

	public static function getApartmentsStatusArray($withAll = false) {
		$status = array();
		if($withAll){
            $status[''] = Yii::t('common', 'All');
        }

		$status[0] = Yii::t('common', 'Inactive');
		$status[1] = Yii::t('common', 'Active');

		return $status;
	}

	public static function getApartmentsStatus($status){
        if(!isset(self::$_apartment_arr)){
            self::$_apartment_arr = self::getApartmentsStatusArray();
        }
        return self::$_apartment_arr[$status];
	}

	public static function setApartmentVisitCount(Apartment $apartment, $ipAddress = '', $userAgent = '') {
		if (isset($apartment->id) && $apartment->id) {
			// total
			$apartment->visits += 1;
			$apartment->save(false, array('visits'));

			// today
			Yii::app()->db->createCommand()->insert('{{apartment_statistics}}', array(
				'apartment_id'=> $apartment->id,
				'date_created' => new CDbExpression('NOW()'),
				'ip_address'=> $ipAddress,
				'browser'=> $userAgent,
			));
		}
	}

	public static function getApartmentVisitCount(Apartment $apartment) {
		if (isset($apartment->id) && $apartment->id) {
			$statistics = array();

			$statistics['all'] = $apartment->visits;

			$statistics['today'] = Yii::app()->db->createCommand()
					->select(array(new CDbExpression("COUNT(id) AS countToday")))
					->from('{{apartment_statistics}}')
					->where('apartment_id = "'.intval($apartment->id).'" AND date(date_created)=date(now())')
					->queryScalar();

			return $statistics;
		}
		return false;
	}

    public static function getCountModeration(){
        $sql = "SELECT COUNT(id) FROM {{apartment}} WHERE type IN (".implode(',', Apartment::availableApTypesIds()).") AND active=".self::STATUS_MODERATION;
        return (int) Yii::app()->db->createCommand($sql)->queryScalar();
    }

	public function getUrlSendEmail(){
		return Yii::app()->createUrl('/apartments/main/sendEmail', array('id'=>$this->id));
	}

	public function getObjType4table(){
		$str = $this->objType->getName();
		if($this->num_of_rooms){
			$str .= '<br/>'.Yii::t('module_apartments', '{n} rooms', $this->num_of_rooms);
		}
		return $str;
	}

	public function getRowCssClass(){
		if($this->is_special_offer){
			return 'special_offer_tr';
		}
		if($this->date_up_search != '0000-00-00 00:00:00'){
			return 'up_in_search';
		}
		return '';
	}


	public function getMapIconUrl(){
		if (isset($this->objType) && $this->objType->icon_file) {
			$iconUrl = Yii::app()->getBaseUrl().'/'.$this->objType->iconsMapPath.'/'.$this->objType->icon_file;
		} else {
			$iconUrl = Yii::app()->getBaseUrl()."/images/house.png";
		}

		return $iconUrl;
	}

	public function getPaidHtml($withDateEnd = false, $withAddLink = false, $icon = false){
		$content = '';
		$htmlArray = $issetPaids = array();

		# применённые платные услуги
		if(isset($this->paids)){
			foreach($this->paids as $apartmentPaid){
				if(isset($apartmentPaid->paidService) && strtotime($apartmentPaid->date_end) > time()){
					$issetPaids[$apartmentPaid->paidService->id] = $apartmentPaid;
				}
			}
		}

		# все платные услуги
		$allPaidServices = PaidServices::model()->findAll('id != '.PaidServices::ID_ADD_FUNDS.' AND active = 1');
		if ($allPaidServices) {
			foreach($allPaidServices as $service) {

				if (!Yii::app()->user->getState('isAdmin')) { # пользователь

					if (array_key_exists($service->id, $issetPaids)) {
						$html = '<div class="paid_row">'.CHtml::link(
								$icon ? $service->getImageIcon(tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end) : $service->name,
								array('/paidservices/main/index',
									'id'=>$this->id,
									'paid_id'=>$service->id,
								),
								array('class'=>'fancy'));
							$html .= $withDateEnd ? '<span class="valid_till"> ('. tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end. ')</span>' : '';
						$html .= '</div>';
					}
					else {
						$html = '<div class="paid_row_no"><span class="boldText">'.CHtml::link(
                                $icon ? $service->getImageIcon() : $service->name,
								array('/paidservices/main/index',
									'id'=>$this->id,
									'paid_id'=>$service->id,
								),
								array('class'=>'fancy')).'</span>';
						$html .= '</div>';
					}

					if (isset($html) && $html) {
						$htmlArray[] = $html;
						unset($html);
					}
				}
				else { # администратор
					if (array_key_exists($service->id, $issetPaids) && $withDateEnd) {
						$html = '<div class="paid_row"><span class="boldText">'.$service->name.'</span>';
							$html .= $withDateEnd ? '<span class="valid_till"> ('. tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end. ')</span>' : '';
						$html .= '</div>';
					}

					if (isset($html) && $html) {
						$htmlArray[] = $html;
						unset($html);
					}
				}
			}
		}

		if(count($htmlArray) > 0) {
			$content = implode('', $htmlArray);
		} else {
			$content = '<div class="paid_row">'.tc('No').'</div>';
		}

		if(Yii::app()->user->getState('isAdmin') && $withAddLink){
			$addUrl = Yii::app()->createUrl('/paidservices/backend/main/addPaid', array(
				'id' => $this->id,
				'withDate' => (int) $withDateEnd,
			));

			$content .= CHtml::link(tc('Add'), $addUrl,	array(
				'class' => 'tempModal boldText',
				'title' => tc('Apply a paid service to the listing')
			));
		}

		return CHtml::tag('div', array('id' => 'paid_row_el_'.$this->id), $content);
	}

	public static function findAllWithCache($criteria){
		return Apartment::model()
				->cache(param('cachingTime', 1209600), Apartment::getImagesDependency())
				->with(array('images', 'objType'))
				->findAll($criteria);
	}

    public function canShowInView($field) {

        switch($field){
            case 'floor_all':
                if(!$this->floor && !$this->floor_total){
                    return false;
                }
                break;

            case 'phone':
                if( !$this->phone ){
                    if(!isset($this->user) || !$this->user->phone){
                        return false;
                    }
                }
                break;

            default:
                if(!isset($this->$field) || !$this->$field){
                    return false;
                }
        }

        if (issetModule('formdesigner')) {
            Yii::import('application.modules.formdesigner.models.*');
            return FormDesigner::canShow($field, $this);
        }

        return true;
    }

    public function canShowInForm($field) {
        if (issetModule('formdesigner')){
            Yii::import('application.modules.formdesigner.models.*');
            return FormDesigner::canShow($field, $this);
        }
        return true;
    }

    public static function getTip($field) {
        if (issetModule('formdesigner')){
            Yii::import('application.modules.formdesigner.models.*');
            return FormDesigner::getTipForm($field);
        }
        return '';
    }

    public function isOwner($orAdmin = false) {
        $isOwner = $this->owner_id == Yii::app()->user->id;
        if($isOwner || ($orAdmin && Yii::app()->user->getState('isAdmin'))){
            return true;
        }
        return false;
    }

    public function getEditUrl() {
        $editUrl = '';

        if(Yii::app()->user->getState('isAdmin')){
            $editUrl = Yii::app()->createUrl('/apartments/backend/main/update', array('id' => $this->id));
        }elseif($this->owner_id == Yii::app()->user->id){
            $editUrl = Yii::app()->createUrl('/userads/main/update', array('id' => $this->id));
        }
        return $editUrl;
    }

	public static function availableApTypesIds() {
		$return = array();

		if (param('useTypeRent', 1)) {
			$return[] = self::TYPE_RENT;
		}
		if (param('useTypeSale', 1)) {
			$return[] = self::TYPE_SALE;
		}
		if (param('useTypeRenting', 1)) {
			$return[] = self::TYPE_RENTING;
		}
		if (param('useTypeBuy', 1)) {
			$return[] = self::TYPE_BUY;
		}
		if (param('useTypeChange', 1)) {
			$return[] = self::TYPE_CHANGE;
		}
        if (param('useTypeMortgage', 1)) {
            $return[] = self::TYPE_MORTGAGE;
        }
        if (param('useTypePrivatisation', 1)) {
            $return[] = self::TYPE_PRIVATISATION;
        }

		return $return;
	}

	public function getAttributeLabel($attribute){
		if(issetModule('formeditor')){
			$label = FormDesigner::getLabelForm($attribute);

			return $label ? $label : parent::getAttributeLabel($attribute);
		}

		return parent::getAttributeLabel($attribute);
	}

	public static function returnMainThumbForGrid($data = null) {
		if ($data) {
			$res = Images::getMainThumb(60,45, $data->images);
			return CHtml::image($res['thumbUrl'], $data->getStrByLang('title'), array(
				'title' => $data->getStrByLang('title'),
				'class' => 'apartment_type_img_small'
			));
		}
	}

    public function setDefaultType(){
        if (param('useTypeRent', 1)) {
            $this->type = Apartment::TYPE_RENT;
        }
        elseif(param('useTypeSale', 1)) {
            $this->type = Apartment::TYPE_SALE;
        }
        elseif(param('useTypeRenting', 1)) {
            $this->type = Apartment::TYPE_RENTING;
        }
        elseif(param('useTypeBuy', 1)) {
            $this->type = Apartment::TYPE_BUY;
        }
        elseif(param('useTypeChange', 1)) {
            $this->type = Apartment::TYPE_CHANGE;
        }
        elseif(param('useTypeMortgage', 1)) {
            $this->type = Apartment::TYPE_MORTGAGE;
        }
        elseif(param('useTypePrivatisation', 1)) {
            $this->type = Apartment::TYPE_PRIVATISATION;
        }
        else
            $this->type = 0;
    }

    public static function getModeShowList(){
        return array(
            'block' => tt('Display block', 'apartments'),
            'table' => tt('Display table', 'apartments'),
            'map' => tt('Display with a map', 'apartments'),
        );
    }

    public static function getPeriodActivityList(){
        // key for strtotime - http://php.net/manual/ru/function.strtotime.php
        return array(
            '+1 week' => tt('a week', 'apartments'),
            '+1 month' => tt('a month', 'apartments'),
            '+3 month' => tt('3 months', 'apartments'),
            '+6 month' => tt('6 months', 'apartments'),
            '+1 year' => tt('a year', 'apartments'),
            'always' => tt('always', 'apartments'),
        );
    }

    public function canSetPeriodActivity(){
        return $this->activity_always || time() >= strtotime($this->date_end_activity);
    }

    public function getDateEndActivityLongFormat(){
        return Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), CDateTimeParser::parse($this->date_end_activity, 'yyyy-MM-dd'));
    }

	public function writeRating($id, $rating){
		$sql = 'UPDATE {{apartment}} SET rating=:rating WHERE id=:id';
		Yii::app()->db->createCommand($sql)->execute(array(':rating' => $rating, ':id' => $id));
	}

    public function getSquareString(){
        if($this->canShowInForm('square')){
            return $this->square." ".tc("site_square");
        }

        if($this->canShowInForm('land_square')){
            return $this->land_square." ".tc("site_land_square");
        }
    }

    public function getSublocationsOfLocation($location_id){
        $data=Sublocation::model()->findAll('location_id=:location_id',
            array(':location_id'=>$location_id));

        $data=CHtml::listData($data,'id','name');
        $chooseTranslate = tt("Choose sublocation",'apartments');
        array_unshift($data, $chooseTranslate);
        return $data;
        /*foreach($data as $value=>$name)
        {
            echo CHtml::tag('option',
                array('value'=>$value),CHtml::encode($name),true);
        }*/
    }

    //устанавливает просмотры последних обьявлений
    public function setLastVisitedObjectsCookies($apartment_id){
        $oldCookie = Yii::app()->request->cookies['objects'];
        $date=new DateTime();

        $tempObjectArray = array();
        $currentDate = $date->format('Y-m-d');
        if ($oldCookie) {
            $objectIdArray = unserialize($oldCookie);
            if (Yii::app()->request->cookies['objects'])
                unset(Yii::app()->request->cookies['objects']);
            //уничтожаем через неделю
            $one_week_ago = strtotime('-1 week');

            //чистим все просмотры недельной давности
            foreach ($objectIdArray as $key=>$value){
                foreach ($value as $d=>$v)
                {
                    $timeVisitedPage = strtotime($d);
                    if ($timeVisitedPage < $one_week_ago)
                    {
                        unset($value[$d]);
                    }
                    else
                        array_push($tempObjectArray, $value);
                }
            }
        }

        //добавляем новые просмотры в массив
        $view = array();
        $view[$currentDate]=$apartment_id;
        array_push($tempObjectArray, $view);

        //сохраняем новые cookie
        $newCookie = new CHttpCookie('objects', serialize($tempObjectArray));
        $newCookie->expire = time() + (60*60*24*7);
        Yii::app()->request->cookies['objects'] = $newCookie;
    }
}