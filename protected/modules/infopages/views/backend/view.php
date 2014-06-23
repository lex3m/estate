<?php
$this->menu = array(
	array('label' => InfoPagesModule::t('Add infopage'), 'url' => array('create')),
	array('label' => InfoPagesModule::t('Edit infopage'), 'url' => array('update', 'id' => $model->id)),
	array('label' => tt('Delete infopage'), 'url' => '#',
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	),
);

$this->widget('bootstrap.widgets.TbDetailView', array(
	'data' => $model,
	'attributes'=>array(
		'id',
		array (
			'label' => CHtml::encode($model->getAttributeLabel('active')),
			'value' => ($model->active == InfoPages::STATUS_ACTIVE) ? tc('Active') : tc('Inactive'),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => CHtml::encode($model->getAttributeLabel('title')),
			'type' => 'raw',
			'value' => CHtml::encode($model->getStrByLang('title')),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => CHtml::encode($model->getAttributeLabel('body')),
			'type' => 'raw',
			'value' => CHtml::decode($model->getStrByLang('body')),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => CHtml::encode($model->getAttributeLabel('widget')),
			'value' => ($model->widget) ? InfoPages::getWidgetOptions($model->widget) : '',
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
	),
));
