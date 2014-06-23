<?php

$this->breadcrumbs=array(
	tt("FAQ")=>array('index'),
	tt("Manage FAQ")=>array('admin'),
	$model['page_title'],
);

$this->menu=array(
	array('label' => tt("Manage FAQ"), 'url'=>array('/articles/backend/main/admin')),
	array('label'=>tt("Add FAQ"), 'url'=>array('/articles/backend/main/create')),
	array('label'=>tt("Update FAQ"), 'url'=>array('/articles/backend/main/update', 'id' => $model->id)),
	array('label'=>tt('Delete FAQ'), 'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=>tc('Are you sure you want to delete this item?')
		)
	),
);

$this->adminTitle = $model['page_title'];

$this->renderPartial('../view', array(
		'model' => $model,
		'articles' => $articles,
));

?>
