<?php

$this->menu = array(
	array('label' => NewsModule::t('Add news'), 'url' => array('create')),
	array('label' => NewsModule::t('Edit news'), 'url' => array('update', 'id' => $model->id)),
	array('label' => tt('Delete news', 'news'), 'url' => '#',
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tt('Are you sure you want to delete this item?')
		),
	),

);

$this->renderPartial('../view', array(
	'model' => $model,
));