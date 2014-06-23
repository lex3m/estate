<?php
	class commentListWidget extends CWidget {

		public $model;
		public $url;
		public $showRating = false;

		// TODO
		// уведомление на почту о комментариях
		// Reply

		public function getModelName(){
			return get_class($this->model);
		}

		public function getViewPath($checkTheme=false){
			if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'comments';
			}
			return Yii::getPathOfAlias('application.modules.comments.views');
		}

		public function createComment(){
			$comment = new Comment();
			$comment->model_name = $this->getModelName();
			$comment->model_id = $this->getModelId();
			return $comment;
		}

		protected function getModelId() {
			if (is_array($this->model->primaryKey)) {
				return implode('.', $this->model->primaryKey);
			} else {
				return $this->model->primaryKey;
			}
		}

		public function run() {
			$newComment = $this->createComment();
			$comments = $newComment->getCommentsThree();

			$form = new CommentForm();
			$form->url = $this->url;
			$form->modelName = $this->getModelName();
			$form->modelId = $this->getModelId();
			$form->defineShowRating();

			$this->render('commentsListWidget', array(
				'comments' => $comments,
				'newComment' => $newComment,
				'form' => $form,
			));
		}
	}