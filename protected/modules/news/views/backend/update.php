<?php
$this->pageTitle=Yii::app()->name . ' - ' . NewsModule::t('Edit news');

$this->menu = array(
    array('label' => tt('Manage news'), 'url' => array('admin')),
	array('label' => NewsModule::t('Add news'), 'url' => array('create')),
	array('label' => tt('Delete news', 'news'),
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	)
);
$this->adminTitle = NewsModule::t('Edit news').': <i>'.CHtml::encode($model->title).'</i>';
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>