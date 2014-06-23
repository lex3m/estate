<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'Object type') => array('/site/viewreferences'),
	tt('Manage apartment object types')=>array('admin'),
	tt('Add object type'),
);

$this->menu=array(
	array('label'=>tt('Manage apartment object types'), 'url'=>array('admin')),
	//array('label'=>tt('Add object type'), 'url'=>array('/apartmentObjType/backend/main/create')),
);

$this->adminTitle = tt('Add object type');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>