<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference categories')=>array('admin'),
	tt('Edit category:').' '.$model->getTitle(),
);

$this->menu=array(
	array('label'=>tt('Manage reference categories'), 'url'=>array('admin')),
	array('label'=>tt('Delete category'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))),
	array('label'=>tt('Add reference category'), 'url'=>array('/referencecategories/backend/main/create')),

);
$this->adminTitle = tt('Edit category:').' '.$model->getTitle();
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>