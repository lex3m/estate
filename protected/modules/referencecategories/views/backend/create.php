<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference categories')=>array('admin'),
	tt('Add category'),
);

$this->menu=array(
	array('label'=>tt('Manage reference categories'), 'url'=>array('admin')),
	//array('label'=>tt('Add reference category'), 'url'=>array('/referencecategories/backend/main/create')),
);

$this->adminTitle = tt('Add category');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>