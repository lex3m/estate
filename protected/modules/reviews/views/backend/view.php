<?php
$this->menu = array(
	array('label' => ReviewsModule::t('Add_feedback'), 'url' => array('create')),
	array('label' => ReviewsModule::t('Edit_review'), 'url' => array('update', 'id' => $model->id)),
	array('label' => tt('Delete_review', 'reviews'), 'url' => '#',
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	),
);

$this->adminTitle = tt('View_review');

$this->widget('bootstrap.widgets.TbDetailView',array(
	'data' => $model,
	'attributes'=>array(
		'id',
		'name',
		'email',
		array(
			'label' => CHtml::encode($model->getAttributeLabel('body')),
			'value' => CHtml::encode($model->body),
			'type' => 'raw',
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		'date_created',
	))
);
?>