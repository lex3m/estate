<?php
$this->pageTitle .= ' - '.Yii::t('module_specialoffers', 'Special offers');
$this->breadcrumbs=array(
	Yii::t('module_specialoffers', 'Special offers'),
);

if (isset($_GET['is_ajax'])) {
	Yii::app()->clientScript->registerCoreScript('jquery');
	Yii::app()->clientScript->registerCoreScript('jquery.ui');
	Yii::app()->clientScript->registerCoreScript('rating');
	Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl().'/rating/jquery.rating.css');
}

?>
<?php
$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
	'criteria' => $criteria,
	'widgetTitle' => Yii::t('common', 'Special offers'),
	'breadcrumbs' => array(
		Yii::t('module_specialoffers', 'Special offers'),
	),
));
?>
