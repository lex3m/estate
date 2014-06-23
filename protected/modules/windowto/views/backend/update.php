<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference (window to..)') => array('admin'),
	tt('Update value'),
);

$this->menu=array(
	array('label'=>tt('Manage reference (window to..)'), 'url'=>array('index')),
	array('label'=>tt('Add value'), 'url'=>array('create')),
	array('label'=>tt('Delete value'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),
		'confirm'=>tc('Are you sure you want to delete this item?'))),
);

$this->adminTitle = tt('Update value');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>