<?php
$this->pageTitle=Yii::app()->name . ' - ' . NewsModule::t('Manage news');


$this->menu = array(
	array('label' => NewsModule::t('Add news'), 'url' => array('create')),
);
$this->adminTitle = NewsModule::t('Manage news');
?>

<?php $this->widget('CustomGridView', array(
	'id'=>'news-grid',
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
			'name'=>'title_'.Yii::app()->language,
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->getStrByLang("title")), $data->url)'
		),
		array(
			'name'=>'dateCreated',
			'type'=>'raw',
			'filter'=>false,
			'htmlOptions' => array('style' => 'width:130px;'),
		),
		array(
			//'class'=>'CButtonColumn',
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'viewButtonUrl' => '$data->url',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/news/backend/main/itemsSelected',
	'id' => 'news-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>