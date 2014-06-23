<?php
$this->pageTitle=Yii::app()->name . ' - ' . PublicationsModule::t('Manage publications');

$this->menu = array(
    array('label' => PublicationsModule::t('Add publication'), 'url' => array('create')),
);
$this->adminTitle = PublicationsModule::t('Manage publications');
?>

<div class="flash-notice"><?php echo tt('help_info_publications'); ?></div>

<?php $this->widget('CustomGridView', array(
    'id'=>'punlications-grid',
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
            'name'=>'name',
            'type'=>'raw',
            'value'=>'CHtml::encode($data->name)'
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
            'deleteButtonUrl'=>'Yii::app()->controller->createUrl("deletePublication",array("id"=>$data->id))',
            'template'=>'{view}{delete}',
       //     'delete' =>array('js:function(){ alert("new function"); }'),
       //     'afterDelete' =>array('js:function(){ alert("new function"); }'),
            'buttons' => array(
                'delete' =>array('js:function(){ alert("new function"); }'),
                'afterDelete' =>array('js:function(){ alert("new function"); }'),
            ),           
        ),
    ),
));
