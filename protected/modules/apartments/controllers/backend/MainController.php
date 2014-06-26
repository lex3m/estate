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

class MainController extends ModuleAdminController {

	public $modelName = 'Apartment';

	public function actionView($id = 0) {
		//$this->layout='//layouts/inner';

		Yii::app()->bootstrap->plugins['tooltip'] = array(
			'selector'=>' ', // bind the plugin tooltip to anchor tags with the 'tooltip' class
			'options'=>array(
				'placement'=>'top', // place the tooltips below instead
			),
		);

		$model = $this->loadModelWith(array('windowTo', 'objType', 'city'));

		if (!in_array($model->type, Apartment::availableApTypesIds())) {
			throw404();
		}

		$this->render('view', array(
			'model' => $model,
			'statistics' => Apartment::getApartmentVisitCount($model),
		));
	}

    public function actionAdmin(){

        $countNewsProduct = NewsProduct::getCountNoShow();
        if($countNewsProduct > 0){
            Yii::app()->user->setFlash('info', Yii::t('common', 'There are new product news') . ': '
                . CHtml::link(Yii::t('common', '{n} news', $countNewsProduct), array('/news/backend/main/product')));
        }

		$this->rememberPage();

		$this->getMaxSorter();

		$model = new Apartment('search');
		$model = $model->with(array('user'));

		$this->render('admin',array_merge(array('model'=>$model), $this->params));


    }

	public function actionUpdate($id){
        $this->_model = $this->loadModel($id);

        $old_price = $this->_model->price;

        if(!$this->_model){
            throw404();
        }

        $oldStatus = $this->_model->active;

        if(issetModule('bookingcalendar')) {
			$this->_model = $this->_model->with(array('bookingCalendar'));
		}
        if(isset($_GET['type'])){
            $type = self::getReqType();

            $this->_model->type = $type;
        }

		if(isset($_POST[$this->modelName])){
			$this->_model->attributes = $_POST[$this->modelName];

			if ($this->_model->type != Apartment::TYPE_BUY && $this->_model->type != Apartment::TYPE_RENTING) {
				// video
				$videoFileValidate = true;
				if((isset($_FILES[$this->modelName]['name']['video_file']) && $_FILES[$this->modelName]['name']['video_file'])){
					$this->_model->scenario = 'video_file';
					if ($this->_model->validate()) {
						$this->_model->videoUpload = CUploadedFile::getInstance($this->_model, 'video_file');
						$videoFile = md5(uniqid()).'.'.$this->_model->videoUpload->extensionName;
						$pathVideo = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$id;

						if (newFolder($pathVideo)) {
							$this->_model->videoUpload->saveAs($pathVideo.'/'.$videoFile);

							$sql = 'INSERT INTO {{apartment_video}} (apartment_id, video_file, 	video_html, date_updated)
								VALUES ("'.$id.'", "'.$videoFile.'", "", NOW())';
							Yii::app()->db->createCommand($sql)->execute();
						}
						else {
							Yii::app()->user->setFlash('error', tt('not_create_folder_to_save.', 'apartments'));
							$this->redirect(array('update', 'id' => $id));
						}
					}
					else {
						$videoFileValidate = false;
					}
				}

				if ($videoFileValidate) {
					// html code
					$videoHtmlValidate = true;
					if (isset($_POST[$this->modelName]['video_html']) && $_POST[$this->modelName]['video_html']) {
						$this->_model->video_html = $_POST[$this->modelName]['video_html'];
						$this->_model->scenario = 'video_html';
						if ($this->_model->validate()) {
							$sql = 'INSERT INTO {{apartment_video}} (apartment_id, video_file, 	video_html, date_updated)
								VALUES ("'.$id.'", "", "'.CHtml::encode($this->_model->video_html).'", NOW())';
							Yii::app()->db->createCommand($sql)->execute();
						}
						else {
							$videoHtmlValidate = false;
						}
					}
				}

				if ($videoFileValidate && $videoHtmlValidate) {
					$panoramaValidate = true;
					$this->_model->panoramaFile = CUploadedFile::getInstance($this->_model, 'panoramaFile');

					$this->_model->scenario = 'panorama';
					if(!$this->_model->validate()) {
						$panoramaValidate = false;
					}
				}

				$city = "";
				if (issetModule('location') && param('useLocation', 1)) {
					$city .= $this->_model->locCountry ? $this->_model->locCountry->getStrByLang('name') : "";
					$city .= ($city && $this->_model->locCity) ? ", " : "";
					$city .= $this->_model->locCity ? $this->_model->locCity->getStrByLang('name') : "";
				} else
					$city = $this->_model->city ? $this->_model->city->getStrByLang('name') : "";

				// data
				if ($videoFileValidate && $videoHtmlValidate && $panoramaValidate) {
					if(($this->_model->address && $city) && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))){
						if (!$this->_model->lat && !$this->_model->lng) { # уже есть

							$coords = Geocoding::getCoordsByAddress($this->_model->address, $city);

							if(isset($coords['lat']) && isset($coords['lng'])){
								$this->_model->lat = $coords['lat'];
								$this->_model->lng = $coords['lng'];
							}
						}
					}
				}
			}

			$this->_model->scenario = 'savecat';

			$isUpdate = Yii::app()->request->getPost('is_update');
            if (isset($_POST['Apartment']['price_old_new']))
            {
                if ($this->_model->price_old!=$_POST['Apartment']['price_old_new'])
                    $this->_model->price_old =  $_POST['Apartment']['price_old_new'];
                else
                    if ($old_price!= $this->_model->price)
                        $this->_model->price_old = $old_price;
            }

            //error - Invalid datetime format: 1292 Incorrect date value: '' for column 'is_free_from'
            $this->_model->is_free_from = "0000-00-00";
            $this->_model->is_free_to = "0000-00-00";

			if($isUpdate){
				$this->_model->active = $oldStatus;
				$this->_model->save(false);
			} elseif($this->_model->validate()) {
				$this->_model->save(false);
				$this->redirect(array('view','id'=>$this->_model->id));
			}
		}

        $this->_model->getCategoriesForUpdate();

        if($this->_model->active == Apartment::STATUS_DRAFT){
			Yii::app()->user->setState('menu_active', 'apartments.create');
			$this->render('create', array(
				'model' => $this->_model,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			));
			return;
		}

		$this->render('update', array(
			'model' => $this->_model,
			'supportvideoext' => ApartmentVideo::model()->supportExt,
			'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
		));
	}

    private static function getReqType(){
        $type = Yii::app()->getRequest()->getQuery('type');
        $existType = array_keys(Apartment::getTypesArray());
        if(!in_array($type, $existType)){
            $type = Apartment::TYPE_DEFAULT;
        }
        return $type;
    }

	public function actionCreate(){
		$model = new $this->modelName;
		$model->active = Apartment::STATUS_DRAFT;
        $model->setDefaultType();
		$model->save(false);

		$this->redirect(array('update', 'id' => $model->id));
	}

	public function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = floatval($_POST['lat']);
				$apartment->lng = floatval($_POST['lng']);
				$apartment->update(array('lat', 'lng'));
			}
			Yii::app()->end();
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true), true);

		if($result){
			return $this->renderPartial('_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){

		if($model === null){
			$model = $this->loadModel($id);
		}

		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionOSmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomOSMap::actionOSmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('_osmap', $result, true);
		}
		return '';
	}
}