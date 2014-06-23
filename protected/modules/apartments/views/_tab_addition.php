<?php
if(issetModule('formeditor')){
    echo '<dl class="ap-descr">';
    Yii::import('application.modules.formeditor.models.HFormEditor');
    $rows = HFormEditor::getExtendedFields();
    HFormEditor::renderViewRows($rows, $data);
    echo '</dl>';
}

	$prev = '';
	$column1 = 0;
	$column2 = 0;
	$column3 = 0;

	foreach($data->references as $item){
		if($item['title']){
			if($prev != $item['style']){
				$column2 = 0;
				$column3 = 0;
				echo '<div class="clear"></div>';
			}
			$$item['style']++;
			$prev = $item['style'];
			echo '<div class="'.$item['style'].'">';
			echo '<span class="viewapartment-subheader">'.CHtml::encode($item['title']).'</span>';
			echo '<ul class="apartment-description-ul">';
			foreach($item['values'] as $key => $value){
				if($value){
					if (param('useReferenceLinkInView')) {
						echo '<li><span>'.CHtml::link(CHtml::encode($value), $this->createAbsoluteUrl('/service-'.$key)).'</span></li>';
					}
					else {
						echo '<li><span>'.CHtml::encode($value).'</span></li>';
					}
				}
			}
			echo '</ul>';
			echo '</div>';
			if(($item['style'] == 'column2' && $column2 == 2)||$item['style'] == 'column3' && $column3 == 3){
				echo '<div class="clear"></div>';
			}

		}
	}
	?>
	<div class="clear"></div>
