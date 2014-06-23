<?php
$this->pageTitle=Yii::app()->name . ' - ' . ReviewsModule::t('Edit_review');

$this->menu = array(
	array('label' => tt('Reviews_management'), 'url' => array('admin')),
	array('label' => ReviewsModule::t('Add_feedback'), 'url' => array('create')),
	array('label' => tt('Delete_review'),
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	)
);
$this->adminTitle = ReviewsModule::t('Edit_review');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>