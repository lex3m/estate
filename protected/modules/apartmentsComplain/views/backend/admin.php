<?php
$this->breadcrumbs = array(
	tt('Complains'),
);

$this->menu=array(
	array('label'=> tt('Reasons of complain'), 'url'=>array('/apartmentsComplain/backend/complainreason/admin')),
);

$this->adminTitle = tt('Complains');

$this->widget('CustomGridView', array(
	'id' => 'complains-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns' => array(
		array(
			'class' => 'CCheckBoxColumn',
			'id' => 'itemsSelected',
			'selectableRows' => '2',
			'htmlOptions' => array(
				'class' => 'center',
			),
		),
		array(
			'name' => 'name',
			'headerHtmlOptions' => array('style' => 'width:150px;'),
			'type' => 'raw',
			'value' => 'ApartmentsComplain::getUserEmailLink($data)',
			'filter' => false,
			'sortable' => false,
		),
		array(
			'name' => 'complain_id',
			'headerHtmlOptions' => array('style' => 'width:150px;'),
			'value' => 'ApartmentsComplainReason::getAllReasons($data->complain_id)',
			'filter' => ApartmentsComplainReason::getAllReasons(),
			'sortable' => false,
		),
		'body',
		array(
			'name' => 'apartment_id',
			'headerHtmlOptions' => array('style' => 'width:150px;'),
			'type' => 'raw',
			'value' => 'CHtml::link($data->apartment->id, Yii::app()->createUrl("/apartments/backend/main/view", array("id" => $data->apartment->id)))',
			'filter' => false,
			'sortable' => true,
		),
		array(
			'name' => 'date_created',
			'headerHtmlOptions' => array('style' => 'width:130px;'),
			'filter' => false,
			'sortable' => true,
		),
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
			'template' => '{delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'viewButtonUrl' => '',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartmentsComplain/backend/main/itemsSelected',
	'id' => 'complains-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>