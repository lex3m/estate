<?php

$this->breadcrumbs=array(
	tt("FAQ")=>array('index'),
	tt("Manage FAQ"),
);

$this->menu=array(
	array('label'=>tt("Add FAQ"), 'url'=>array('/articles/backend/main/create')),
);
$this->adminTitle = tt('Manage FAQ');

$this->widget('CustomGridView', array(
	'id'=>'article-grid',
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
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "article-grid", 1)',
			'htmlOptions' => array('class'=>'infopages_status_column'),
			'filter' => false,
			'sortable' => false,
		),
		array (
            'header' => tt('Title / Question'),
            'name'=>'page_title_'.Yii::app()->language,
			//'htmlOptions' => array('class'=>'width120'),
			'sortable' => false,
			'type' => 'raw',
			'value' => 'CHtml::link(CHtml::encode($data->page_title),array("/articles/backend/main/view","id" => $data->id))',

		),
		array (
            'header' => tt('Body / Answer'),
            'name'=>'page_body_'.Yii::app()->language,
			'sortable' => false,
			'type' => 'raw',
			'value' => 'CHtml::decode(truncateText($data->page_body))',
		),
		array (
			'name' => 'date_updated',
			'headerHtmlOptions' => array('style' => 'width:130px;'),
			'filter' => false,
			'sortable' => false,
		),
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.TbButtonColumn',

			'template'=>'{up}{down}{view}{update}{delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'viewButtonUrl' => "Yii::app()->createUrl('/articles/backend/main/view', array('id' => \$data->id))",
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'buttons' => array(
				'up' => array(
					'label' => tc('Move an item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/articles/backend/main/move", array("id"=>$data->id, "direction" => "up"))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > 1',
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/articles/backend/main/move", array("id"=>$data->id, "direction" => "down"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < "'.$maxSorter.'"',
				),
			),
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/articles/backend/main/itemsSelected',
	'id' => 'article-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));
?>