<?php

$this->pageTitle=Yii::app()->name . ' - ' . tt('Manage lang messages', 'translateMessage');

$this->menu=array(
	array('label'=>tt('Add message'), 'url'=>array('create')),
);

$this->adminTitle = tt('Manage lang messages', 'translateMessage');

$this->widget('CustomGridView', array(
	'id'=>'translate-message-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
        array(
            'name' => 'status',
            'filter' => TranslateMessage::getStatusArray(),
            'type' => 'raw',
            'value' => '$data->getStatusHtml()',
            'htmlOptions' => array(
                'class'=>'width120',
            ),
        ),
        array(
            'name' => 'category',
            'filter' => TranslateMessage::getCategoryFilter(),
            'htmlOptions' => array(
                'class'=>'width200',
            ),
        ),
        'message',
        'translation',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			//'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
)); ?>
