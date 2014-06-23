<?php

$this->menu=array(
	array('label'=>tt('Manage reference', 'windowto'), 'url'=>array('admin')),
	array('label'=>tt('Add value', 'windowto'), 'url'=>array('create')),
	array('label'=>tt('Delete value', 'windowto'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),
		'confirm'=>tc('Are you sure you want to delete this item?'))),
);

$this->adminTitle = tt('Update value', 'windowto');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>