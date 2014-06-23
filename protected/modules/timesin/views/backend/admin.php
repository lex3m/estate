<?php

$this->menu=array(
	array('label'=>tt('Add value', 'windowto'), 'url'=>array('create')),
);

$this->adminTitle = tt('Manage reference', 'windowto');

$this->widget('CustomGridView', array(
	'id'=>'timesin-grid',
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
			'name' => 'title_'.Yii::app()->language,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			//'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/timesin/backend/main/itemsSelected',
	'id' => 'timesin-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
