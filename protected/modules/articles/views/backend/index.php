<?php
$this->breadcrumbs=array(
	tt("FAQ"),
	tt("Manage FAQ")=>array('admin'),
);

$this->menu=array(
	array('label' => tt("Manage FAQ"), 'url'=>array('/articles/backend/main/admin')),
	array('label'=>tt("Add FAQ"), 'url'=>array('/articles/backend/main/create')),
);

$this->adminTitle = tt("FAQ");

$this->renderPartial('../index', array(
		'pages' => $pages,
		'articles' => $articles,
));
