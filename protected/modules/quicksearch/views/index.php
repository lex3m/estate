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

    $subLocation = (int) Yii::app()->request->getParam('sublocation_id');
    $region = (int) Yii::app()->request->getParam('region_id');
    if (!empty($region)) {
       echo '<div id=\'location_description\'>';
       $content = Region::model()->findByPk($region)->content;
       $content = Apartment::excerpt($content);
       echo $content;
       echo '</div>' ;

       echo '<div style=\'display: none;\'  id=\'f_location_description\'>';
       echo Region::model()->findByPk($region)->content;
       echo '</div>' ;
       echo '<a href=\'#\' id=\'show_f_desc\'>Показать</a>';
    }
	$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
		'criteria' => $criteria,
		'count' => $apCount,
		'widgetTitle' => $wTitle,
	));
}
?>
</div>
<?php
Yii::app()->clientScript->registerScript('testscript',"
var showDescIsShow = false;
$('#show_f_desc').on('click', function(){
    if (!showDescIsShow)
    {
        $(this).text('Скрыть');
        $('#location_description').hide();
        $('#f_location_description').show();
        showDescIsShow = true;
    }
    else
    {
        $(this).text('Показать');
        $('#f_location_description').hide();
        $('#location_description').show();
        showDescIsShow = false;
    }
    return false;
});
",CClientScript::POS_READY);