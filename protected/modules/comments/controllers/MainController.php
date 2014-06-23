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

class MainController extends ModuleUserController{
	public $modelName = 'Comment';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionWriteComment(){
		$model = new CommentForm();

		if(isset($_POST['CommentForm'])){
			$model->attributes=$_POST['CommentForm'];
			$model->defineShowRating();
			if($model->validate() && Comment::checkExist(null, $model->modelName, $model->modelId)){

				if(
					($model->modelName == 'News' && !param('enableCommentsForNews', 1))
					|| ($model->modelName == 'Apartment' && !param('enableCommentsForApartments', 1))
					|| ($model->modelName == 'Menu' && !param('enableCommentsForPages', 0))
					|| ($model->modelName == 'Article' && !param('enableCommentsForFaq', 1))
					|| ($model->modelName == 'InfoPages' && !param('enableCommentsForPages', 0))
				 ){
					throw404();
				}

				$comment = new Comment();
				$comment->body = $model->body;
				$comment->parent_id = $model->rel;

				if($model->rel == 0){
					$comment->rating = $model->rating;
				} else {
					$comment->rating = -1;
				}

				$comment->model_name = $model->modelName;
				$comment->model_id = $model->modelId;

				if(Yii::app()->user->isGuest){
					$comment->user_name = $model->user_name;
					$comment->user_email = $model->user_email;
				} else {
					$comment->owner_id = Yii::app()->user->id;
				}

				if(param('commentNeedApproval', 1) && !Yii::app()->user->hasState('isAdmin')){
					$comment->status = Comment::STATUS_PENDING;
					Yii::app()->user->setFlash('success', Yii::t('module_comments','Thank you for your comment. Your comment will be posted once it is approved.'));
				} else {
					$comment->status = Comment::STATUS_APPROVED;
					Yii::app()->user->setFlash('success', Yii::t('module_comments','Thank you for your comment.'));
				}
				$comment->save(false);

				$this->redirect($model->url);
			}
		}

		$this->render('commentForm', array('model' => $model));
	}

	public function actionDeleteComment(){
		$return['status'] = 0;

		$id = Yii::app()->request->getPost('id');

		$model = $this->loadModel($id);
		if(!$model || ($model->owner_id != Yii::app()->user->id && !Yii::app()->user->hasState('isAdmin'))){
			$return['message'] = tt('commentNotFound', 'comments');
		} else {
			$model->delete();
			$return['status'] = 1;
		}

		echo CJavaScript::jsonEncode($return);
	}


}
