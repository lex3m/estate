<?php
$this->breadcrumbs=array(
	tt('Add reasons of complain')
);

$this->menu=array(
	array('label'=> tt('Complains'), 'url'=>array('/apartmentsComplain/backend/main/admin')),
	array('label'=>tt('Reasons of complain'), 'url'=>array('/apartmentsComplain/backend/complainreason/admin')),
);

$this->adminTitle = tt('Add reasons of complain');

$this->renderPartial('_form', array(
	'model' => $model,
));
?>