<?php
$this->breadcrumbs=array(
	tt('Manage apartments') => array('admin'),
	tt('Add apartment'),
);
$this->menu = array(
	array('label'=>tt('Manage apartments'), 'url'=>array('admin')),
);
$this->adminTitle = tt('Add apartment');
?>

<?php
	$this->renderPartial('_form',array(
		'model'=>$model,
		'supportvideoext' => $supportvideoext,
		'supportvideomaxsize' => $supportvideomaxsize,
	));
?>