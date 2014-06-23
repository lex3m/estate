<?php
$this->breadcrumbs=array(
	tt('Manage lang')=>array('admin'),
	tt('Add lang'),
);

$this->menu=array(
	array('label'=>tt('Manage lang'), 'url'=>array('admin')),
);

$this->adminTitle = tt('Add lang');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>