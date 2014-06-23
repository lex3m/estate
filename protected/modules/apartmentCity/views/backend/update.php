<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage apartment city')=>array('admin'),
	tt('Edit city'),
);

$this->menu=array(
    array('label'=>tt('Manage apartment city'), 'url'=>array('admin')),
    array('label'=>tt('Add city'), 'url'=>array('/apartmentCity/backend/main/create')),
);

$this->adminTitle = tt('Edit city');
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>