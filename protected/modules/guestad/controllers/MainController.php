<?php

class MainController extends ModuleUserController {
    public function actions() {
        return array(
            'captcha' => array(
                'class' => 'MathCCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
        );
    }

    public function actionCreate(){
        if(!Yii::app()->user->isGuest){
            if(Yii::app()->user->getState('isAdmin')){
                $this->redirect(Yii::app()->createUrl('/apartments/backend/main/create'));
            }else{
                $this->redirect(Yii::app()->createUrl('/userads/main/create'));
            }
        }

        $user = new User('register');
        $login = new LoginForm();
        $model = new Apartment();
        $model->active = Apartment::STATUS_DRAFT;
        $model->period_activity = param('apartment_periodActivityDefault', 'always');

        $isAdmin = false;
        $activeTab = 'tab_register';
        $isUpdate = Yii::app()->request->getPost('is_update');

        if (!$isUpdate && isset($_POST['LoginForm']) && ( $_POST['LoginForm']['username'] || $_POST['LoginForm']['password'] )) {
            $activeTab = 'tab_login';
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->login()) {
                User::updateUserSession();
                $isAdmin = Yii::app()->user->getState('isAdmin');
                $user = User::model()->findByPk(Yii::app()->user->id);
            }
        }

        if(isset($_POST['Apartment'])){
            $model->attributes = $_POST['Apartment'];

            if(!$isUpdate){
                $adValid = $model->validate();
                $userValid = false;

                if($activeTab == 'tab_register'){
                    $user->attributes = $_POST['User'];

                    $userValid = $user->validate();
                    if($adValid && $userValid){
                        $user->activatekey = User::generateActivateKey();
                        $userData = User::createUser($user->attributes);

                        if ($userData) {
                            $user = $userData['userModel'];

                            $user->password = $userData['password'];
                            $user->activatekey = $userData['activatekey'];
                            $user->activateLink = $userData['activateLink'];

                            $notifier = new Notifier;
                            $notifier->raiseEvent('onNewUser', $user, $user->id);
                        }
                    }
                }

                if($user->id && (($activeTab == 'tab_login' && $adValid) || ($activeTab == 'tab_register' && $adValid && $userValid))){
                    if(param('useUseradsModeration', 1)){
                        $model->active = Apartment::STATUS_MODERATION;
                    } else {
                        $model->active = Apartment::STATUS_ACTIVE;
                    }
                    $model->owner_active = Apartment::STATUS_ACTIVE;
                    $model->owner_id = $user->id;

                    if($model->save(false)){
                        if(!$isAdmin && param('useUseradsModeration', 1)){
                            Yii::app()->user->setFlash('success', tc('The listing is succesfullty added and is awaiting moderation'));
                        } else {
                            Yii::app()->user->setFlash('success', tc('The listing is succesfullty added'));
                        }

                        if($activeTab == 'tab_register'){
                            showMessage(Yii::t('common', 'Registration'), Yii::t('common', 'You were successfully registered. The letter for account activation has been sent on {useremail}', array('{useremail}' => $user['email'])));
                        } else {
                            if ($isAdmin) {
                                NewsProduct::getProductNews();
                                $this->redirect(array('/apartments/backend/main/update', 'id' => $model->id));
                                Yii::app()->end();
                            } else {
                                $this->redirect(array('/userads/main/update', 'id' => $model->id));
                            }
                        }
                    }
                }

            }

        } else {
            $objTypes = array_keys(Apartment::getObjTypesArray());

            $model->setDefaultType();
            $model->obj_type_id = reset($objTypes);

            $user->unsetAttributes(array('verifyCode'));
        }

        $this->render('create', array(
            'model' => $model,
            'user' => $user,
            'login' => $login,
            'activeTab' => $activeTab,
        ));
    }
}