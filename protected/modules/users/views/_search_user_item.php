<div class="user_item">
<?php

echo '<div class="user_item-ava">';
echo $data->renderAva();
echo  '<span class="user_item-login">';
echo $data->type == User::TYPE_AGENCY ? $data->agency_name : $data->username;
echo '</span>';
echo ', ' . $data->getTypeName();
echo '</div>';

echo '<div class="user_item-right">';

echo '<ul class="user_item-ul">';

$icon = CHtml::image(Yii::app()->baseUrl . '/images/design/phone-16.png');
echo '<li>' . $icon . ' <span class="user-list-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</li>';

$icon = CHtml::image(Yii::app()->baseUrl . '/images/design/ads-16.png') . ' ';
echo '<li>' . $icon . $data->getLinkToAllListings() . '</li>';
echo '</ul>';

echo '</div>';

echo '<div class="clear"></div>';

$additionalInfo = 'additional_info_'.Yii::app()->language;
if (isset($data->$additionalInfo) && !empty($data->$additionalInfo)){
    echo '<div class="clear"></div>';
    echo CHtml::encode(truncateText($data->$additionalInfo, 20));
}

?>
</div>
