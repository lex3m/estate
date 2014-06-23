<?php
$this->pageTitle=Yii::app()->name . ' - ' . InfoPagesModule::t('Edit infopage');

$this->menu = array(
    array('label' => tt('Manage infopages'), 'url' => array('admin')),
	array('label' => InfoPagesModule::t('Add infopage'), 'url' => array('create')),
	array('label' => tt('Delete infopage'),
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	)
);
$this->adminTitle = InfoPagesModule::t('Edit infopage');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>