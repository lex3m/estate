<div class="boxList">
<?php
$this->pageTitle .= ' - '.Yii::t('common', 'Apartment search');

if ($filterName) {
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
		tt('all_by_filter', 'apartments') . ' "' . $filterName . '"',
	);

	$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
		'criteria' => $criteria,
		'widgetTitle' => tt('all_by_filter', 'apartments') . ' "' . $filterName . '"',
	));
}
else {
	$this->breadcrumbs=array(
		Yii::t('common', 'Apartment search'),
	);

	$wTitle = null;
	if(issetModule('rss')){
		$wTitle = '<a target="_blank" title="'.tt('rss_subscribe', 'rss').'" class="rss-icon" href="'
			.$this->createUrl('mainsearch', CMap::mergeArray($_GET, array('rss' => 1))).'"><img alt="'.tt('rss_subscribe', 'rss').'" src="'
			.Yii::app()->request->baseUrl.'/images/feed-icon-28x28.png" /></a>'
			.Yii::t('module_apartments', 'Apartments list');
	}

	$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
		'criteria' => $criteria,
		'count' => $apCount,
		'widgetTitle' => $wTitle,
	));
}
?>
</div>