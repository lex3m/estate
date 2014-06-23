<?php
$this->pageTitle=Yii::app()->name . ' - ' . InfoPagesModule::t('Manage infopages');


$this->menu = array(
	array('label' => InfoPagesModule::t('Add infopage'), 'url' => array('create')),
);
$this->adminTitle = InfoPagesModule::t('Manage infopages');
?>

<div class="flash-notice"><?php echo tt('help_infopages_backend_main_admin'); ?></div>

<?php $this->widget('CustomGridView', array(
	'id'=>'infopages-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
			'id'=>'itemsSelected',
			'selectableRows' => '2',
			'htmlOptions' => array(
				'class'=>'center',
			),
		),
		array(
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "infopages-grid", 1, 1)',
			'headerHtmlOptions' => array(
				'class'=>'apartments_status_column',
			),
			'filter' => false,
			'sortable' => false,
		),
		array(
			'header' => tc('Name'),
			'name'=>'title_'.Yii::app()->language,
			'type'=>'raw',
			'value'=>'CHtml::encode($data->getStrByLang("title"))'
		),
		array(
			'header' => tt('Link', 'menumanager'),
			'type'=>'raw',
			'value'=>'($data->special == 0) ? $data->getUrl() : ""',
			'filter' => false,
			'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'template'=>'{view}{update}{delete}',
			'buttons' => array(
				'delete' => array(
					'visible' => '$data->special == 0',
				),
			),
		),
	),
));
/*
$this->renderPartial('//site/admin-select-items', array(
	'url' => '/infopages/backend/main/itemsSelected',
	'id' => 'infopages-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));*/
?>