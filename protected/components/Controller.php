<?php

/* * ********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 * 	version				:	1.8.1
 * 	copyright			:	(c) 2014 Monoray
 * 	website				:	http://www.monoray.ru/
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

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

	public $layout = '//layouts/index';
	public $infoPages = array();
	public $menuTitle;
	public $menu = array();
	public $breadcrumbs = array();
	public $pageKeywords;
	public $pageDescription;
	public $adminTitle = '';
	public $aData;
	public $modelName;

	public $seoTitle;
	public $seoDescription;
	public $seoKeywords;

	/* advertising */
	public $advertPos1 = array();
	public $advertPos2 = array();
	public $advertPos3 = array();
	public $advertPos4 = array();
	public $advertPos5 = array();
	public $advertPos6 = array();

	public $apInComparison = array();
	public $assetsGenPath;
	public $assetsGenUrl;

	public $maxLengthSearch = 15; // максимальное количество слов во фразе
	public $minLengthSearch = 4; // минимальная длина искомого слова - http://dev.mysql.com/doc/refman/5.0/en/fulltext-fine-tuning.html

    public $showSearchForm = true;

    protected function beforeAction($action) {
		//echo Yii::app()->request->csrfToken;
		Yii::app()->clientScript->registerScript('ajax-csrf', '
			$.ajaxPrefilter(function(options, originalOptions, jqXHR){
				if(originalOptions.type){
					var type = originalOptions.type.toLowerCase();
				} else {
					var type = "";
				}

				if(type == "post" && typeof originalOptions.data === "object"){
					options.data = $.extend(originalOptions.data, { "'.Yii::app()->request->csrfTokenName.'": "'.Yii::app()->request->csrfToken.'" });
					options.data = $.param(options.data);
				}
			});
		', CClientScript::POS_END, array());

		if (!Yii::app()->user->getState('isAdmin')) {
			$currentController = Yii::app()->controller->id;
			$currentAction = Yii::app()->controller->action->id;

			if (!($currentController == 'site' && ($currentAction == 'login' || $currentAction == 'logout'))) {
				if (issetModule('service')){
					$serviceInfo = Service::model()->findByPk(Service::SERVICE_ID);
					if ($serviceInfo && $serviceInfo->is_offline == 1) {
						$allowIps = explode(',', $serviceInfo->allow_ip);
						$allowIps = array_map("trim", $allowIps);

						if (!in_array(Yii::app()->request->userHostAddress, $allowIps)) {
							$this->renderPartial('//../modules/service/views/index', array('page' => $serviceInfo->page), false, true);
							Yii::app()->end();
						}
					}
				}
			}
		}

		/* start  get page banners */
		if (issetModule('advertising') && !param('useBootstrap')) {
			$advert = new Advert;
			$advert->getAdvertContent();
		}
		/* end  get page banners */

		return parent::beforeAction($action);
	}

	function init() {

		if (!oreInstall::isInstalled() && !(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')) {
			$this->redirect(array('/install'));
		}

		setLang();

		$modulesToCheck = ConfigurationModel::getModulesList();
		foreach($modulesToCheck as $module){
			if(param('module_enabled_'.$module) === null){
				ConfigurationModel::createValue('module_enabled_'.$module, 0);
				Yii::app()->params['module_enabled_'.$module] = 0;
			}
		}
		unset($modulesToCheck);

		$this->assetsGenPath = Yii::getPathOfAlias('webroot.assets');
		$this->assetsGenUrl = Yii::app()->getBaseUrl(true).'/assets/';

		Yii::app()->user->setState('menu_active', '');

		if (isFree()) {
			$this->pageTitle = param('siteTitle');
			$this->pageKeywords = param('siteKeywords');
			$this->pageDescription = param('siteDescription');
		}
		else {
			if(issetModule('seo')){
				$this->pageTitle = Seo::getSeoValue('siteName');
				$this->pageKeywords = Seo::getSeoValue('siteKeywords');
				$this->pageDescription = Seo::getSeoValue('siteDescription');
			}
			else {
				$this->pageTitle = tt('siteName', 'seo');
				$this->pageKeywords = tt('siteKeywords', 'seo');
				$this->pageDescription = tt('siteDescription', 'seo');
			}
		}

		Yii::app()->name = $this->pageTitle;

		if(Yii::app()->getModule('menumanager')){
			if(!(Yii::app()->controller->module && Yii::app()->controller->module->id == 'install')){
				$this->infoPages = Menu::getMenuItems(0);
			}
		}


        if(!Yii::app()->user->isGuest && !Yii::app()->user->getState('isAdmin')){
            $subItems = HUser::getMenu();
		} else {
            $subItems = array();
        }

        $urlAddAd = (Yii::app()->user->isGuest && issetModule('guestad')) ? array('/guestad/main/create') : array('/userads/main/create');

		$this->aData['userCpanelItems'] = array(
			array(
				'label' => tt('Add ad', 'common'),
				'url' => $urlAddAd,
				//'visible' => param('useUserads', 0) == 1
                'visible' =>  Yii::app()->user->isGuest == false
			),
			array(
				'label' => '|',
				'visible' => param('useUserads', 0) == 1
			),
            array('label' => tt('Special offers', 'common'), 'url' => array('/specialoffers/main/index')),
            array('label' => tt('Search for listings on the map', 'common'), 'url' => array('/page/2')),
			array('label' => tt('Contact us', 'common'), 'url' => array('/contactform/main/index')),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null),
			array(
				'label' => tt('Reserve apartment', 'common'),
				'url' => array('/booking/main/mainform'),
				'visible' => Yii::app()->user->getState('isAdmin') === null,
				'linkOptions' => array('class' => 'fancy'),
			),
			array('label' => '|', 'visible' => Yii::app()->user->getState('isAdmin') === null),
			array(
				'label' => Yii::t('common', 'Control panel'),
				'url' => array('/usercpanel/main/index'),
				'visible' => Yii::app()->user->getState('isAdmin') === null,
				'items' => $subItems,
				'submenuOptions'=>array(
					'class'=>'sub_menu_dropdown'
				),
			),
		);

        if(!Yii::app()->user->isGuest){
            $user = HUser::getModel();
            $this->aData['userCpanelItems'][] = array('label' => '|');
            $this->aData['userCpanelItems'][] = array('label' => '(' . $user->username . ') ' . tt('Logout', 'common'), 'url' => array('/site/logout'));
        }

		$this->aData['topMenuItems'] = $this->infoPages;

		// comparison list
		if (issetModule('comparisonList')) {
			if (!Yii::app()->user->isGuest) {
				$resultCompare = ComparisonList::model()->findAllByAttributes(
					array(
						'user_id' => Yii::app()->user->id,
					)
				);
			}
			else {
				$resultCompare = ComparisonList::model()->findAllByAttributes(
					array(
						'session_id' => Yii::app()->session->sessionId,
					)
				);
			}

			if ($resultCompare) {
				foreach($resultCompare as $item) {
					$this->apInComparison[] = $item->apartment_id;
				}
			}
		}

		parent::init();
	}

	public static function disableProfiler() {
		if (Yii::app()->getComponent('log')) {
			foreach (Yii::app()->getComponent('log')->routes as $route) {
				if (in_array(get_class($route), array('CProfileLogRoute', 'CWebLogRoute', 'YiiDebugToolbarRoute'))) {
					$route->enabled = false;
				}
			}
		}
	}

	public function createLangUrl($lang='en', $params = array()){
		$langs = Lang::getActiveLangs();

		if(count($langs) > 1 && issetModule('seo') && isset(SeoFriendlyUrl::$seoLangUrls[$lang])){
			if (count($params))
				return SeoFriendlyUrl::$seoLangUrls[$lang].'?'.http_build_query($params);

			return SeoFriendlyUrl::$seoLangUrls[$lang];
		}

		$route = Yii::app()->urlManager->parseUrl(Yii::app()->getRequest());
		$params = array_merge($_GET, $params);
		$params['lang'] = $lang;
		return $this->createUrl('/'.$route, $params);
	}

	public function excludeJs(){
		//Yii::app()->clientscript->scriptMap['*.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;
		Yii::app()->clientscript->scriptMap['bootstrap.min.js'] = false;
		Yii::app()->clientscript->scriptMap['jquery-ui-i18n.min.js'] = false;
	}

	public static function getCurrentRoute(){
		$moduleId = isset(Yii::app()->controller->module) ? Yii::app()->controller->module->id.'/' : '';
		return trim($moduleId.Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId());
	}

	public function setSeo(SeoFriendlyUrl $seo){
		$this->seoTitle = $seo->getStrByLang('title');
		$this->seoDescription = $seo->getStrByLang('description');
		$this->seoKeywords = $seo->getStrByLang('keywords');
	}

	public function actionDeleteVideo($id = null, $apId = null) {
		if (Yii::app()->user->isGuest)
			throw404();

		if (!$id && !$apId)
			throw404();

		if (Yii::app()->user->getState('isAdmin')) {
			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			$this->redirect(array('/apartments/backend/main/update', 'id' => $apId));
		}
		else {
			$modelApartment = Apartment::model()->findByPk($apId);
			if($modelApartment->owner_id != Yii::app()->user->id){
				throw404();
			}

			$modelVideo = ApartmentVideo::model()->findByPk($id);
			$modelVideo->delete();

			$this->redirect(array('/userads/main/update', 'id' => $apId));
		}
	}
	public function actionDeletePanorama($id = null, $apId = null) {
		if (Yii::app()->user->isGuest)
			throw404();

		if (!$id && !$apId)
			throw404();

		if (Yii::app()->user->getState('isAdmin')) {
			$modelPanorama = ApartmentPanorama::model()->findByPk($id);
			$modelPanorama->delete();

			$this->redirect(array('/apartments/backend/main/update', 'id' => $apId));
		}
		else {
			$modelApartment = Apartment::model()->findByPk($apId);
			if($modelApartment->owner_id != Yii::app()->user->id){
				throw404();
			}

			$modelPanorama = ApartmentPanorama::model()->findByPk($id);
			$modelPanorama->delete();

			$this->redirect(array('/userads/main/update', 'id' => $apId));
		}
	}

	public static function returnBookingTableStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		$statuses = Bookingtable::getAllStatuses();

		$items = CJavaScript::encode($statuses);

		$options = array(
			'onclick' => 'ajaxSetBookingTableStatus(this, "'.$tableId.'", "'.$data->id.'", "'.$items.'"); return false;',
		);

		return '<div align="center" class="editable_select" id="editable_select-'.$data->id.'">'.CHtml::link($statuses[$data->active], '#' , $options).'</div>';
	}

	public function actionBookingTableActivate(){
		$field = isset($_GET['field']) ? $_GET['field'] : 'active';

		if (Yii::app()->request->getParam('id') && (Yii::app()->request->getParam('value') != null)) {
			$this->scenario = 'update_status';
			$action = Yii::app()->request->getParam('value', null);
			$id = Yii::app()->request->getParam('id', null);
			$availableStatuses = Bookingtable::getAllStatuses();

			if (!array_key_exists($action, $availableStatuses)) {
				$action = 0;
			}
		}

		if(!(!$id && $action === null)){
			$model = $this->loadModelUserBookingTable($id);

			if($this->scenario){
				$model->scenario = $this->scenario;
			}

			if($model){
				$model->$field = $action;
				$model->save(false);

				if (issetModule('bookingcalendar')) {
					if ($field == 'active' && $action == Bookingtable::STATUS_CONFIRM) {
						$modelBookingCalendar = new Bookingcalendar;

						$modelBookingCalendar->date_start = $model->date_start;
						$modelBookingCalendar->date_end = $model->date_end;
						$modelBookingCalendar->status = Bookingcalendar::STATUS_BUSY;
						$modelBookingCalendar->apartment_id = $model->apartment_id;
						$modelBookingCalendar->save(false);
					}
				}
			}
		}

		echo CHtml::link($availableStatuses[$action]);
	}

	public function loadModelUserBookingTable($id) {
		$model = $this->loadModel($id);

		if (!Yii::app()->user->getState('isAdmin')) {
			$sql = 'SELECT id FROM {{apartment}} WHERE owner_id = "'.Yii::app()->user->id.'" ';
			$apIds = Yii::app()->db->createCommand($sql)->queryColumn();

			if(!in_array($model->apartment_id, $apIds)) {
				throw404();
			}
		}

		return $model;
	}

    public function setActiveMenu($key, $pos = 'cpanel'){
        $this->aData[$pos] = array();
        $this->aData[$pos][$key] = true;
    }

    public function menuIsActive($key, $pos = 'cpanel'){
        return isset($this->aData[$pos][$key]) && $this->aData[$pos][$key] === true;
    }
}