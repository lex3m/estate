<?php
if (is_array($ads) && count($ads) > 0) {
	echo '<div class="similar-ads" id="similar-ads">';
		echo '<span class="viewapartment-subheader">'.tt('Similar ads', 'similarads').'</span>';
		echo '<ul id="mycarousel" class="jcarousel-skin-tango">';
			foreach ($ads as $item) {
				echo '<li>';
					echo '<a href="'.$item->getUrl().'">';
						$res = Images::getMainThumb(150, 100, $item->images);
						echo CHtml::image($res['thumbUrl'], '', array(
							'title' => $item->{'title_'.Yii::app()->language},
							'width' => 150,
							'height' => 100,
						));
					echo '</a>';
					if($item->getStrByLang('title')){
						echo '<div class="similar-descr">'.truncateText(CHtml::encode($item->getStrByLang('title')), 6).'</div>';
					}
					echo '<div class="similar-price">'.tt('Price from', 'apartments').': '.$item->getPrettyPrice().'</div>';
				echo '</li>';
			}
		echo '</ul>';
	echo '</div>';

	if (count($ads) > 5) {
		Yii::app()->clientScript->registerScript('similar-ads-slider', '
			$("#mycarousel").jcarousel({ visible: 5});
		', CClientScript::POS_READY);
	}
	else {
		Yii::app()->clientScript->registerScript('similar-ads-slider', '
			$("#mycarousel").jcarousel({ visible: 5, buttonNextHTML: null, buttonPrevHTML: null});
		', CClientScript::POS_READY);
	}
}
?>