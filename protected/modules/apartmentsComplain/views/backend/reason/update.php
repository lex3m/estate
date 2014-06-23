<?php
$this->breadcrumbs=array(
	tt('Edit reasons of complain')
);

$this->menu=array(
	array('label'=> tt('Complains'), 'url'=>array('/apartmentsComplain/backend/main/admin')),
	array('label'=>tt('Reasons of complain'), 'url'=>array('/apartmentsComplain/backend/complainreason/admin')),
);

$this->adminTitle = tt('Edit reasons of complain');

$this->renderPartial('_form', array(
	'model' => $model,
));
?>