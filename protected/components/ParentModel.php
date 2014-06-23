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

class ParentModel extends CActiveRecord
{
    public $WorkItemsSelected;

    private $_cacheRules;

    public function i18nFields(){
        return NULL;
    }

    public function getI18nFieldSafe(){
        $i18nFields = array_keys($this->i18nFields());
		if(isFree()){
			$activeLangs = array(Yii::app()->language);
		} else {
        	$activeLangs = Lang::getActiveLangs();
		}
        $i18nSafeArr = array();
        foreach($activeLangs as $lang){
            foreach($i18nFields as $field){
                $i18nSafeArr[] = $field.'_'.$lang;
            }
        }
        return implode(', ', $i18nSafeArr);
    }

    public function i18nRules($field){
        if(!isset($this->_cacheRules[$field])){
            $this->_cacheRules[$field] = self::i18nGenFields($field);
        }
        return $this->_cacheRules[$field];
    }

    public static function i18nGenFields($field){
        $activeLangs = Lang::getActiveLangs();
        $i18nRuleArr = array();
        foreach($activeLangs as $lang){
            $i18nRuleArr[] = $field.'_'.$lang;
        }
        return implode(', ', $i18nRuleArr);
    }

    public function getStrByLang($str){
   		$str .= '_'.Yii::app()->language;
   		return $this->$str;
   	}

	public function setStrByLang($str, $value){
   		$str .= '_'.Yii::app()->language;
   		$this->$str = $value;
   	}

	protected function isEmpty($value,$trim=false)
	{
		return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
	}


	public function isLangAttributeRequired($attribute)
	{
		foreach($this->getValidators($attribute) as $validator)
		{
			if($validator instanceof CInlineValidator && $validator->method == 'i18nRequired')
				return true;
		}
		return false;
	}

	public function i18nRequired($attribute, $params) {
		$label = $this->getAttributeLabel($attribute);

		$activeLangs = Lang::getActiveLangs(true);

        foreach($activeLangs as $lang){
			$attr = $attribute.'_'.$lang['name_iso'];
            if($this->isEmpty($this->$attr,true))
				$this->addError($attr, Yii::t('common','{label} cannot be blank for {lang}.', array('{label}'=>$label, '{lang}'=>$lang['name'])));
        }
	}

	public function i18nLength($attribute, $params)
	{

		$label = $this->getAttributeLabel($attribute);

		$activeLangs = Lang::getActiveLangs(true);

        foreach($activeLangs as $lang){
			$attr = $attribute.'_'.$lang['name_iso'];

			$value=$this->$attr;

			if(function_exists('mb_strlen'))
				$length = mb_strlen($value, Yii::app()->charset);
			else
				$length = utf8_strlen($value);

			if(isset($params['min']) && $length<$params['min'])
			{
				$this->addError($attr, Yii::t('common','{label} is too short for {lang} (minimum is {min} characters).',
						array('{label}'=>$label, '{lang}'=>$lang['name'], '{min}'=>$params['min'])));
			}
			if(isset($params['max']) && $length>$params['max'])
			{
				$this->addError($attr, Yii::t('common','{label} is too long for {lang} (maximum is {max} characters).',
						array('{label}'=>$label, '{lang}'=>$lang['name'], '{max}'=>$params['max'])));
			}
			if(isset($params['is']) && $length!==$params['is'])
			{
				$this->addError($attr, Yii::t('common','{label} is of the wrong length for {lang} (should be {length} characters).',
						array('{label}'=>$label, '{lang}'=>$lang['name'], '{length}'=>$params['is'])));
			}
		}
	}

    public function getDateTimeInFormat($field = 'date_created')
    {
        $dateFormat = param('dateFormat', 'd.m.Y H:i:s');
        return date($dateFormat, strtotime($this->$field));
    }

}
