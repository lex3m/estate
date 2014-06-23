<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference (window to..)') => array('admin'),
	tt('Add value'),
);

$this->menu=array(
	array('label'=>tt('Manage reference (window to..)'), 'url'=>array('index')),
	//array('label'=>tt('Add value'), 'url'=>array('/windowto/backend/main/create')),
);

$this->adminTitle = tt('Add value');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>