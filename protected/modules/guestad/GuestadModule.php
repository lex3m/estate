<?php
class GuestadModule extends Module {
    public function init(){
        Yii::import('application.modules.guestad.models.*');
        parent::init();
    }
}
