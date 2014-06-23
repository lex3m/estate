<?php
$this->pageTitle=Yii::app()->name . ' - ' . PublicationsModule::t('Manage publications');

$this->menu = array(
    array('label' => PublicationsModule::t('Manage publications'), 'url' => array('admin')),
);
$this->adminTitle = PublicationsModule::t('Manage publications');
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>