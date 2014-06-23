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

class MainController extends ModuleAdminController {
	public $modelName = 'SocialauthModel';
	public $defaultAction='admin';

	public function actionView($id){
		$this->redirect(array('admin'));
	}

    public function actionAdmin(){
        $model = new SocialauthModel('search');

        $this->render('admin',array(
                'model'=>$model,
                'currentSection' => Yii::app()->request->getQuery('section_filter', 'main'),
        ));
    }

    public function actionUpdate($id, $ajax = 0){
        $model = $this->loadModel($id);

        if($ajax){
			$this->excludeJs();

            $this->renderPartial('update', array(
                'model' => $model,
                'ajax' => $ajax,
            ), false, true);
        }else{
            $this->render('update', array(
                'model' => $model,
                'ajax' => $ajax,
            ));
        }
    }

    public function actionUpdateAjax(){
		if(demo()){
			echo 'ok';
			Yii::app()->end();
		}

        $id = Yii::app()->request->getPost('id');
        $val = Yii::app()->request->getPost('val', '');

        if(!$id){
			Yii::app()->user->setFlash('error', tt('Enter the required value'));
            echo 'error_save';
            Yii::app()->end();
        }

        $model = SocialauthModel::model()->findByPk($id);

		if(!$val && !in_array($model->name, SocialauthModel::model()->allowEmpty)) {
			Yii::app()->user->setFlash('error', tt('Enter the required value'));
			echo 'error_save';
			Yii::app()->end();
		}

        $model->value = $val;
        if($model->save()){
            echo 'ok';
        } else {
			Yii::app()->user->setFlash('error', tt('Enter the required value'));
            echo 'error_save';
        }
    }

    public function actionActivate(){
		if(demo()){
			echo 'ok';
			Yii::app()->end();
		}

        $id = intval(Yii::app()->request->getQuery('id', 0));

        if($id){
            $action = Yii::app()->request->getQuery('action');
            $model = $this->loadModel($id);

            if($model){
                $model->value = ($action == 'activate' ? 1 : 0);
                $model->update(array('value'));
            }
        }
        if(!Yii::app()->request->isAjaxRequest){
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

    public function getSections($withAll = 1){
        $sql = 'SELECT section FROM {{socialauth}} GROUP BY section';
        $categories = Yii::app()->db->createCommand($sql)->queryAll();

        if($withAll)
            $return['all'] = tc('All');
        foreach($categories as $category){
            $return[$category['section']] = tt($category['section']);
        }
        return $return;
    }

}
