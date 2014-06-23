<?php
$this->breadcrumbs=array(
	tt('Manage reference values')=>array('admin'),
	tt('Update reference'),
);

$this->menu=array(
	array('label'=>tt('Manage reference values'), 'url'=>array('admin')),
	array('label'=>tt('Create reference value'), 'url'=>array('create')),
	array('label'=>tt('Delete reference value'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))),
	array('label'=>tt('Create value'), 'url'=>array('/referencevalues/backend/main/create')),
);

$this->adminTitle = tt('Update reference');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>