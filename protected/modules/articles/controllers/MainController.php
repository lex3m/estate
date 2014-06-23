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

class MainController extends ModuleUserController {
	public $modelName = 'Article';

	public function init() {
		parent::init();

		$articlePage = Menu::model()->findByPk(Menu::ARTICLES_ID);
		if ($articlePage) {
			if ($articlePage->active == 0) {
				throw404();
			}
		}
	}

	public function actionIndex(){
//		if(Yii::app()->user->getState('isAdmin')){
//			$this->redirect(array('/articles/backend/main/index'));
//		}

		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';

		$pages = new CPagination(Article::model()->count($criteria));
		$pages->pageSize = param('module_articles_itemsPerPage', 10);
		$pages->applyLimit($criteria);

		$articles = Article::model()->cache(param('cachingTime', 1209600), Article::getCacheDependency())->findAll($criteria);

		$this->render('/index',array(
			'articles' => $articles, 'pages' => $pages
		));
	}

	public function actionView($id = 0, $url = ''){
//		if(Yii::app()->user->getState('isAdmin')){
//			$this->redirect(array('/articles/backend/main/view', 'id' => $id));
//		}

		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';

		$articles = Article::model()->cache(param('cachingTime', 1209600), Article::getCacheDependency())->findAll($criteria);

		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}

		$this->render('view',array(
			'model'=>$this->loadModel($id), 'articles' => $articles
		));
	}
}