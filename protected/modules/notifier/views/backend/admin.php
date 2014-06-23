<?php
$this->pageTitle=Yii::app()->name . ' - ' . tc('Mail editor');

$this->adminTitle = tc('Mail editor');
?>

<?php $this->widget('CustomGridView', array(
    'id'=>'news-grid',
    'dataProvider'=>$model->active()->search(),
    'filter'=>$model,
    'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
    'columns'=>array(

//        array(
//            'name' => 'status',
//            'value' => '$data->getStatusName()',
//            'filter' => NotifierModel::getStatusList(),
//        ),
//        array(
//            'name' => 'event',
//        ),
        array(
            'header' => tc('Subject'),
            'value' => '$data->subject',
        ),

        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update}',
        ),
    ),
));

?>