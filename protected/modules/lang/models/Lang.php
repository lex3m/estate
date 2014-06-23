<?php
/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.8.1
 * 	copyright			: 	(c) 2014 Monoray
 * 	website				: 	http://www.monoray.ru/
 * 	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ********************************************************************************************* */

class Lang extends ParentModel
{
	private static $ISOlangs = array('sq' => 'Albanian', 'ar' => 'Arabic', 'az' => 'Azerbaijani', 'bn' => 'Bengali', 'bg' => 'Bulgarian', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en' => 'English', 'et' => 'Estonian', 'fa' => 'Farsi', 'fi' => 'Finnish', 'fr' => 'French', 'de' => 'German', 'ha' => 'Hausa', 'hi' => 'Hindi', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'id' => 'Indonesian', 'it' => 'Italian', 'kk' => 'Kazakh', 'ky' => 'Kyrgyz', 'la' => 'Latin', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'mk' => 'Macedonian', 'mn' => 'Mongolian', 'ne' => 'Nepali', 'no' => 'Norwegian', 'ps' => 'Pushto', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ro' => 'Romanian', 'ru' => 'Russian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovene', 'so' => 'Somali', 'es' => 'Spanish', 'sw' => 'Swahili', 'sv' => 'Swedish', 'tl' => 'Tagalog', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 'vi' => 'Vietnamese', 'cy' => 'Welsh');
	private static $RFC3066langs = array('en' => 'en-US', 'ru' => 'ru-RU', 'tr' => 'tr-TR', 'uk' => 'uk-UA');

    /**
     * @var array ~ example ('ru' => 'ru', 'en' => 'en)
     */
    private static $_activeLangs;

    private static $_activeLangsTranslated;

    private static $_activeLangsFull;

    private static $_mainLang;

	private static $_adminMailLang;

    public $copy_lang_from;

	const FLAG_DIR = '/images/flags/';

