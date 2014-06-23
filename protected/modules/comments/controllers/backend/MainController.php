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
	public $modelName = 'Comment';

	public function actionIndex(){
		$model = new $this->modelName;
		$model = $model->resetScope();

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public static function returnStatusHtml($data, $tableId, $onclick = 0, $ignore = 0){
		if($data->status){
			return '<div align="center">'.
			$img = CHtml::image(
					Yii::app()->request->baseUrl.'/images/'.($data->status?'':'in').'active_grey.png',
					Yii::t('common', $data->status?'Active':'Inactive')).
				'</div>';
		}
		$url = Yii::app()->controller->createUrl("activate", array("id" => $data->id, 'action' => ($data->status==1?'deactivate':'activate'), 'field' => 'status'));
		$img = CHtml::image(
			Yii::app()->request->baseUrl.'/images/'.($data->status?'':'in').'active.png',
			Yii::t('common', $data->status?'Active':'Inactive'),
			array('title' => Yii::t('common', $data->status?'Deactivate':'Activate'))
		);
		$options = array();
		if($onclick){
			$options = array(
				'onclick' => 'ajaxSetStatus(this, "'.$tableId.'"); return false;',
			);
		}
		return '<div align="center">'.CHtml::link($img,$url, $options).'</div>';
	}

	public function actionItemsSelected(){
		$idsSelected = Yii::app()->request->getPost('itemsSelected');

		$work = Yii::app()->request->getPost('workWithItemsSelected');

		if($idsSelected && is_array($idsSelected) && $work){
			$idsSelected = array_map('intval', $idsSelected);

			foreach($idsSelected as $id){
				$model = $this->loadModel($id);
				$model->scenario = 'changeStatus';

				if($work == 'delete'){
					$model->delete();
				}elseif($work == 'activate') {
					$model->status = Comment::STATUS_APPROVED;
					$model->update('status');
				}/*elseif($work == 'deactivate') {
					$model->active = 0;
					$model->update('active');
				}*/
			}
		}

		if(!Yii::app()->request->isAjaxRequest){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

	public function actionApprove($id){
		$comment=$this->loadModel($id);
		if($comment->status != Comment::STATUS_APPROVED){
			$comment->status = Comment::STATUS_APPROVED;
			$comment->update(array('status'));
		}
		$this->redirect(array('index'));
	}

	public function actionView($id){
		$this->redirect(array('index'));
	}

}
