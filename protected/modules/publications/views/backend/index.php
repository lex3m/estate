<?php
/* @var $this PublicationController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Publications',
);

$this->menu=array(
	array('label'=>'Create Publication', 'url'=>array('create')),
	array('label'=>'Manage Publication', 'url'=>array('admin')),
);
?>

<h1>Publications</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
