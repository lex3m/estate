<?php
$this->pageTitle=Yii::app()->name . ' - ' . ReviewsModule::t('Add_feedback');

$this->menu = array(
	array('label' => tt('Reviews_management'), 'url' => array('admin')),
);

$this->adminTitle = ReviewsModule::t('Add_feedback');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>