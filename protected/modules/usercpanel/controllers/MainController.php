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
    public $layout='//layouts/usercpanel';

    public $modelName = 'User';

	public function filters(){
		return array(
			'accessControl',
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				'users'=>array('@'),
			),
			array(
				'deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex(){
        $this->setActiveMenu('my_listings');

		$model=$this->loadModel(Yii::app()->user->id);
		$from = Yii::app()->request->getParam('from');

		$socSuccess = Yii::app()->request->getQuery('soc_success');
		if ($socSuccess)
			Yii::app()->user->setFlash('error', tt('During export account data may be generate random email and password. Please change it.', 'socialauth'));

		if ($from != 'userads') {
			if(!$socSuccess && preg_match("/null\.io/i", $model->email))
				Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));
		}

		if(isset($_POST[$this->modelName])){
			if(isset($_POST['changePassword']) && $_POST['changePassword']){
				$model->scenario = 'changePass';

				$model->attributes=$_POST[$this->modelName];

				if($model->validate()){
					$model->setPassword();
					$model->save(false);
					Yii::app()->user->setFlash('success', tt('Your password successfully changed.'));
					$this->redirect(array('index'));
				}
			}
			else{
				$model->scenario = 'usercpanel';
				$model->attributes=$_POST[$this->modelName];

				if($model->save()){
					if($model->scenario == 'usercpanel'){
						Yii::app()->user->setFlash('success', tt('Your details successfully changed.'));
					}
					$this->redirect(array('index'));
				}
			}
		}

		$this->render('index',array(
			'model' => $this->loadModel(Yii::app()->user->id),
			'from' => $from,
		));
	}

    public function actionData(){
        $this->setActiveMenu('my_data');

        $model=$this->loadModel(Yii::app()->user->id);

		$agencyUserIdOld = '';

        if($model->type == User::TYPE_AGENT){
            $agencyUserIdOld = $model->agency_user_id;
        }

        if(preg_match("/null\.io/i", $model->email))
            Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));

        if(isset($_POST[$this->modelName])){
            $model->scenario = 'usercpanel';
            $model->attributes=$_POST[$this->modelName];

            if($agencyUserIdOld != $model->agency_user_id){
                if($model->agency_user_id){
                    $agency = User::model()->findByPk($model->agency_user_id);

                    if($agency){
                        $notifier = new Notifier();
                        $notifier->raiseEvent('onNewAgent', $model, array(
                            'forceEmail' => $agency->email,
                        ));
                    } else {
                        $model->addError('agency_user_id', 'There is no Agency with such ID');
                    }
                }

                $model->agent_status = User::AGENT_STATUS_AWAIT_VERIFY;
            }

            if($model->save()){
                if($model->scenario == 'usercpanel'){
                    Yii::app()->user->setFlash('success', tt('Your details successfully changed.'));
                }
                $this->redirect(array('index'));
            }
        }

        $this->render('data',array(
            'model' => $model,
        ));
    }

    public function actionChangepassword(){
        $this->setActiveMenu('my_changepassword');

        $model=$this->loadModel(Yii::app()->user->id);
        $from = Yii::app()->request->getParam('from');

        if(preg_match("/null\.io/i", $model->email))
            Yii::app()->user->setFlash('error', tt('Please change your email and password!', 'socialauth'));

        if(isset($_POST[$this->modelName])){
            $model->scenario = 'changePass';
            $model->attributes=$_POST[$this->modelName];

            if($model->validate()){
                $model->setPassword();
                $model->save(false);
                Yii::app()->user->setFlash('success', tt('Your password successfully changed.'));
                $this->redirect(array('index'));
            }
        }

        $this->render('changepassword', array(
            'model' => $model,
            'from' => $from,
        ));
    }

    public function actionPayments(){
        $this->setActiveMenu('my_payments');

        if(Yii::app()->request->isAjaxRequest){
            $this->renderPartial('payments',array(
                'model' => User::model()->with('payments')->findByPk(Yii::app()->user->id)
            ), false, true);
        } else {
            $this->render('payments',array(
                'model' => User::model()->with('payments')->findByPk(Yii::app()->user->id)
            ));
        }
    }

    public function actionBalance(){
        $this->setActiveMenu('my_balance');

        $this->render('balance');
    }

    public function actionAjaxDelAva(){
        if(Yii::app()->user->isGuest || !Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $user = HUser::getModel();
        $folder = HUser::getUploadDirectory($user, HUser::UPLOAD_AVA) . DIRECTORY_SEPARATOR;
        @unlink($folder . $user->ava);
        @unlink($folder . User::AVA_PREFIX . $user->ava);

        $user->ava = '';
        $user->update(array('ava'));

        $result['avaHtml'] = '<div class="user-ava-crop">'.CHtml::image(Yii::app()->baseUrl . '/images/ava-default.jpg', $user->username, array('class' => 'message_ava')).'</div>';

        HAjax::jsonOk(tc('Success'), $result);
    }

    public function actionAgents(){
        $user = HUser::getModel();
        if($user->type != User::TYPE_AGENCY){
            throw404();
        }

        $this->setActiveMenu('my_agents');

        $model = new User('search');
        $model->myAgents();
        $model->with('countAdRel');

        $this->render('agents', array('model' => $model));
    }

    public function actionDeleteAgent($id){
        $user = HUser::getModel();
        if($user->type != User::TYPE_AGENCY){
            throw404();
        }

        $agent = User::model()->findByPk($id);
        $agent->agency_user_id = 0;
        $agent->update(array('agency_user_id'));

        Yii::app()->user->setFlash('success', Yii::t('common', 'This user "{name}" is not your agent anymore', array('{name}' => $agent->username)));

        $this->redirect(array('agents'));
    }

    public function actionAjaxSetAgentStatus(){
        if (Yii::app()->request->getParam('id') && (Yii::app()->request->getParam('value') != null)) {
            $status = Yii::app()->request->getParam('value', null);
            $id = Yii::app()->request->getParam('id', null);
            $user = User::model()->findByPk($id);

            $availableStatuses = User::getAgentStatusList();
            if (!array_key_exists($status, $availableStatuses) || !$user) {
                HAjax::jsonError();
            }

            $user->agent_status = $status;
            $user->update(array('agent_status'));
        }

        echo CHtml::link($availableStatuses[$status]);
    }
}

