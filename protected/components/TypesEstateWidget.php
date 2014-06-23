<?php

// protected/components/SubscriberFormWidget.php

class TypesEstateWidget extends CWidget
{


    public function run()
    {
        $objTypes = ApartmentObjType::model()->findAll();
        $this->render('typesEstateWidget', array('objTypes'=>$objTypes));
    }
}

