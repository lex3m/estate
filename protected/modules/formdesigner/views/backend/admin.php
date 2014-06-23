<?php
$this->adminTitle = tc('The forms designer');

if(issetModule('formeditor')){
    $this->menu = array(
        array('label'=>tt('Add field', 'formeditor'), 'url'=>array('/formeditor/backend/main/create')),
        array('label'=>tt('Edit search form', 'formeditor'), 'url'=>array('/formeditor/backend/search/editSearchForm')),
    );
}

Yii::app()->clientScript->registerScript('search', "
$('#form-designer-filter').submit(function(){
    $('#form-designer-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});

function ajaxSetVisible(elem){
	$.ajax({
		url: $(elem).attr('href'),
		success: function(){
			$('#form-designer-grid').yiiGridView.update('form-designer-grid');
		}
	});
}
");

$this->widget('CustomGridView', array(
    'id'=>'form-designer-grid',
    'dataProvider'=>$model->search(),
    'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
    //'filter'=>$model,
    'columns'=>array(
        array(
            'name' => 'field',
            'value' => '$data->getLabel()'
        ),

        array(
            'header' => tt('Show for property types', 'formdesigner'),
            'value' => '$data->getTypesHtml()',
            'type' => 'raw',
            'sortable' => false,
        ),

        array(
            'name' => 'tip',
        ),

        array(
            'name' => 'visible',
            'value' => '$data->getVisibleHtml()',
            'type' => 'raw',
            'sortable' => false,
        ),

        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}{delete}',
            'buttons' => array(
                'update' => array(
                    'url' => '$data->getUpdateUrl()',
                ),
                'delete' => array(
                    'visible' => '$data->type != FormDesigner::TYPE_DEFAULT',
                    'url' => 'Yii::app()->createUrl("/formeditor/backend/main/delete", array("id" => $data->id))'
                ),
            )
        ),
    ),
));
?>
