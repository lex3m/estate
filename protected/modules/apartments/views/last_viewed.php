<?php
$this->pageTitle .= ' - '.Yii::t('common', 'Last viewed apartments');

$this->breadcrumbs=array(
    tt('Last viewed apartments', 'common'),
);
?>

<div class="boxList">
    <?php $this->widget('application.modules.apartments.components.ApartmentsWidget', array(
        'criteria' => $criteria,
        'count' => $apCount,
        'widgetTitle' => tt('Last viewed apartments', 'common'),
    )); ?>
</div>               
