<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage lang messages')=>array('admin'),
	tt('Edit lang message:'),
);

$this->menu=array(
    array('label'=>tt('Manage lang messages'), 'url'=>array('admin')),
    array('label'=>tt('Add message'), 'url'=>array('/translateMessage/backend/main/create')),
);

$this->adminTitle = tt('Edit lang message:');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>