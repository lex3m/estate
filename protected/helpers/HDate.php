<?php

class HDate {
    public static function formatDateTime($dateTime, $format = 'default'){
        $dateFormat = param('dateFormat', 'd.m.Y H:i:s');

        if($format == 'default'){
            return date($dateFormat, strtotime($dateTime));
        } else {
            return Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), CDateTimeParser::parse($dateTime, 'yyyy-MM-dd hh:mm:ss'));
        }
    }
}