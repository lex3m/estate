<?php
$this->pageTitle .= ' - '.tt('Add apartment', 'apartments');
$this->breadcrumbs = array(
	Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
	tt('Add apartment', 'apartments')
);

//$this->widget('zii.widgets.CMenu', array(
//	'items' => array(
//		array('label'=>tt('Manage apartments', 'apartments'), 'url'=>array('/usercpanel/main/index')),
//	)
//));

$this->renderPartial('_form',array(
	'model'=>$model,
	'supportvideoext' => $supportvideoext,
	'supportvideomaxsize' => $supportvideomaxsize,
));
