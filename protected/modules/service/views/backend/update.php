<?php
$this->pageTitle=Yii::app()->name . ' - ' . tt('Service site', 'common');
$this->adminTitle = tt('Service site', 'common');
$this->menu = array(
	array(),
);
echo $this->renderPartial('/backend/_form', array('model'=>$model)); 
?>