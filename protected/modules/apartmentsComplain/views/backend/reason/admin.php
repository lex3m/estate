<?php
$this->breadcrumbs=array(
	tt('Reasons of complain'),
);

$this->menu=array(
	array('label'=> tt('Complains'), 'url'=>array('/apartmentsComplain/backend/main/admin')),
	array('label'=>tc('Add'), 'url'=>array('/apartmentsComplain/backend/complainreason/create')),
);

$this->adminTitle = tt('Reasons of complain');

$this->widget('CustomGridView', array(
	'id'=>'complain-reason-grid',
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
			'header' => tc('Name'),
			'name' => 'name_'.Yii::app()->language,
			'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{up}{down}{update}{delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'buttons' => array(
				'up' => array(
					'label' => tc('Move an item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentsComplain/backend/complainreason/move", array("id"=>$data->id, "direction" => "up"))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > 1',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'complain-reason-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentsComplain/backend/complainreason/move", array("id"=>$data->id, "direction" => "down"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < "'.$maxSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'complain-reason-grid'); return false;}",
				),
			),
			'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").html(data); }'
		),
	),
)); ?>

<?php
$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartmentsComplain/backend/complainreason/itemsSelected',
	'id' => 'complain-reason-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>