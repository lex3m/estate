<?php
//$this->pageTitle .= ' - '.Yii::t('common', 'Apartment search');

$this->pageTitle .= ' - '.tt('all_member_listings', 'apartments') . ' '.$username;
$this->breadcrumbs=array(
	tt('all_member_listings', 'apartments').' '.$username,
);
?>

<!--<h1><?php // echo Yii::t('common', 'Quick search') ?></h1>-->
<div class="boxList">
<?php $this->widget('application.modules.apartments.components.ApartmentsWidget', array(
	'criteria' => $criteria,
	'count' => $apCount,
	'widgetTitle' => tt('all_member_listings', 'apartments'). ' '.$username,
)); ?>
</div>               
