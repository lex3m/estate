<?php

$this->breadcrumbs=array(
	tt('Manage of the top menu') => array('admin'),
);

$this->menu=array(
	array('label'=>tt('Manage of the top menu'), 'url'=>array('admin')),
);

$this->renderPartial('_form', array('model'=>$model));