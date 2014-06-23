<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment'),
);

$this->menu=array(
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);

$this->adminTitle = Yii::t('common', 'User managment');

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
		'disabled' => '$data->id == 1',
	),
	array(
		'name' => 'active',
		'header' => tt('Status'),
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnStatusHtml($data, "user-grid", 1, 1)',
		'headerHtmlOptions' => array(
			'class'=>'infopages_status_column',
		),
		'filter' => array(0 => tt('Inactive'), 1 => tt('Active')),
	),
    array(
        'name' => 'type',
        'value' => '$data->getTypeName()',
        'filter' => User::getTypeList(),
    ),
	array(
		'name' => 'username',
		'header' => tt('User name'),
	),
	'phone',
	'email',
);

if(issetModule('paidservices')){
	$columns[] = 'balance';
}

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'template'=>'{update}{delete}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'buttons' => array(
		'delete' => array(
			'visible' => '$data->id != 1',
		),
	)
);

$this->widget('CustomGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>$columns
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/users/backend/main/itemsSelected',
	'id' => 'user-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));

