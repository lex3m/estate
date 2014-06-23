<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage lang messages')=>array('admin'),
	tt('Add message'),
);


$this->menu=array(
    array('label'=>tt('Manage lang messages'), 'url'=>array('admin')),
	//array('label'=>tt('Add value'), 'url'=>array('create')),
);

$this->adminTitle = tt('Add message');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>