<?php
$this->breadcrumbs=array(
	tt('Manage of the top menu') => array('admin'),
);

$this->menu=array(
	array('label' => tt('Manage of the top menu'), 'url'=>array('admin')),
);

//if (!$model->special)
	//array('label'=> tt('Delete item'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('deleteItem','id'=>$model->id),'confirm'=> tc('Are you sure you want to delete this item?')));


$this->adminTitle = tt('Update').': '.$model->{'title_'.Yii::app()->language};
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>