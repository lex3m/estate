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
class CustomUrlManager extends CUrlManager {

    public function init() {
        $langs = Lang::getActiveLangs();

        $countLangs = count($langs);

        $langRoute = ($countLangs > 1 || ($countLangs == 1 && param('useLangPrefixIfOneLang'))) ? '<lang:'.implode('|',$langs).'>' : '';

        $rules = array(
            'sitemap.xml'=>'sitemap/main/viewxml',
            'yandex_export_feed.xml'=>'yandexRealty/main/viewfeed',

            'version'=>'/site/version',

            'sell'=>'quicksearch/main/mainsearch/type/2',
            'rent'=>'quicksearch/main/mainsearch/type/1',

			'site/uploadimage/' => 'site/uploadimage/',
			$langRoute . '/site/uploadimage/' => 'site/uploadimage/',

			'min/serve/g/' => 'min/serve/',
			$langRoute . '/min/serve/g/' => 'min/serve/',

            '<module:\w+>/backend/<controller:\w+>/<action:\w+>'=>'<module>/backend/<controller>/<action>', // CGridView ajax

            $langRoute . '/property/<id:\d+>'=>'apartments/main/view',
            $langRoute . '/property/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'apartments/main/view',
            $langRoute . '/news'=>'news/main/index',
            $langRoute . '/news/<id:\d+>'=>'news/main/view',
            $langRoute . '/news/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'news/main/view',
            $langRoute . '/faq'=>'articles/main/index',
            $langRoute . '/faq/<id:\d+>'=>'articles/main/view',
            $langRoute . '/faq/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'articles/main/view',
            $langRoute . '/contact-us'=>'contactform/main/index',
            $langRoute . '/specialoffers'=>'specialoffers/main/index',
            $langRoute . '/sitemap'=>'sitemap/main/index',
			$langRoute . '/reviews'=>'reviews/main/index',
			$langRoute . '/reviews/add'=>'reviews/main/add',
			$langRoute . '/guestad/add'=>'guestad/main/create',


			$langRoute . '/page/<id:\d+>'=>'infopages/main/view',
			$langRoute . '/page/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'infopages/main/view',

			$langRoute . '/search' => 'quicksearch/main/mainsearch',
			$langRoute . '/comparisonList' => 'comparisonList/main/index',
			$langRoute . '/complain/add' => 'apartmentsComplain/main/complain',
			$langRoute . '/booking/add' => 'booking/main/bookingform',
			$langRoute . '/booking/request' => 'booking/main/mainform',
			$langRoute . '/usercpanel' => 'usercpanel/main/index',
			$langRoute . '/userads/create' => 'userads/main/create',
			$langRoute . '/userads/edit' => 'userads/main/update',
			$langRoute . '/userads/delete' => 'userads/main/delete',
			$langRoute . '/users/viewall' => 'users/main/search',
			$langRoute . '/users/alllistings' => 'apartments/main/alllistings',
			$langRoute . '/apartments/sendEmail' => 'apartments/main/sendEmail',


            '/rss' => 'quicksearch/main/mainsearch/rss/1',

            $langRoute . '/service-<serviceId:\d+>' => 'quicksearch/main/mainsearch',

            $langRoute . '/<controller:(quicksearch|specialoffers)>/main/index' => '<controller>/main/index',
            $langRoute . '/' => 'site/index',
            $langRoute . '/<_m>/<_c>/<_a>*' => '<_m>/<_c>/<_a>',
            $langRoute . '/<_c>/<_a>*' => '<_c>/<_a>',
            $langRoute . '/<_c>' => '<_c>',

            '/property/'=>'quicksearch/main/mainsearch',
            $langRoute . '/property/'=>'quicksearch/main/mainsearch',

        );

        if($langRoute){
            $rules[$langRoute] = '';
        }

        $this->addRules($rules);

		if(oreInstall::isInstalled()){
			$modules = Yii::app()->getModules();

			$paramModules = ConfigurationModel::getModulesList();
			foreach($paramModules as $module){
				if(isset($modules[$module]) && !param('module_enabled_'.$module)){
					$modules[$module]['enabled'] = false;
				}
			}

			Yii::app()->setModules($modules);
		}
        return parent::init();
    }

    private $parseReady = false;

    public function parseUrl($request)
    {
        if(issetModule('seo') && $this->parseReady === false && oreInstall::isInstalled()){
            if (preg_match('#^([\w-]+)#i', $request->pathInfo, $matches)) {
                $activeLangs = Lang::getActiveLangs();
                $arr = array();
                foreach($activeLangs as $lang){
                    $arr[] = 'url_'.$lang.' = :alias';
                }
                $condition = '('.implode(' OR ', $arr).')';

                $seo = SeoFriendlyUrl::model()->find(array(
                    'condition' => 'direct_url = 1 AND '.$condition,
                    'params' => array('alias'=>$matches[1])
                ));

                if ($seo !== null) {
                    foreach($activeLangs as $lang){
                        $field = 'url_'.$lang;
                        if($seo->$field == $matches[1]){
                            $_GET['lang'] = $lang;
                        }
                    }
                    $_GET['url'] = $matches[1];
                    //Yii::app()->controller->seo = $seo;
                    return 'infopages/main/view';
                }
            }

            $this->parseReady = true;
        }

        return parent::parseUrl($request);
    }

    public function createUrl($route, $params = array(), $ampersand = '&') {
		if ($route != 'min/serve' && $route != 'site/uploadimage') {
			$langs = Lang::getActiveLangs();
			$countLangs = count($langs);

			if (!isFree() && empty($params['lang']) && ($countLangs > 1 || ($countLangs == 1 && param('useLangPrefixIfOneLang')))) {
				$params['lang'] = Yii::app()->language;
			}
		}

        return parent::createUrl($route, $params, $ampersand);
    }
}