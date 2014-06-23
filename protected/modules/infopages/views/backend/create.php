<?php
$this->pageTitle=Yii::app()->name . ' - ' . InfoPagesModule::t('Add infopage');

$this->menu = array(
    array('label' => tt('Manage infopages'), 'url' => array('admin')),
);

$this->adminTitle = InfoPagesModule::t('Add infopage');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>