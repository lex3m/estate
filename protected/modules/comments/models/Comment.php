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

	class Comment extends ParentModel {
		const STATUS_PENDING = 0;
		const STATUS_APPROVED = 1;
		const STATUS_DELETED = 2;

		public $dateCreated;

		public $childs;
		public $username;

		public static function model($className=__CLASS__) {
			return parent::model($className);
		}

		public function tableName() {
			return '{{comments}}';
		}

		public function rules() {
			return array(
				array('body', 'required'),
				array('rating, user_name, user_email', 'safe'),
			);
		}

		public function relations() {
			return array(
				'user' => array(self::BELONGS_TO, 'User', 'owner_id'),
			);
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
		public function attributeLabels() {
			return array(
				'body' => Yii::t('module_comments', 'Comment'),
				'rating' => Yii::t('module_comments', 'Rate'),
				'verifyCode' => tt('Verification Code', 'contactform'),
				'date_created' => Yii::t('module_comments', 'Creation date'),
				'status' => Yii::t('module_comments', 'Status'),
				'user_name' => Yii::t('module_comments', 'Name'),
				'user_email' => Yii::t('module_comments', 'Email'),
			);
		}

		public function getCommentsThree() {
			$criteria = new CDbCriteria;
			$criteria->compare('model_name', $this->model_name);
			$criteria->compare('model_id', $this->model_id);
			$criteria->compare('t.status', array(
				self::STATUS_APPROVED,
				self::STATUS_DELETED,
			));

			$criteria->order = 'parent_id, t.date_created';
			$criteria->join = 'LEFT JOIN {{users}} user ON user.id=owner_id';
			$criteria->select = 't.id, parent_id, rating, owner_id, t.status, t.body, t.date_created, user.username, user_name';

			$comments = self::model()->findAll($criteria);

			return $this->buildTree($comments);
		}

		private function buildTree(&$data, $rootId = 0) {
			$tree = array();
			foreach ($data as $id => $node) {
				$node->parent_id = $node->parent_id === null ? 0 : $node->parent_id;
				if ($node->parent_id == $rootId) {
					unset($data[$id]);
					$node->childs = $this->buildTree($data, $node->id);
					$tree[] = $node;
				}
			}
			return $tree;
		}

		public static function checkExist($id, $modelName, $modelId) {
			if ($id) {
				$sql = 'SELECT COUNT(*) FROM {{comments}} WHERE id=:id AND model_name=:modelName AND model_id=:modelId';
				return Yii::app()->db->createCommand($sql)->queryScalar(array(
					':id' => $id,
					':modelName' => $modelName,
					':modelId' => $modelId,
				));
			} else {
				if(class_exists($modelName)){
					$model = new $modelName;
				} else {
					return false;
				}

				/*if (@YiiBase::autoload($modelName)) {
					$model = new $modelName;
				} else {
					return false;
				}*/

				$model = $model->findByPk($modelId);
				return $model ? true : false;
			}
		}

		protected function afterFind() {
			$dateFormat = param('commentModule_dateFormat', 0) ? param('commentModule_dateFormat') : param('dateFormat', 'd.m.Y H:i:s');
			$this->dateCreated = date($dateFormat, strtotime($this->date_created));

			return parent::afterFind();
		}

		public static function calcRating($modelName, $modelId){
			$sql = 'SELECT AVG(rating) FROM {{comments}}
				WHERE model_name=:modelName AND model_id=:modelId AND status=:status AND rating > -1';
			return intval(Yii::app()->db->createCommand($sql)->queryScalar(array(':modelName' => $modelName, ':modelId' => $modelId, ':status' => Comment::STATUS_APPROVED)));
		}

		public static function removeComment($id){
			$sql = 'UPDATE {{comments}} SET parent_id=0 WHERE parent_id=:id';
			Yii::app()->db->createCommand($sql)->execute(array(':id' => $id));
		}

		public static function getCountPending(){
			$sql = 'SELECT COUNT(*) FROM {{comments}} WHERE status=:status';
			return Yii::app()->db->createCommand($sql)->queryScalar(array(':status' => Comment::STATUS_PENDING));
		}

		public static function countForModel($modelName, $modelId){
			$sql = 'SELECT COUNT(*) FROM {{comments}} WHERE model_name=:modelName AND model_id=:modelId AND status=:status';
			return Yii::app()->db->createCommand($sql)->queryScalar(array(':modelName' => $modelName, ':modelId' => $modelId, ':status' => self::STATUS_APPROVED));
		}

		public function search(){
			$criteria = new CDbCriteria();

			//$criteria->compare('name',$this->name, true);

			$criteria->compare('body',$this->body, true);
			$criteria->with = array('user');

			//$criteria->compare('rating',$this->rating);

			return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>param('adminPaginationPageSize', 20),
				),
				'sort'=>array('defaultOrder'=>'t.date_created DESC'),
			));
		}

		public function getLinkForSection(){
			$tmp = new $this->model_name;
			$model = $tmp->findByPk($this->model_id);
			if(!$model){
				return '';
			}
			$area = '';
			$url = '';

			switch($this->model_name){
				case 'News':
					$area = tt('News', 'news').': '.$model->getStrByLang('title');
					$url = $model->getUrl();
					break;
				case 'Article':
					$area = tt('FAQ', 'articles').': '.$model->getStrByLang('page_title');
					$url = $model->getUrl();
					break;
				case 'Apartment':
					$area = tt('Apartments list', 'apartments').': '.$model->getStrByLang('title');
					$url = $model->getUrl();
					break;
				case 'Menu':
					$area = tt('Page', 'service').': '.$model->getTitle();
					$url = $model->getUrl();
					break;
			}

			if($area && $url){
				return CHtml::link($area, $url);
			} else {
				return '';
			}
		}

		public function getUser(){
			if($this->owner_id && $this->user){
				return CHtml::link($this->user->username, array('/users/backend/main/view', 'id' => $this->user->id));
			} else {
				return CHtml::encode($this->user_name).' (<a href="mailto:'.$this->user_email.'">'.$this->user_email.'</a>)';
			}
		}

		public function _calcRating(){
			$form = new CommentForm();
			$form->modelName = $this->model_name;
			$form->defineShowRating();

			if($form->enableRating && $this->rating != -1){
				$rating = self::calcRating($this->model_name, $this->model_id);
				$tmp = new $this->model_name;
				$tmp->writeRating($this->model_id, $rating);
			}
		}

		public function afterDelete(){
			if($this->status == Comment::STATUS_APPROVED){
				$this->_calcRating();
			}
			self::removeComment($this->id);

			return parent::afterDelete();
		}

		public function afterSave(){
			if ($this->status == Comment::STATUS_APPROVED){
				$this->_calcRating();

//            if($this->model_name == 'Apartment'){
//                $ad = Apartment::model()->with('user')->findByPk($this->model_id);
//                if($ad && isset($ad->user)){
//                    $user = $ad->user;
//                    $notifier = new Notifier();
//                    $notifier->raiseEvent('onNewComment', $this, array('user' => $user));
//                }
//            }
			}

            if($this->isNewRecord){
                $notifier = new Notifier();
                $notifier->raiseEvent('onNewComment', $this);
            }

			return parent::afterSave();
		}
	}
