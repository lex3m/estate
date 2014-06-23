<?php
$this->pageTitle .= ' - '.tt('Contact Us', 'contactform');

$this->breadcrumbs=array(
	tt('Contact Us', 'contactform')
);

Yii::import('application.modules.contactform.components.*');
$this->widget('ContactformWidget', array('page' => 'contactForm'));
