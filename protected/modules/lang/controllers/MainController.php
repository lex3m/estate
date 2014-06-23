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

	public $modelName = 'Lang';

    public function actionAjaxTranslate(){
        if(!Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $fromLang = Yii::app()->request->getPost('fromLang');
        $fields = Yii::app()->request->getPost('fields');
        if(!$fromLang || !$fields){
            throw new CException('Lang no req data');
        }

        $translate = new GoogleTranslater();
        $fromVal = $fields[$fromLang];

        $translateField = array();
        foreach($fields as $lang=>$val){
            if($lang == $fromLang){
                continue;
            }
            $translateField[$lang] = $translate->translateText($fromVal, $fromLang, $lang);
        }

        echo json_encode(array(
            'result' => 'ok',
            'fields' => $translateField
        ));
        Yii::app()->end();
    }
}