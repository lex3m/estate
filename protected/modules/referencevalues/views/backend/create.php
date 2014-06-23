<?php
$this->breadcrumbs=array(
	tt('Manage reference values')=>array('admin'),
	Yii::t('common', 'Create'),
);

$this->menu=array(
	array('label'=>tt('Manage reference values'), 'url'=>array('admin')),
	//array('label'=>tt('Create value'), 'url'=>array('/referencevalues/backend/main/create')),
);
$this->adminTitle = tt('Create reference value');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>