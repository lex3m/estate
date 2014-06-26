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

class SearchForm {
    const SEARCH_AP_TYPE = 'ap_type';
    const SEARCH_OBJ_TYPE = 'obj_type';
   // const SEARCH_LOCATION = 'location';
    const SEARCH_ROOMS = 'rooms';
    const SEARCH_PRICE = 'price';
    const SEARCH_SQUARE = 'square';
    const SEARCH_FLOOR = 'floor';
    const SEARCH_BY_ID = 'by_id';
    const SEARCH_BY_LAND_SQUARE = 'land_square';

    private static $_cache;

    private static $js = array(
        'countField' => 0
    );

    public static function getSearchFields(){
        if(!isset(self::$_cache['fields'])){
            self::$_cache['fields'] = array(
                self::SEARCH_AP_TYPE => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Search in section',
                ),
                self::SEARCH_OBJ_TYPE => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Property type',
                ),
               /* self::SEARCH_LOCATION => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Search by location',
                ),*/
                self::SEARCH_ROOMS => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Rooms range',
                ),
                self::SEARCH_PRICE => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Price range',
                ),
                self::SEARCH_SQUARE => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Square range',
                ),
                self::SEARCH_FLOOR => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Floor range',
                ),
                self::SEARCH_BY_ID => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Apartment ID',
                ),
                self::SEARCH_BY_LAND_SQUARE => array(
                    'status' => SearchFormModel::STATUS_STANDARD,
                    'translate' => 'Apartment square to',
                ),
            );

            if(issetModule('formeditor')){
                //Yii::import('application.modules.formeditor.models.HFormEditor');
                $newFieldsAll = FormDesigner::getNewFields();
                foreach($newFieldsAll as $field){
                    self::$_cache['fields'][$field->field] = array(
                        'status' => SearchFormModel::STATUS_NEW_FIELD,
                        'translate' => 'Search by ' . $field->field,
                        'formdesigner_id' => $field->id,
                    );
                }
            }
        }
        return self::$_cache['fields'];
    }

    public static function cityInit(){
        $cityActive = array();
        if (oreInstall::isInstalled()) {
            Yii::import('application.modules.apartmentCity.models.ApartmentCity');
            $cityActive = ApartmentCity::getActiveCity();
            if($cityActive === null){
                $cityActive = array();
            }
        }
        return $cityActive;
    }

    public static function apTypes(){
		$result = Apartment::getApTypes();

		$types = array(0 => Yii::t('common', 'Please select'));

		if (param('useTypeSale', 1)) {
			if(in_array(Apartment::PRICE_SALE, $result)){
				$types[Apartment::PRICE_SALE] = tt('Sale', 'apartments');
			}
		}

		if (param('useTypeBuy', 1)) {
			if(in_array(Apartment::PRICE_BUY, $result)){
				$types[Apartment::PRICE_BUY] = tt('Buy a', 'apartments');
			}
		}

		if (param('useTypeRenting', 1)) {
			if(in_array(Apartment::PRICE_RENTING, $result)){
				$types[Apartment::PRICE_RENTING] = tt('Rent a', 'apartments');
			}
		}

		if (param('useTypeChange', 1)) {
			if(in_array(Apartment::PRICE_CHANGE, $result)){
				$types[Apartment::PRICE_CHANGE] = tt('Exchange', 'apartments');
			}
		}

        if (param('useTypeMortgage', 1)) {
            if(in_array(Apartment::PRICE_MORTGAGE, $result)){
                $types[Apartment::PRICE_MORTGAGE] = tt('Mortgage', 'apartments');
            }
        }

        if (param('useTypePrivatisation', 1)) {
            if(in_array(Apartment::PRICE_PRIVATISATION, $result)){
                $types[Apartment::PRICE_PRIVATISATION] = tt('Privatisation', 'apartments');
            }
        }

		if (param('useTypeRent', 1)) {
			if(in_array(Apartment::PRICE_PER_DAY, $result)){
				$types[Apartment::PRICE_PER_DAY] = tc('rent by the day');
			}

			if(in_array(Apartment::PRICE_PER_HOUR, $result)){
				$types[Apartment::PRICE_PER_HOUR] = tc('rent by the hour');
			}

			if(in_array(Apartment::PRICE_PER_MONTH, $result)){
				$types[Apartment::PRICE_PER_MONTH] = tc('rent by the month');
			}

			if(in_array(Apartment::PRICE_PER_WEEK, $result)){
				$types[Apartment::PRICE_PER_WEEK] = tc('rent by the week');
			}
		}


		$return['propertyType'] = $types;

		if (issetModule('selecttoslider') && param('usePriceSlider') == 1) {
			$return['currencyTitle'] = array(Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':', Yii::t('common', 'Price range').':');
		}
		else {
			$return['currencyTitle'] = array(Yii::t('common', 'Payment to'), Yii::t('common', 'Payment to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'), Yii::t('common', 'Fee up to'));
		}

		return $return;
	}

    public static function getSliderStep($diffPrice){
        if ($diffPrice <= 10)
            $step = 1;
        else
            $step = 10;

        if ($diffPrice > 100) {
            $step = 10;
        }
        if ($diffPrice > 1000) {
            $step = 100;
        }
        if ($diffPrice > 10000) {
            $step = 1000;
        }
        if ($diffPrice > 100000) {
            $step = 10000;
        }
        if ($diffPrice > 1000000) { // 1 million
            $step = 300000;
        }
        if ($diffPrice > 10000000) { // 10 millions
            $step = 1000000;
        }
        if ($diffPrice > 100000000) { // 100 millions
            $step = 10000000;
        }

        return $step;
    }

    public static function renderSliderRange(array $params){
		
        $cssClass = isset($params['class']) ? $params['class'] : 'default-search-select';
        echo '<div class="index-search-form '.$cssClass.'">';
        echo '<div id="slider-range-'.$params['field'].'"></div>';
        echo '<div class="vals">';
        echo '<div id="'.$params['field'].'_min_val" class="left">' . CHtml::encode($params['min_sel']) . '</div>';
        echo '<div id="'.$params['field'].'_max_val" class="right">' . CHtml::encode($params['max_sel']) . '</div>';
        echo '</div>';
        echo '</div>';

        echo CHtml::hiddenField($params['field'].'_min', $params['min_sel']);
        echo CHtml::hiddenField($params['field'].'_max', $params['max_sel']);

        if(isset($params['measure_unit']) && $params['measure_unit']){
            echo '<div class="slider-price-currency">'.$params['measure_unit'].'</div>';
        }

        // for javascript
        $intValues = array('min', 'max', 'min_sel', 'max_sel', 'step');
        foreach($intValues as $key){
            $params[$key] = (int) $params[$key];
        }

        self::$js['sliderRangeFields'][] = array(
            'field' => $params['field'],
            'params' => $params
        );
    }

    public static function getSliderRangeFields(){
        return isset(self::$js['sliderRangeFields']) ? self::$js['sliderRangeFields'] : false;
    }

    public static function getCityField(){
        return isset(self::$js['cityField']) ? self::$js['cityField'] : false;
    }

    public static function setJsParam($key, $val){
        self::$js[$key]= $val;
    }

    public static function increaseJsCounter(){
        self::$js['countField']++;
    }

    public static function getCountFiled(){
        return self::$js['countField'];
    }
}
