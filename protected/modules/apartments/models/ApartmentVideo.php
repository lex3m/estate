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

class ApartmentVideo extends ParentModel {
	public $supportExt = 'flv, mp4';
	public $fileMaxSize = 10485760; /* 1024 * 1024 * 10 - 10 MB */

	public $path = 'webroot.uploads.video';
	public $url = 'uploads/video';

	public function init() {
		$fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
		$fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));

		$this->fileMaxSize = min($fileMaxSize);

		parent::init();
	}

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_video}}';
	}

	public function rules() {
		return array(
			array('apartment_id', 'required'),
			array('apartment_id', 'numerical', 'integerOnly' => true),
			array('id, apartment_id', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		Yii::import('application.modules.apartments.models.Apartment');
		return array(
			'apartment' => array(self::BELONGS_TO, 'Apartment', 'apartment_id'),
		);
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_updated',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'apartment_id' => tt('apartment_id', 'apartments'),
			'video_html' => tt('video_html', 'apartments'),
			'video_file' => tt('video_file', 'apartments'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('apartment_id', $this->apartment_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public function beforeDelete() {
		if($this->video_file){
			$pathVideo = Yii::getPathOfAlias($this->path).DIRECTORY_SEPARATOR.$this->apartment_id.DIRECTORY_SEPARATOR;
			deleteFile($pathVideo, $this->video_file);
		}

		return parent::beforeDelete();
	}

	public function parseVideoHTML($code) {
		$return = $this->parseVideoEmbed($code);
		if (!$return[0]) {
			$return = $this->parseVideoIframe($code);
		}
		return $return;
	}

	public function isFile(){
		return $this->video_file ? true : false;
	}

	public function isHtml(){
		return $this->video_html ? true : false;
	}

	public function isFileExists(){
		$path = Yii::getPathOfAlias($this->path).DIRECTORY_SEPARATOR.$this->apartment_id.DIRECTORY_SEPARATOR.$this->video_file;
		return file_exists($path);
	}

	public function getFileUrl(){
		return Yii::app()->getBaseUrl().'/'.$this->url.'/'.$this->apartment_id.'/'.$this->video_file;
	}

	function parseVideoEmbed($code) {
		$code = str_replace("'", "\"", stripslashes($code));

		$pattObject = '/<object(.*)>(.*)(<embed.*><\/embed>).*<\/object>/Us';
		preg_match($pattObject, $code, $objMatch);

		if (is_array($objMatch) && count($objMatch) > 0) {
			$pattObjectParams = '/(width="\d+"|height="\d+")/Us';
			$objParamsCode = $objMatch[1];
			preg_match_all($pattObjectParams, $objParamsCode, $objectParMatch);

			if (count($objectParMatch[1]) == 2) {
				$objParStr = strip_tags(implode(' ', $objectParMatch[1]));
			}else
				return array(false, "error");

			$pattParam = '/<param.*name="(.*)".*value="(.*)".*>.*<\/param>/Us';
			$paramCode = strip_tags($objMatch[2], '<param>');
			preg_match_all($pattParam, $paramCode, $paramsMatch);

			$paramsNames = $paramsMatch[1];
			$paramsValues = $paramsMatch[2];
			$paramStr = '';
			foreach ($paramsNames as $key => $item) {
				$paramStr .= '<param name="' . $paramsNames[$key] . '" value="' . $paramsValues[$key] . '" ></param>';
			}
			if ($paramStr == '')
				return array(false, "error");

			$embedCode = strip_tags($objMatch[3], '<embed>');
			$embedPatt = '/([a-zA-Z]*=".*")/Us';
			preg_match_all($embedPatt, $embedCode, $embedMatch);
			$embedParams = $embedMatch[1];

			if (!count($embedParams))
				return array(false, "error");
			$embedStr = '<embed ' . implode(' ', $embedParams) . ' ></embed>';

			$objStr = "<object " . $objParStr . " >" . $paramStr . $embedStr . "</object>";
			return array(true, $objStr);
		}
		return array(false, "error");
	}

	public function parseVideoIframe($code) {
		$code = str_replace("'", "\"", stripslashes($code));

		$pattObject = '/<iframe(.*)>/Us';
		preg_match($pattObject, $code, $objMatch);

		if (is_array($objMatch) && count($objMatch) > 0) {
			$pattObjectParams = '/(width|title|height|src|frameborder)="([^\"]*)"/Us';
			$objParamsCode = $objMatch[1];
			preg_match_all($pattObjectParams, $objParamsCode, $objectParMatch, PREG_SET_ORDER);

			if (empty($objectParMatch)) {
				return array(false, "error");
			}

			foreach ($objectParMatch as $paramData) {
				$param[$paramData[1]] = $paramData[2];
			}

			if (!isset($param["src"])) {
				return array(false, "error");
			}

			$objStr = '<iframe ';
			foreach ($param as $name => $value) {
				$objStr .= $name . '="' . $value . '" ';
			}
			$objStr .= "></iframe>";
			return array(true, $objStr);
		}
		return array(false, "error");
	}
}
