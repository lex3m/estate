<?php
$this->breadcrumbs=array(
	tt('Manage langs'),
);

$this->menu = array(
	array('label'=>tt('Add lang'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage langs');

$this->widget('CustomGridView', array(
	'id'=>'langs-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
		array(
			'header' => tc('Status'),
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "langs-grid", 0,
				$data->active ? array(Lang::getDefaultLangId(), Lang::getAdminMailLangId()) : !$data->currency->active)',
			'headerHtmlOptions' => array(
				'class'=>'apartments_status_column',
			),
			'filter' => false,
			'sortable' => false,

		),
		array(
            'header' => tc('Default'),
            'type' => 'raw',
            'value' => '$data->getIsDefaultHtml(0)',
            'filter' => false,
            'sortable' => false,
            'htmlOptions' => array(
                'class'=>'width100 center',
            ),
        ),
		array(
            'name' => 'admin_mail',
            'type' => 'raw',
            'value' => '$data->getIsDefaultHtml(1)',
            'filter' => false,
            'sortable' => false,
            'htmlOptions' => array(
                'class'=>'width100 center',
            ),
        ),
		array(
			'name' => 'flag_img',
			'type' => 'raw',
			'value' => 'CHtml::image($data->getFlagUrl())',
			'sortable' => false,
			'filter' => false,
			'htmlOptions' => array(
				'class'=>'width100 center',
			),
		),
		array(
			'header' => tc('Language'),
			'value' => '$data->name_'.Yii::app()->language,
			'sortable' => false,
		),
		array(
			'header' => tc('Currency'),
			'value' => '$data->currency->char_code',
			'sortable' => false,
			'filter' => false,
		),
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{up}{down}{update}{delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
				'htmlOptions' => array('class'=>'infopages_buttons_column'),
				'buttons' => array(
					'down' => array(
						'label' => tc('Move an item down'),
						'imageUrl' => Yii::app()->assetManager->publish(
							Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
						),
						'url'=>'Yii::app()->createUrl("/lang/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid" => "0"))',
						'options' => array('class'=>'infopages_arrow_image_up'),

						'visible' => '$data->sorter < "'.$maxSorter.'"',
						'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'langs-grid'); return false;}",
					),
					'up' => array(
						'label' => tc('Move an item up'),
						'imageUrl' => Yii::app()->assetManager->publish(
							Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
						),
						'url'=>'Yii::app()->createUrl("/lang/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid" => "0"))',
						'options' => array('class'=>'infopages_arrow_image_down'),
						'visible' => '$data->sorter > 1',
						'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'langs-grid'); return false;}",
					),
					'delete' => array(
						'visible' => '$data->name_iso != Lang::getDefaultLang() && $data->name_iso != Lang::getAdminMailLang()'
					)
				),
		),
	),
));
Yii::app()->clientScript->registerScript('setDefLang', "
    function changeDefault(id, admin_mail){
        $.ajax({
            type: 'POST',
            url: '".Yii::app()->request->baseUrl."/lang/backend/main/setDefault',
            data: { 'id' : id, 'admin_mail' : admin_mail },
			success: function(msg){
				$('#currency-grid').yiiGridView.update('langs-grid');
        }
        });
        return;
    }",
	CClientScript::POS_END);


?>