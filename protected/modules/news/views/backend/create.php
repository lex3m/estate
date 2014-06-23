<?php
$this->pageTitle=Yii::app()->name . ' - ' . NewsModule::t('Add news');

$this->menu = array(
    array('label' => tt('Manage news'), 'url' => array('admin')),
);

$this->adminTitle = NewsModule::t('Add news');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>