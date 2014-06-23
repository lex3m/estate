<?php
$this->breadcrumbs=array(
	tt('Manage apartments') => array('admin'),
	tt('Update apartment'),
);

$this->menu = array(
	array('label'=>tt('Manage apartments'), 'url'=>array('admin')),
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
	array('label'=>tt('Delete apartment'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))),
);

$this->adminTitle = tt('Update apartment');
?>

<?php
	if(isset($show) && $show){
		/*Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScript('scroll-to','
				scrollto("'.CHtml::encode($show).'");
			',CClientScript::POS_READY
		);*/
	}
	$this->renderPartial('_form',array(
			'model'=>$model,
			'supportvideoext' => $supportvideoext,
			'supportvideomaxsize' => $supportvideomaxsize,
	));
?>