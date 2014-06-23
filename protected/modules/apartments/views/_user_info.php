
    <?php
    $owner = $data->user;
    //echo '<h3>'.tc('Listing provided by').'</h3>';

    echo '<div class="user-info-ava">';
    echo $owner->renderAva();
    echo $owner->getNameForType();
    echo '</div>';

    echo '<div class="user-info-right">';

    echo '<ul class="user-info-ul">';

    if($data->canShowInView('phone')){
        $icon = CHtml::image(Yii::app()->baseUrl . '/images/design/phone-16.png');
        echo '<li>' . $icon . ' <span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</li>';
    }

    if (param('use_module_request_property') && $data->owner_id != Yii::app()->user->id){
        $icon = CHtml::image(Yii::app()->baseUrl . '/images/design/email-16.png') . ' ';
        echo '<li>' . $icon . CHtml::link(tt('request_for_property'), $data->getUrlSendEmail(), array('class'=>'fancy')) . '</li>';
    }

    $icon = CHtml::image(Yii::app()->baseUrl . '/images/design/ads-16.png') . ' ';
    echo '<li>' . $icon . $owner->getLinkToAllListings() . '</li>';
    echo '</ul>';

    echo '</div>';

    echo '<div class="clear"></div>';

    $additionalInfo = 'additional_info_'.Yii::app()->language;
    if (isset($data->user->$additionalInfo) && !empty($data->user->$additionalInfo)){
        echo '<div class="clear"></div>';
        echo CHtml::encode(truncateText($data->user->$additionalInfo, 20));
    }

    Yii::app()->clientScript->registerScript('generate-phone', '
				function generatePhone(){
					$("span#owner-phone").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $data->id)).'" />\');
				}
			', CClientScript::POS_END);
    ?>
