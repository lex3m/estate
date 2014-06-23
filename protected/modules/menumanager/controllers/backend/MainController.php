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

class MainController extends ModuleAdminController{
	public $modelName = 'Menu';

	public function actionView($id){
		if($id == Menu::NEWS_ID){
			$this->redirect(array('/news/main/index'));
		}
		if($id == Menu::SPECIALOFFERS_ID){
			$this->redirect(array('/specialoffers/main/index'));
		}
		if($id == Menu::ARTICLES_ID){
			$this->redirect(array('/articles/backend/main/index'));
		}
		if($id == Menu::SITEMAP_ID){
			$this->redirect(array('/sitemap/main/index'));
		}
		if($id == Menu::REVIEWS_ID){
			$this->redirect(array('/reviews/main/index'));
		}

		$model = $this->loadModel($id);
		/*if ($model->active == 0)
			throw404();*/

		$this->render('view',array(
			'model' => $model,
		));
	}

	public function actionIndex(){
		$this->redirect(array('admin'));
	}

	public function actionAdmin(){
		$this->scenario = 'create';
		parent::actionAdmin();
	}

	public function actionGetPageList(){
		echo json_encode(array(
			'data' => tt('Main menu'),
			'state' => 'open',
			'attr' => array('rel' => 'root', 'pid' => 0, 'special' => 0),
			'children' => Menu::buildTreePages(0),
		));
	}

	public function actionSetVisible(){
		$item = Menu::model()->findByPk($_POST['pid']);
		if($item===null)
			throw404();

		$item->setVisible($_POST['visible']);
	}


	public function actionRename(){
		$item = Menu::model()->findByPk($_POST['pid']);
		if($item===null)
			throw404();

		$item->rename($_POST['title']);
	}

	public function actionMove(){
		$item = Menu::model()->findByPk($_POST['pid']);
		if($item===null)
			throw404();

		$item->move($_POST['ref'], $_POST['pos']);
	}

	public function actionDeleteItem(){
		if(Yii::app()->request->isPostRequest){
			$fromMenu = false;
			if (isset($_POST) && $_POST && array_key_exists('pid', $_POST)) {
				$fromMenu = true;
				$item = Menu::model()->findByPk($_POST['pid']);
			}
			else
				$item = Menu::model()->findByPk(Yii::app()->request->getParam('id'));

			if($item===null)
				throw404();

			if ($item->special == 1)
				throw new CHttpException(403, tt('backend_menumanager_main_admin_noDeleteSystemItem', 'menumanager'));

			$item->deleteBranch();

			if (!$fromMenu)
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionCreate(){
		echo Menu::create($_POST)->id;
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		/*// подставляем урл, если не задан вручную
		if (!$model->seo_link && $model->title) {
			if (isset($model->parent) && $model->parent) { # есть родитель
				if (isset($model->parent->seo_link) && $model->parent->seo_link) {
					$model->seo_link = $model->parent->seo_link.'/'.translit(mb_strtolower($model->title, 'utf8'));
				}
				elseif (isset($model->parent->title) && $model->parent->title) {
					$model->seo_link = translit(mb_strtolower($model->parent->title, 'utf8')).'/'.translit(mb_strtolower($model->title, 'utf8'));
				}
			}
		}*/

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes = $_POST[$this->modelName];
			$model->scenario = 'link_'.$model->type;

			if($model->special){
				$model->scenario = 'special';
			}

			if($model->save()){
				$this->redirect(array('admin'));
			}
		}

		$this->render('update',
			array('model'=>$model)
		);
	}
}
