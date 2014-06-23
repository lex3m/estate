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

class HUser {
    const UPLOAD_MAIN = 'main';
    const UPLOAD_PORTFOLIO = 'portfolio';
    const UPLOAD_AVA = 'ava';

    private static $_model;

    public static function getUploadDirectory(User $user, $category = self::UPLOAD_MAIN) {
        $DS = DIRECTORY_SEPARATOR;
        $root = ROOT_PATH . $DS . 'uploads' . $DS . $category;
        self::genDir($root);

        $year = date('Y', strtotime($user->date_created));
        $path = $root . $DS . $year;
        self::genDir($path);

        $month = date('m', strtotime($user->date_created));
        $path = $path . $DS . $month;
        self::genDir($path);

        return $path;
    }

    public static function getUploadUrl(User $user, $category = self::UPLOAD_MAIN){
        $DS = '/';
        $root = 'uploads' . $DS . $category;

        $year = date('Y', strtotime($user->date_created));
        $path = $root . $DS . $year;

        $month = date('m', strtotime($user->date_created));
        $path = $path . $DS . $month;

        return Yii::app()->baseUrl . $DS . $path;
    }

    public static function genDir($path){
        if(!is_dir($path)){
            if(!mkdir($path)){
                throw new CException('HUser невозможно создать директорию ' . $path);
            }
        }
    }

    public static function getModel()
    {
        if(!isset(self::$_model)){
            self::$_model = User::model()->findByPk(Yii::app()->user->id);
        }

        return self::$_model;
    }

    public static function getListAgency(){
        $sql = "SELECT id, agency_name FROM {{users}} WHERE active = 1 AND type=:type";
        $all = Yii::app()->db->createCommand($sql)->queryAll(true, array(':type' => User::TYPE_AGENCY));
        $list = CHtml::listData($all, 'id', 'agency_name');

        return CMap::mergeArray(array(0 => ''), $list);
    }

    public static function getLinkDelAgent(User $user){
        return CHtml::link(tc('Delete'), Yii::app()->createUrl('/usercpanel/main/deleteAgent', array('id' => $user->id)));
    }

    public static function returnStatusHtml($data, $tableId){
        $statuses = User::getAgentStatusList();

        $options = array(
            'onclick' => 'ajaxSetAgentStatus(this, "'.$tableId.'", "'.$data->id.'"); return false;',
        );

        return '<div align="center" class="editable_select" id="editable_select-'.$data->id.'">'.CHtml::link($statuses[$data->agent_status], '#' , $options).'</div>';
    }

    public static function getCountAwaitingAgent($agencyUserID){
        $sql = "SELECT COUNT(id) FROM {{users}} WHERE agency_user_id = :user_id AND agent_status = :status AND active = 1";
        return Yii::app()->db->createCommand($sql)->queryScalar(array(
            ':user_id' => $agencyUserID,
            ':status' => User::AGENT_STATUS_AWAIT_VERIFY,
        ));
    }

    public static function getMenu(){
        $user = HUser::getModel();

        if(param('useUserads')){
            $menu[] = array(
                'label' => tc('My listings'),
                'url' => Yii::app()->createUrl('/usercpanel/main/index'),
                'active' => Yii::app()->controller->menuIsActive('my_listings'),
            );

            $menu[] = array(
                'label' => tc('Add ad', 'apartments'),
                'url' => Yii::app()->createUrl('/userads/main/create'),
                'active' => Yii::app()->controller->menuIsActive('add_ad'),
            );
        }

        if($user->type == User::TYPE_AGENCY){
            $countAwaitAgent = HUser::getCountAwaitingAgent($user->id);
            $bage = $countAwaitAgent ? ' (' .$countAwaitAgent. ')' : '';

            $menu[] = array(
                'label' => tt('My agents', 'usercpanel').$bage,
                'url' => Yii::app()->createUrl('/usercpanel/main/agents'),
                'active' => Yii::app()->controller->menuIsActive('my_agents'),
            );
        }

        $menu[] = array(
            'label' => tc('My data'),
            'url' => Yii::app()->createUrl('/usercpanel/main/data'),
            'active' => Yii::app()->controller->menuIsActive('my_data'),
        );
        $menu[] = array(
            'label' => tt('Change your password', 'usercpanel'),
            'url' => Yii::app()->createUrl('/usercpanel/main/changepassword'),
            'active' => Yii::app()->controller->menuIsActive('my_changepassword'),
        );

        if (issetModule('payment')) {
            $menu[] = array(
                'label' => tt('My payments', 'usercpanel'),
                'url' => Yii::app()->createUrl('/usercpanel/main/payments'),
                'active' => Yii::app()->controller->menuIsActive('my_payments'),
            );
            $menu[] = array(
                'label' => tc('My balance') . ' (' . $user->balance . ' ' . Currency::getDefaultCurrencyName() . ')',
                'url' => Yii::app()->createUrl('/usercpanel/main/balance'),
                'active' => Yii::app()->controller->menuIsActive('my_balance'),
            );
        }

        if (issetModule('bookingtable')) {
            $menu[] = array(
                'label' => tt('Booking applications', 'usercpanel')  . ' (' . Bookingtable::getCountNew(true) . ')',
                'url' => Yii::app()->createUrl('/bookingtable/main/index'),
                'active' => Yii::app()->controller->menuIsActive('booking_applications'),
            );
        }

        return $menu;
    }
}