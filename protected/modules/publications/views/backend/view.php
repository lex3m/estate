<?php
/* @var $this PublicationController */
/* @var $model Publication */

$this->breadcrumbs=array(
	'Publications'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Publication', 'url'=>array('index')),
	array('label'=>'Create Publication', 'url'=>array('create')),
	array('label'=>'Update Publication', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Publication', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Publication', 'url'=>array('admin')),
);
?>

<h1>Детали публикации</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'date',
	),
)); ?>
<img src="<?php echo Yii::app()->request->getBaseUrl(true).'/media/publications/snapshots/'.$model->snapshot; ?>" style=""></img>
