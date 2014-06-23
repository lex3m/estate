<?php

// protected/components/SubscriberFormWidget.php

class LastEstateObjects extends CWidget
{


    public function run()
    {
        $criteria = new CDbCriteria(array(
            'limit'=>6,
            'order'=>'t.id DESC',
            'condition'=>'active=:active',
            'params'=>array(':active'=>Apartment::STATUS_ACTIVE),
        ));
        $lastEstateObjects = Apartment::model()->findAll($criteria);
        $this->render('lastEstateObjects', array('lastEstateObjects'=>$lastEstateObjects));
    }
}

