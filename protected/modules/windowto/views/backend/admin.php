<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference (window to..)'),
);

$this->menu=array(
	/*array('label'=>tt('Manage reference (window to..)'), 'url'=>array('index')),
	array('label'=>tt('Add value'), 'url'=>array('create')),*/
	array('label'=>tt('Add value'), 'url'=>array('/windowto/backend/main/create')),
);

$this->adminTitle = tt('Manage reference (window to..)');

$this->widget('CustomGridView', array(
	'id'=>'windowto-grid',
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
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/windowto/backend/main/itemsSelected',
	'id' => 'windowto-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>