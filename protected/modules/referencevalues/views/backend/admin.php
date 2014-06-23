<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference values'),
);

$this->menu=array(
	array('label'=>tt('Create value'), 'url'=>array('/referencevalues/backend/main/create')),
);

$this->adminTitle = tt('Manage reference values');

$this->widget('CustomGridView', array(
	'id'=>'reference-values-grid',
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
			'header' => tt('Category'),
			'value' => '$data->category->getTitle()',
			'filter' => CHtml::dropDownList('ReferenceValues[category_filter]', $currentCategory, $this->getCategories()),
			'htmlOptions' => array(
				//'class' => 'referencevalues_category_column',
				//'onChange' => '',
			),
		),
		array(
			'header' => tc('Name'),
			'name' => 'title_'.Yii::app()->language,
			'sortable' => false,
		),
        array(
            'name' => 'for_sale',
            'type' => 'raw',
            'value' => 'ReferenceValues::returnForStatusHtml($data, "for_sale", "reference-values-grid")',
            'sortable' => false,
            'filter' => false
        ),
        array(
            'name' => 'for_rent',
            'type' => 'raw',
            'value' => 'ReferenceValues::returnForStatusHtml($data, "for_rent", "reference-values-grid")',
            'sortable' => false,
            'filter' => false
        ),
		array(
			//'class'=>'CButtonColumn',
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
					'url'=>'Yii::app()->createUrl("/referencevalues/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid"=>$data->category->id))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > 1',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'reference-values-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/referencevalues/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid"=>$data->category->id))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < Yii::app()->controller->maxSorters[$data->reference_category_id]',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'reference-values-grid'); return false;}",
				),
			),
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/referencevalues/backend/main/itemsSelected',
	'id' => 'reference-values-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