	private static $apartmentIndexedFields = array('title', 'description', 'description_near', 'address');

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Lang the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{lang}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name_iso,' . $this->i18nRules('name'), 'required'),
            array('active, currency_id, sorter', 'numerical', 'integerOnly' => true),
            array('name_iso, copy_lang_from', 'length', 'max' => 2),
            array('flag_img', 'length', 'max' => 50),
            array('name_iso', 'dubleIsoValidator'),
            array($this->i18nRules('name'), 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
	        array('name_rfc3066', 'safe'),
	        array('name_rfc3066', 'length', 'max' => 10),
            array('id, currency_id, name_iso, active, sorter, copy_lang_from, date_updated, ' . $this->i18nRules('name'), 'safe', 'on' => 'search'),
        );
    }

    public function i18nFields()
    {
        return array(
            'name' => 'varchar(100) not null'
        );
    }

    public function dubleIsoValidator()
    {
		if($this->getIsNewRecord()){
			$sql = "SELECT COUNT(id) FROM " . $this->tableName() . " WHERE name_iso=:iso";
			$nameIso = $this->name_iso;
			$count = Yii::app()->db->createCommand($sql)
				->bindParam(':iso', $nameIso, PDO::PARAM_STR)
				->queryScalar();
			if ($count > 0) {
				$this->addError('name_iso', tt('Such a language already exists.'));
			}
		}
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'currency' => array(self::BELONGS_TO, 'Currency', 'currency_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name_iso' => tt('Name Iso'),
            'active' => tt('Active'),
            'currency_id' => tc('Currency'),
            'copy_lang_from' => tt('Copy lang from'),
			'admin_mail' => tt('Administrator e-mail'),
			'flag_img' => tt('Flag icon'),
        );
    }


    public function beforeSave()
    {
        if ($this->isNewRecord) {
            $maxSorter = Yii::app()->db->createCommand()
                ->select('MAX(sorter) as maxSorter')
                ->from($this->tableName())
                ->queryScalar();
            $this->sorter = $maxSorter + 1;
        }

		if ($this->scenario == 'set_default') {
            $sql = "UPDATE " . $this->tableName() . " SET main=0 WHERE id!=" . $this->id;
            Yii::app()->db->createCommand($sql)->execute();
        }

		if ($this->scenario == 'set_admin_mail') {
            $sql = "UPDATE " . $this->tableName() . " SET admin_mail=0 WHERE id!=" . $this->id;
            Yii::app()->db->createCommand($sql)->execute();
        }

		// set RFC-3066
		$langCode = 'en-US';
		if (array_key_exists($this->name_iso, self::$RFC3066langs))
			$langCode = self::$RFC3066langs[$this->name_iso];

		$this->name_rfc3066 = $langCode;

        return parent::beforeSave();
    }

	public function afterSave(){
		if ($this->isNewRecord) {
			$this->addLang($this->name_iso);
			Yii::app()->cache->flush();
		}

		return parent::afterSave();
	}

	public function afterDelete() {
		Yii::app()->cache->flush();
		return parent::afterDelete();
	}

	private $_modelNameI18nArr = array(
		'Apartment',
		'ReferenceCategories',
		'ReferenceValues',
		'WindowTo',
		'Lang',
		'TimesIn',
		'TimesOut',
		'ApartmentCity',
		'ApartmentObjType',
		'ApartmentsComplainReason',
		'News',
		'Menu',
		'Article',
		'TranslateMessage',
		'User',
		'FormDesigner',
		'InfoPages',

		// Images - for future multilang comments
	);

	public function init() {
		if (issetModule('seo')) {
			array_push($this->_modelNameI18nArr, "Seo");
			array_push($this->_modelNameI18nArr, "SeoFriendlyUrl");
		}
		if (issetModule('payment')) {
			array_push($this->_modelNameI18nArr, "PaidServices");
			array_push($this->_modelNameI18nArr, "Paysystem");
		}
		if (issetModule('advertising')) {
			array_push($this->_modelNameI18nArr, "Advert");
		}
		if (issetModule('slider')) {
			array_push($this->_modelNameI18nArr, "Slider");
		}
		if (issetModule('location')) {
			array_push($this->_modelNameI18nArr, "City");
			array_push($this->_modelNameI18nArr, "Region");
			array_push($this->_modelNameI18nArr, "Country");
		}

		parent::init();
	}

    public function addLang($lang) {
        $db = Yii::app()->db;

        Yii::import('application.modules.referencecategories.models.ReferenceCategories');
        Yii::import('application.modules.referencevalues.models.ReferenceValues');
        Yii::import('application.modules.windowto.models.WindowTo');
        Yii::import('application.modules.articles.models.Article');
        Yii::import('application.modules.formdesigner.models.FormDesigner');

        // pass on models with the multilanguage fields
        foreach ($this->_modelNameI18nArr as $modelName) {
            $model = new $modelName;
            $table = $model->tableName();
            $i18nFields = $model->i18nFields();

            // add the new field to the table
            foreach ($i18nFields as $field => $type) {
                $columnName = $field . '_' . $lang;
                $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='{$table}' AND COLUMN_NAME='{$columnName}'  AND table_schema = DATABASE()";
                $fieldExist = $db->createCommand($sql)->queryScalar();
                if (!$fieldExist) {
                    $db->createCommand()->addColumn($table, $columnName, $type);

                    // copy the data from other language if it is necessary
                    if ($this->copy_lang_from) {
                        $columnFrom = $field . '_' . $this->copy_lang_from;
						if($modelName == 'SeoFriendlyUrl' && $field == 'url'){
							$sql = "UPDATE `{$table}` SET `{$columnName}`=`model_id`";
						}else{
							$sql = "UPDATE `{$table}` SET `{$columnName}`=`{$columnFrom}`";
						}
                        $db->createCommand($sql)->execute();
                    }

					// add fulltext index
					if ($modelName == 'Apartment' && is_numeric(array_search($field, self::$apartmentIndexedFields))) {
						$addIndex = true;

						$allIndexes = $db->createCommand('SHOW INDEX FROM '.$table)->queryAll();
						if ($allIndexes) {
							$resIndex = CHtml::listData($allIndexes, 'Key_name', 'Index_type');

							if (array_key_exists($columnName, $resIndex))
								$addIndex = false;
						}

						if ($addIndex)
							$db->createCommand('ALTER TABLE '.$table.' ADD FULLTEXT ( '.$columnName.' );')->execute();
					}
                }
            }
        }
    }

    public function beforeDelete()
    {
        if ($this->name_iso == self::getDefaultLang() || $this->name_iso == self::getAdminMailLang() || $this->model()->count() <= 1) {
            return false;
        }

        $this->deleteLang($this->name_iso);
        return parent::beforeDelete();
    }

    public function deleteLang($lang)
    {
        $db = Yii::app()->db;

        Yii::import('application.modules.referencecategories.models.ReferenceCategories');
        Yii::import('application.modules.referencevalues.models.ReferenceValues');
        Yii::import('application.modules.windowto.models.WindowTo');
        Yii::import('application.modules.articles.models.Article');
        Yii::import('application.modules.formdesigner.models.FormDesigner');

        foreach ($this->_modelNameI18nArr as $modelName) {
            $model = new $modelName;
            $table = $model->tableName();
            $i18nFields = $model->i18nFields();

            foreach ($i18nFields as $field => $type) {
                $columnName = $field . '_' . $lang;

                $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME='{$table}' AND COLUMN_NAME='{$columnName}' AND table_schema = DATABASE()";
                $fieldExist = $db->createCommand($sql)->queryScalar();

                if ($fieldExist) {
                    $sql = "ALTER TABLE {$table} DROP `$columnName` ";
                    $db->createCommand($sql)->execute();

					// delete fulltext index
					if ($modelName == 'Apartment' && is_numeric(array_search($field, self::$apartmentIndexedFields))) {
						$deleteIndex = false;

						$allIndexes = $db->createCommand('SHOW INDEX FROM '.$table)->queryAll();
						if ($allIndexes) {
							$resIndex = CHtml::listData($allIndexes, 'Key_name', 'Index_type');

							if (array_key_exists($columnName, $resIndex))
								$deleteIndex = true;
						}

						if ($deleteIndex)
							$db->createCommand('ALTER TABLE '.$table.' DROP INDEX ( '.$columnName.' );')->execute();
					}
                }
            }
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $tmp = 'name_' . Yii::app()->language;
        $criteria->compare($tmp, $this->$tmp, true);
        $criteria->order = 'sorter ASC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getISOlangArray()
    {
        return self::$ISOlangs;
    }

    public static function getISOname($lang)
    {
        return isset(self::$ISOlangs[$lang]) ? self::$ISOlangs[$lang] : '';
    }

    public static function getISOlangForAdd()
    {
        $ret = array();
        foreach(self::$ISOlangs as $iso => $name){
            $ret[$iso] = $iso . " ($name)";
        }
        return $ret;
    }

    public static function getActiveLangs($full = false, $requery = false)
    {
		if(!oreInstall::isInstalled()){
			return array('ru', 'en', 'de');
		}

        if (!isset(self::$_activeLangs) || $requery) {
            $sql = "SELECT id, name_iso, flag_img, main, name_" . Yii::app()->language . " AS name, name_rfc3066, currency_id
                    FROM {{lang}}
                    WHERE active=1
                    ORDER BY sorter ASC";
            $activeLangs = Yii::app()->db->createCommand($sql)->queryAll();

            // Загружаем данные актвных языков и определяем главный ( дефолтный )

            foreach ($activeLangs as $lang) {
                self::$_activeLangs[$lang['name_iso']] = $lang['name_iso'];
                self::$_activeLangsFull[$lang['name_iso']] = $lang;
                self::$_activeLangsTranslated[$lang['name_iso']] = $lang['name'];
				self::$_activeLangsTranslated[$lang['name_iso']] = $lang['name_rfc3066'];

                if ($lang['main']) {
                    self::$_mainLang = $lang['name_iso'];
                }
            }

        }
        return $full ? self::$_activeLangsFull : self::$_activeLangs;
    }

    public static function getAdminMenuLangs()
    {
        $admLangs = array();

        if (!isset(self::$_activeLangsFull))
            self::getActiveLangs();

        $activeLangs = self::$_activeLangsFull;

        foreach ($activeLangs as $lang) {

            if (Yii::app()->language == $lang['name_iso']) {
                $admLang = array(
                    'url' => '',
                    'linkOptions' => array('onclick' => 'return false;', 'class' => 'boldText')
                );
            } else {
                $admLang = array(
                    'url' => Yii::app()->controller->createLangUrl($lang['name_iso'])
                );
            }

            $admLang['label'] = $lang['name'];
            $admLangs[] = $admLang;
        }
        return $admLangs;
    }

    public static function getActiveLangsTranslated()
    {
        if (!isset(self::$_activeLangsTranslated)) {
            self::getActiveLangs();
        }

        return self::$_activeLangsTranslated;
    }

    public static function getDefaultLang()
    {
        if (!isset(self::$_mainLang)) {
            $sql = "SELECT name_iso FROM {{lang}} WHERE active=1 AND main=1";
            self::$_mainLang = Yii::app()->db->createCommand($sql)->queryScalar();
        }
        return self::$_mainLang;
    }

	public static function getAdminMailLang()
    {
        if (!isset(self::$_adminMailLang)) {
            $sql = "SELECT name_iso FROM {{lang}} WHERE active=1 AND admin_mail=1";
            self::$_adminMailLang = Yii::app()->db->createCommand($sql)->queryScalar();
        }
        return self::$_adminMailLang;
    }

    public static function getCurrencyIdForLang($lang)
    {
        if (!isset(self::$_activeLangsFull)) {
            self::getActiveLangs();
        }

        if (isset(self::$_activeLangsFull[$lang]['currency_id'])
            && self::$_activeLangsFull[$lang]['currency_id'] > 0
        ) {
            return self::$_activeLangsFull[$lang]['currency_id'];
        } else {
            Currency::getDefaultCurrncyId();
        }
    }

    public static function getCurrencyNameForLang($lang)
    {
        if (!isset(self::$_activeLangsFull[$lang]['currencyName'])) {
            if (!isset(self::$_activeLangs)) {
                self::getActiveLangs();
            }

            $currency_id = self::getCurrencyIdForLang($lang);

            $sql = "SELECT char_code FROM {{currency}} WHERE id=" . $currency_id;
            $char_code = Yii::app()->db->createCommand($sql)->queryScalar();

            self::$_activeLangsFull[$lang]['currencyName'] = tt($char_code . '_translate', 'currency');

        }
        return self::$_activeLangsFull[$lang]['currencyName'];
    }

    /*
     * Function For check lang by get request eq /hr/site/index
     * If language is by get request found in database and active langage will return current link eg /hr/site/active if your request is eg /fr/site/index
     * in this case will return /en/site/index while fr langage is not in database
     * @param cod varchar 2
     * @param route current action route
     * @param redirection default to true to redirect from eg /fr/ to /en/ while fr dont exists as language
     */
    public static function findByCode($cod, $route, $redirect = true)
    {
        $cod = substr($cod, 0, 2);
        $activeLangs = self::getActiveLangs();

        if (empty($activeLangs[$cod])) {
            $lang = self::getDefaultLang();
        } else {
            $lang = empty($activeLangs[$cod]);
        }

        if ($redirect == true) {
            if (preg_match("/index/", $route))
                Yii::app()->controller->redirect(Yii::app()->homeUrl . $lang . '/');
            else
                Yii::app()->controller->redirect(Yii::app()->homeUrl . $lang . '/' . $route);
        } else {
            return $lang;
        }
    }

	public function getIsDefaultHtml($admin_mail = 0)
    {
        if ($this->active) {
            if ((!$admin_mail && $this->main == 1) || ($admin_mail && $this->admin_mail == 1)) {
                $onclick = 'return false;';
            } else {
                $onclick = "changeDefault(" . $this->id . ", " . $admin_mail . ");";
            }
            return $admin_mail ? CHtml::radioButton("admin_mail", ($this->admin_mail == 1), array('onclick' => $onclick)) :
				CHtml::radioButton("main", ($this->main == 1), array('onclick' => $onclick));
        }
    }

	public function setDefault($admin_mail = 0)
    {
		if ($admin_mail) {
			if ($this->admin_mail || !$this->active) {
				return false;
			}

			$this->scenario = 'set_admin_mail';
			$this->admin_mail = 1;
			$this->update('admin_mail');
		} else {
			if ($this->main || !$this->active) {
				return false;
			}

			$this->scenario = 'set_default';
			$this->main = 1;
			$this->update('main');
		}
        return true;
    }

	public static function getDefaultLangId()
    {
        $sql = "SELECT id FROM {{lang}} WHERE main=1";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }

	public static function getAdminMailLangId()
    {
        $sql = "SELECT id FROM {{lang}} WHERE admin_mail=1";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }

	public static function getFlagImgArray(){
		$d = dir(ROOT_PATH . Lang::FLAG_DIR);

		$arr = array();

		if ($d) {
			while (false !== ($name = $d->read())) {
				if ($name === '.' || $name === '..') continue;
				$arr[] = $name;
			}
			$d->close();
		}
		asort($arr);

		return $arr;
	}

	public static function publishAssetsDD()
	{
		$assets = dirname(__FILE__) . '/../assetsDD';
		$baseUrl = Yii::app()->assetManager->publish($assets);

		if (is_dir($assets)) {
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerCssFile($baseUrl . '/dd.css');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/jquery.dd.js', CClientScript::POS_END);
		} else {
			throw new Exception(Yii::t('common', 'Lang - Error: Couldn\'t find assetsDD folder to publish.'));
		}
	}

	public function getFlagUrl(){
		return Yii::app()->getBaseUrl().Lang::FLAG_DIR.$this->flag_img;
	}

	public function getFlagPath(){
		return Yii::app()->getBasePath().Lang::FLAG_DIR.$this->flag_img;
	}
}