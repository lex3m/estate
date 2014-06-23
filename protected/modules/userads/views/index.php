<?php
Yii::app()->getModule('userads');

//if(!Yii::app()->request->isAjaxRequest){
//    echo '<h1>'.tt('Manage apartments', 'apartments').'</h1>';
//}

$this->pageTitle .= ' - '.tt('Manage apartments', 'apartments');
if (!isset($this->breadcrumbs)) {
	$this->breadcrumbs = array(
		Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
		tt('Manage apartments', 'apartments')
	);
}

//echo CHtml::button(tc('Add ad', 'apartments'), array('onclick' => 'document.location.href="'.Yii::app()->createUrl('/userads/main/create').'"', 'class' => 'button-blue'));

Yii::app()->clientScript->registerScript('ajaxSetStatus', "
		function ajaxSetStatus(elem, id){
			$.ajax({
				url: $(elem).attr('href'),
				success: function(){
					$('#'+id).yiiGridView.update(id);
				}
			});
		}
	",
    CClientScript::POS_HEAD);


$columns = array(
	array(
		'name' => 'id',
		'headerHtmlOptions' => array(
			'class'=>'apartments_id_column',
		),
	),
	array(
		'name' => 'active',
		'type' => 'raw',
		'value' => 'UserAds::returnStatusHtml($data, "userads-grid", 0)',
		'headerHtmlOptions' => array(
			'class'=>'userads_status_column',
		),
		'filter' => Apartment::getModerationStatusArray(),
		'sortable' => false,
	),

	array(
		'name' => 'owner_active',
		'type' => 'raw',
		'value' => 'UserAds::returnStatusOwnerActiveHtml($data, "userads-grid", 1)',
		'headerHtmlOptions' => array(
			'class'=>'userads_owner_status_column',
		),
		'filter' => array(
			'0' => tc('Inactive'),
			'1' => tc('Active'),
		),
		'sortable' => false,
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'Apartment::getNameByType($data->type)',
		'filter' => Apartment::getTypesArray(),
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'sortable' => false,
	),
	array(
		'name' => 'obj_type_id',
		'type' => 'raw',
		'value' => '(isset($data->objType) && $data->objType) ? $data->objType->name : ""',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getObjTypesArray(),
		'sortable' => false,
	),
	array(
		'header' => tc('Name'),
		'name' => 'title_'.Yii::app()->language,
		'type' => 'raw',
		'value' => 'CHtml::link(CHtml::encode($data->{"title_".Yii::app()->language}), $data->getUrl())',
	),
);

if (issetModule('location') && param('useLocation', 1)) {
    $columns[]=array(
        'name' => 'loc_country',
        'value' => '$data->loc_country ? $data->locCountry->name : ""',
        'htmlOptions' => array(
            'style' => 'width: 150px;',
        ),
        'sortable' => false,
        'filter' => Country::getCountriesArray(0, 1),
    );
    $columns[]=array(
        'name' => 'loc_region',
        'value' => '$data->loc_region ? $data->locRegion->name : ""',
        'htmlOptions' => array(
            'style' => 'width: 150px;',
        ),
        'sortable' => false,
        'filter' => Region::getRegionsArray($model->loc_country, 0, 1),
    );
    $columns[]=array(
        'name' => 'loc_city',
        'value' => '$data->loc_city ? $data->locCity->name : ""',
        'htmlOptions' => array(
            'style' => 'width: 150px;',
        ),
        'sortable' => false,
        'filter' => City::getCitiesArray($model->loc_region, 0, 1),
    );
} else {
    $columns[]=array(
        'name' => 'city_id',
        'value' => '$data->city_id ? $data->city->name : ""',
//        'htmlOptions' => array(
//            'style' => 'width: 150px;',
//        ),
        'sortable' => false,
        'filter' => ApartmentCity::getAllCity(),
    );
}

if(issetModule('paidservices')){
	$columns[] = array(
		'header'=>tc('Paid services'),
		'value'=>'$data->getPaidHtml(false, false, true)',
		'type'=>'raw',
		'htmlOptions' => array(
            'class' => 'width70 center',
		),
	);
}

$columns[] = array(
	'class'=>'CButtonColumn',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'viewButtonUrl' => '$data->getUrl()',
    'buttons' => array(
        'update' => array(
            'url' => 'Yii::app()->createUrl("/userads/main/update", array("id" => $data->id))',
        ),
        'delete' => array(
            'url' => 'Yii::app()->createUrl("/userads/main/delete", array("id" => $data->id))',
        ),
    ),
);

$this->widget('NoBootstrapGridView', array(
	'id'=>'userads-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>$columns
)); ?>
