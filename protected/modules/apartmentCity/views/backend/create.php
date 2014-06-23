<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'Object type') => array('/site/viewreferences'),
	tt('Manage apartment city')=>array('admin'),
	tt('Add city'),
);

$this->menu=array(
	array('label'=>tt('Manage apartment city'), 'url'=>array('admin')),
	//array('label'=>tt('Add city'), 'url'=>array('/apartmentCity/backend/main/create')),
);

$this->adminTitle = tt('Add city');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>