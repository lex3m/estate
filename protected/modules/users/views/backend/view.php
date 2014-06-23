<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment') => array('admin'),
	$model->email.($model->username != '' ? ' ('.$model->username.')' : ''),
);

$this->menu=array(
	/*array('label'=>Yii::t('common', 'User managment'), 'url'=>array('admin')),
	array('label'=>tt('Add user'), 'url'=>array('create')),
	array('label'=>tt('Edit user'), 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>tt('Delete user'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),
		'confirm'=>tc('Are you sure you want to delete this item??'))),*/
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);
$model->scenario = 'backend';

$this->adminTitle = $model->email.($model->username != '' ? ' ('.$model->username.')' : '');
?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data' => $model,
	'attributes'=>array(
		'id',
		'username',
		'email',
		'phone',
		'balance',
		array (
			'label' => CHtml::encode($model->getAttributeLabel('additional_info')),
			'value' => $model->getAdditionalInfo(),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => tt('Status'),
			'value' => ($model->active) ? tt('Active') : tt('Inactive'),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
	),
)); ?>
