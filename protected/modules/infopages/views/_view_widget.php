<?php
echo '<div class="clear"></div><div>';
Yii::import('application.modules.'.$model->widget.'.components.*');
$widgetData = array();

switch($model->widget){
    case 'contactform':
        $widgetData = array('page' => 'index');
        break;

    case 'apartments':
        $widgetData = array('criteria' => $model->getCriteriaForAdList());
        break;
}
$this->widget(ucfirst($model->widget).'Widget', $widgetData);

echo '</div>';
echo '<div class="clear"></div>';
?>