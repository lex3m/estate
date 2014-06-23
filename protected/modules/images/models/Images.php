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

class Images extends ParentModel {
	const EMPTY_IMG = 'no_photo_img.png';

	const KEEP_THUMB_PROPORTIONAL = true;
	const KEEP_PHOTO_PROPORTIONAL = true;

	const UPLOAD_DIR = 'uploads';
	const OBJECTS_DIR = 'objects';

	const MODIFIED_IMG_DIR = 'modified';
	const ORIGINAL_IMG_DIR = 'original';

	const WATERMARK_OFFSET_X = 0;
	const WATERMARK_OFFSET_Y = 0;

	const TEXT_WATERMARK_OFFSET_X = 20;
	const TEXT_WATERMARK_OFFSET_Y = 20;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{images}}';
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

	public function beforeSave(){
		if($this->isNewRecord){
            //default value
            $this->file_name_modified='';
            $this->comment='';

            $dateTime = date('Y-m-d H:i:s');
            $this->date_updated = $dateTime;

            $this->is_main = 0;

            $sql = 'SELECT MAX(sorter) FROM {{images}} WHERE id_object=:id';
			$this->sorter = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $this->id_object)) + 1;

			$sql = 'SELECT COUNT(*) FROM {{images}} WHERE id_object=:id AND is_main=1';
			$main = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $this->id_object));
			if($main == 0){
				$this->is_main = 1;
			}
		}

		return parent::beforeSave();
	}

	public function afterDelete(){
		if($this->is_main){
			$sql = 'UPDATE {{images}} SET is_main=1 WHERE id_object=:id LIMIT 1';
			Yii::app()->db->createCommand($sql)->execute(array(':id' => $this->id_object));
		}

		$names = array(
			'thumb_*x*_'.$this->file_name_modified,
			'full_'.$this->file_name_modified,
		);

		foreach($names as $name){
			$mask = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
				self::UPLOAD_DIR.DIRECTORY_SEPARATOR.
				self::OBJECTS_DIR.DIRECTORY_SEPARATOR.
				$this->id_object.DIRECTORY_SEPARATOR.
				self::MODIFIED_IMG_DIR.DIRECTORY_SEPARATOR.$name;
			@array_map( "unlink", glob( $mask ) );
		}

		@unlink(Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
			self::UPLOAD_DIR.DIRECTORY_SEPARATOR.
			self::OBJECTS_DIR.DIRECTORY_SEPARATOR.
			$this->id_object.DIRECTORY_SEPARATOR.
			self::ORIGINAL_IMG_DIR.DIRECTORY_SEPARATOR.$this->file_name);

		return parent::afterDelete();
	}


	#########################################################
	// manipulation with images
	#########################################################

	public static function createSimple($img, $originalPath, $newPath){
		$image = new CImageHandler();
		if($image->load($originalPath)){
			$image->save($newPath);
			return self::returnFullSizeUrl($img);
		} else {
			return '';
		}

	}

	public static function createFullSize($image){
		$newPath = self::returnModifiedFullImgPath($image);
		$originalPath = self::returnOrigPath($image);

		if(!file_exists($originalPath)){
			return '';
		}

		if(!param('useWatermark')){
			return self::createSimple($image, $originalPath, $newPath);
		} else {
			// using watermark

			$positionTransform = array(
				ImageSettings::POS_LEFT_TOP => CImageHandler::CORNER_LEFT_TOP,
				ImageSettings::POS_LEFT_MIDDLE => CImageHandler::CORNER_LEFT_CENTER,
				ImageSettings::POS_LEFT_BOTTOM => CImageHandler::CORNER_LEFT_BOTTOM,
				ImageSettings::POS_CENTER_TOP => CImageHandler::CORNER_CENTER_TOP,
				ImageSettings::POS_CENTER_MIDDLE => CImageHandler::CORNER_CENTER,
				ImageSettings::POS_CENTER_BOTTOM => CImageHandler::CORNER_CENTER_BOTTOM,
				ImageSettings::POS_RIGHT_TOP => CImageHandler::CORNER_RIGHT_TOP,
				ImageSettings::POS_RIGHT_MIDDLE => CImageHandler::CORNER_RIGHT_CENTER,
				ImageSettings::POS_RIGHT_BOTTOM => CImageHandler::CORNER_RIGHT_BOTTOM,
			);

			if(param('watermarkType') == ImageSettings::WATERMARK_FILE){
				$waterMarkFileName = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
					self::UPLOAD_DIR.DIRECTORY_SEPARATOR.
					param('watermarkFile');

				if(!file_exists($waterMarkFileName)){
					return self::createSimple($image, $originalPath, $newPath);
				}

				$img = new CImageHandler();
				if(!$img->load($originalPath)){
					return '';
				}

				$img->watermark($waterMarkFileName, self::WATERMARK_OFFSET_X, self::WATERMARK_OFFSET_Y,
					$positionTransform[param('watermarkPosition', ImageSettings::POS_RIGHT_BOTTOM)]);
				$img->save($newPath);

				return self::returnFullSizeUrl($image);
			}

			if(param('watermarkType') == ImageSettings::WATERMARK_TEXT){
				// apply text to image

				$img = new CImageHandler();

				$img->load($originalPath);

				$font = Yii::getPathOfAlias('application.modules.images.fonts').DIRECTORY_SEPARATOR.'Verdana.ttf';

				$textColor = param('watermarkTextColor');
				$textColor = str_replace('#', '', $textColor);
				$color = array(hexdec(substr($textColor, 0, 2)), hexdec(substr($textColor, 2, 2)), hexdec(substr($textColor, 4, 2)));

				// alpha between 0 and 127
				$alpha = (100 - param('watermarkTextOpacity', 0));
				$alpha = ($alpha/100) * 127;

				$img->text(param('watermarkContent'), $font, param('watermarkTextSize'), $color,
					$positionTransform[param('watermarkPosition', ImageSettings::POS_RIGHT_BOTTOM)],
					self::TEXT_WATERMARK_OFFSET_X, self::TEXT_WATERMARK_OFFSET_Y, 0, $alpha);

				$img->save($newPath);
				return self::returnFullSizeUrl($image);
			}

		}
		return '';
	}

	public static function updateModifiedName($image){
		$ext = pathinfo($image['file_name'], PATHINFO_EXTENSION);
		$name = md5(time().uniqid()).'.'.$ext;

		$sql = 'UPDATE {{images}} SET file_name_modified=:file WHERE id=:id';
		Yii::app()->db->createCommand($sql)->execute(array(
			':file' => $name,
			':id' => $image['id'],
		));
		return $name;
	}

	public static function getFullSizeUrl($image){
		if($image['file_name_modified']){
			$modifiedFileName = self::returnModifiedFullImgPath($image);
			if(file_exists($modifiedFileName)){
				return self::returnFullSizeUrl($image);
			} else {
				return self::createFullSize($image);
			}
		} else {
			$image['file_name_modified'] = self::updateModifiedName($image);
			return self::createFullSize($image);
		}
	}

	public static function createThumb($image, $width, $height){
		$newPath = self::returnModifiedThumbPath($image, $width, $height);
		$originalPath = self::returnOrigPath($image);
		if(!file_exists($originalPath)){
			return '';
		}

		$thumb = new CImageHandler();
		if($thumb->load($originalPath)){
			$thumb->thumb($width, $height, self::KEEP_THUMB_PROPORTIONAL)
				->save($newPath);
			return self::returnThumbUrl($image, $width, $height);
		} else {
			return '';
		}
	}

	public static function returnThumbUrl($image, $width, $height){
		return Yii::app()->request->getBaseUrl().'/'.self::UPLOAD_DIR.'/'.self::OBJECTS_DIR.'/'.$image['id_object']
			.'/'.self::MODIFIED_IMG_DIR.'/thumb_'.$width.'x'.$height.'_'.$image['file_name_modified'];
	}

	public static function returnFullSizeUrl($image){
		return Yii::app()->request->getBaseUrl().'/'.self::UPLOAD_DIR.'/'.self::OBJECTS_DIR.'/'.$image['id_object']
			.'/'.self::MODIFIED_IMG_DIR.'/full_'.$image['file_name_modified'];
	}

	public static function returnModifiedThumbPath($image, $width, $height){
		return Yii::getPathOfAlias('webroot')
			.DIRECTORY_SEPARATOR.self::UPLOAD_DIR.DIRECTORY_SEPARATOR.self::OBJECTS_DIR
			.DIRECTORY_SEPARATOR.$image['id_object'].DIRECTORY_SEPARATOR.self::MODIFIED_IMG_DIR
			.DIRECTORY_SEPARATOR.'thumb_'.$width.'x'.$height.'_'.$image['file_name_modified'];
	}

	public static function returnModifiedFullImgPath($image){
		return Yii::getPathOfAlias('webroot')
			.DIRECTORY_SEPARATOR.self::UPLOAD_DIR.DIRECTORY_SEPARATOR.self::OBJECTS_DIR
			.DIRECTORY_SEPARATOR.$image['id_object'].DIRECTORY_SEPARATOR.self::MODIFIED_IMG_DIR
			.DIRECTORY_SEPARATOR.'full_'.$image['file_name_modified'];
	}

	public static function returnOrigPath($image){
		return Yii::getPathOfAlias('webroot')
			.DIRECTORY_SEPARATOR.self::UPLOAD_DIR.DIRECTORY_SEPARATOR.self::OBJECTS_DIR
			.DIRECTORY_SEPARATOR.$image['id_object'].DIRECTORY_SEPARATOR.self::ORIGINAL_IMG_DIR
			.DIRECTORY_SEPARATOR.$image['file_name'];
	}


	public static function getThumbUrl($image, $width=0, $height=0){
		if($image['file_name_modified']){
			$modifiedFile = self::returnModifiedThumbPath($image, $width, $height);

			if(file_exists($modifiedFile)){
				return self::returnThumbUrl($image, $width, $height);
			} else {
				return self::createThumb($image, $width, $height);
			}
		} else {
			$image['file_name_modified'] = self::updateModifiedName($image);
			return self::createThumb($image, $width, $height);
		}
	}

	public static function getAlt($image){
		if(isset($image['comment'])){
			return $image['comment'];
		} else {
			return '';
		}
	}

	public static function saveComments($model){
		if(!Yii::app()->user->getState('isAdmin') && Yii::app()->user->id != $model->owner_id){
			return;
		}

		$sql = 'SELECT id FROM {{images}} WHERE id_object=:id';
		$ids = Yii::app()->db->createCommand($sql)->queryColumn(array(':id' => $model->id));

		if($ids){
			$comments = Yii::app()->request->getPost('photo_comment');
			foreach($ids as $id){

				if(isset($comments[$id])){
					$sql = 'UPDATE {{images}} SET comment=:comment WHERE id=:id AND id_object=:idObject';
					Yii::app()->db->createCommand($sql)->execute(array(
						':id' => $id,
						':comment' => $comments[$id],
						':idObject' => $model->id,
					));
				}
			}
		}
	}

	public static function returnEmptyImgUrl($width, $height){
		$uploadPath = Yii::getPathOfAlias('webroot.'.self::UPLOAD_DIR);
		$fileName = $width.'x'.$height.'_'.self::EMPTY_IMG;
		if(file_exists($uploadPath.DIRECTORY_SEPARATOR.$fileName)){
			return Yii::app()->request->getBaseUrl().'/'.self::UPLOAD_DIR.'/'.$fileName;
		} else {
			$origFileName = self::EMPTY_IMG;
			if(file_exists($uploadPath.DIRECTORY_SEPARATOR.$origFileName)){
				$img = new CImageHandler();
				if(!$img->load($uploadPath.DIRECTORY_SEPARATOR.$origFileName)){
					return '';
				}
				$img->thumb($width, $height, self::KEEP_THUMB_PROPORTIONAL)
					->save($uploadPath.DIRECTORY_SEPARATOR.$fileName);
				return Yii::app()->request->getBaseUrl().'/'.self::UPLOAD_DIR.'/'.$fileName;
			} else {
				return '';
			}
		}
	}

	public static function getMainThumb($width, $height, $images, $id = null){
		$return = array(
			'link' => '',
			'thumbUrl' => '',
			'comment' => '',
		);

		$image = null;
		if($id !== null){
			$sql = 'SELECT comment, file_name, file_name_modified, id FROM {{images}} WHERE id_object=:id AND is_main=1';
			$image = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $id));
		}

		if($images){
			foreach($images as $img){
				if($img['is_main']){
					$image = $img;
					break;
				}
			}
		}

		if($image){
			$return['thumbUrl'] = self::getThumbUrl($image, $width, $height);
			$return['comment'] = $image['comment'];
			$return['link'] = self::getFullSizeUrl($image);
		} else {
			$return['thumbUrl'] = self::returnEmptyImgUrl($width, $height);
		}
		return $return;
	}

	public static function getObjectThumbs($width, $height, $images, $id = null, $withMain = 0){
		$retImages = null;

		if($id !== null){
			$add = $withMain ? '' : ' AND is_main=0 ';
			$sql = 'SELECT comment, file_name, file_name_modified, id FROM {{images}} WHERE id_object=:id'.$add;
			$retImages = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $id));
		}

		if($images){
			foreach($images as $img){
				$image = array();
				if($withMain && $img['is_main'] || !$withMain && !$img['is_main']){
					$image['comment'] = $img['comment'];
					$image['thumbUrl'] = self::getThumbUrl($img, $width, $height);

					$retImages[] = $image;
				}
			}
		}
		return $retImages;
	}

	public static function addImage($filePath, $objectId, $isMain, $ownerId){
		$path = Yii::getPathOfAlias('webroot.uploads.objects.'.$objectId.'.'.Images::ORIGINAL_IMG_DIR);
		$pathMod = Yii::getPathOfAlias('webroot.uploads.objects.'.$objectId.'.'.Images::MODIFIED_IMG_DIR);

		$oldUMask = umask(0);
		if(!is_dir($path)){
			@mkdir($path, 0777, true);
		}
		if(!is_dir($pathMod)){
			@mkdir($pathMod, 0777, true);
		}
		umask($oldUMask);

		if(is_writable($path) && is_writable($pathMod)){
			touch($path.DIRECTORY_SEPARATOR.'index.htm');
			touch($pathMod.DIRECTORY_SEPARATOR.'index.htm');


			$ext = $ext = pathinfo($filePath, PATHINFO_EXTENSION);

			$mewFName = md5($filePath).'.'.$ext;
			$newFilePath = $path.DIRECTORY_SEPARATOR.$mewFName;

			$resize = new CImageHandler();

			echo $filePath.'<br/>';

			if($resize->load($filePath)){
				$resize->thumb(param('maxImageWidth', 1024), param('maxImageHeight', 768), Images::KEEP_PHOTO_PROPORTIONAL)
					->save($newFilePath);

				$image = new Images();
				$image->id_object = $objectId;
				$image->id_owner = $ownerId;
				$image->is_main = $isMain;
				$image->file_name = $mewFName;

				$image->save();
			} else {
				echo $newFilePath.': Wrong image type.<br/>';
				@unlink($newFilePath);
			}
		}
	}

	public static function deleteByObjectId($model){
		$images = self::model()->findAllByAttributes(array('id_object' => $model->id));
		if($images){
			foreach($images as $image){
				$image->delete();
			}
		}
	}

	public static function deleteDbByObjectId($objId){
		$sql = 'DELETE FROM {{images}} WHERE id_object=:id';
		Yii::app()->db->createCommand($sql)->execute(array(':id' => $objId));
	}

	public static function getMainImageData($images, $id = null){
		$image = null;
		if($id !== null){
			$sql = 'SELECT comment, file_name, file_name_modified, id FROM {{images}} WHERE id_object=:id AND is_main=1';
			$image = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $id));
		}

		if($images){
			foreach($images as $img){
				if($img['is_main']){
					$image = $img;
					break;
				}
			}
		}

		return $image;
	}

	public static function getApartmentsCountImages($ids = array()) {
		$sql = 'SELECT id_object, COUNT(id) as count FROM {{images}} WHERE id_object IN ('.implode(',', $ids).') GROUP BY id_object';
		$res = Yii::app()->db->createCommand($sql)->queryAll();

		return CHtml::listData($res, 'id_object', 'count');
	}

	public static function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	public static function getMaxSizeLimit(){
		$min = min(self::toBytes(ini_get('post_max_size')), self::toBytes(ini_get('upload_max_filesize')));
		return min($min, param('maxImgFileSize', 8 * 1024 * 1024));
	}

}