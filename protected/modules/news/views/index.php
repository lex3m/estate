<?php
$this->pageTitle .= ' - '.NewsModule::t('News');
$this->breadcrumbs=array(
    NewsModule::t('News'),
);
?>

<h1><?php echo NewsModule::t('News'); ?></h1>

<?php
	$this->renderPartial('widgetNews_list', array(
		'news' => $items,
		'pages' => $pages,
	));

