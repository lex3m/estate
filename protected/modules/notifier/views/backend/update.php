<?php
$this->pageTitle=Yii::app()->name . ' - ' . tc('Mail editor');

$this->menu = array(
    array('label' => tc('Mail editor'), 'url' => array('admin')),
);
$this->adminTitle = tc('Edit');
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>