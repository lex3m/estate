<?php
if (param('qrcode_in_listing_view', 1)) {
    echo '<div class="floatright ' . ($data->is_special_offer ? 'qrcode_spec' : 'qrcode') . '" >';
    $this->widget('application.extensions.qrcode.QRCodeGenerator', array(
        'data' => $data->URL,
        'filename' => 'listing_' . $data->id . '-' . Yii::app()->language . '.png',
        'matrixPointSize' => 3,
        'fileUrl' => Yii::app()->getBaseUrl(true) . '/uploads',
        'color' => array(33, 72, 131),
    ));
    echo '</div>';
}

echo '<dl class="ap-descr">';
echo '<dt>' . tt('Apartment ID') . ':</dt><dd>' . $data->id . '</dd>';

if ($data->canShowInView('floor_all')) {
    if ($data->floor || $data->floor_total) {
        if ($data->floor && $data->floor_total) {
            echo '<dt>' . tc('Floor') . ':</dt>';
            echo '<dd>' . Yii::t('module_apartments', '{n} floor of {total} total', array($data->floor, '{total}' => $data->floor_total)) . '</dd>';
        } else {
            if ($data->floor) {
                echo '<dt>' . tc('Floor') . ':</dt>';
                echo '<dd>' . $data->floor . '</dd>';
            }
            if ($data->floor_total) {
                echo '<dt>' . tt('Total number of floors', 'apartments') . ':</dt>';
                echo '<dd>' . $data->floor_total . '</dd>';
            }
        }
    }
}

if ($data->canShowInView('square')) {
    echo '<dt>' . Yii::t('module_apartments', 'Total square') . ':</dt><dd>' . $data->square . ' ' . tc('site_square') . '</dd>';
}

if ($data->canShowInView('land_square')) {
    echo '<dt>' . Yii::t('module_apartments', 'Land square') . ':</dt><dd>' . $data->land_square . ' ' . tc('site_land_square') . '</dd>';
}

if ($data->canShowInView('berths') && $data->berths) {
    echo '<dt>' . Yii::t('module_apartments', 'Number of berths') . ':</dt><dd>' . CHtml::encode($data->berths) . '</dd>';
}

if ($data->canShowInView('window_to') && $data->windowTo->getTitle()) {
    echo '<dt>' . tt('window to') . ':</dt><dd>' . CHtml::encode($data->windowTo->getTitle()) . '</dd>';
}

if ($data->canShowInView('description')) {
    echo '<dt>' . tt('Description') . ':</dt><dd>' . CHtml::encode($data->getStrByLang('description')) . '</dd>';
}

if ($data->canShowInView('description_near')) {
    echo '<dt>' . tt('Near') . ':</dt><dd>' . CHtml::encode($data->getStrByLang('description_near')) . '</dd>';
}

if ($data->canShowInView('address')) {
    $adressFull = '';

    if (issetModule('location') && param('useLocation', 1)) {
        if ($data->locCountry || $data->locRegion || $data->locCity)
            $adressFull = ' ';

        if ($data->locCountry) {
            $adressFull .= $data->locCountry->getStrByLang('name');
        }
        if ($data->locRegion) {
            if ($data->locCountry)
                $adressFull .= ',&nbsp;';
            $adressFull .= $data->locRegion->getStrByLang('name');
        }
        if ($data->locCity) {
            if ($data->locCountry || $data->locRegion)
                $adressFull .= ',&nbsp;';
            $adressFull .= $data->locCity->getStrByLang('name');
        }
    } else {
        if (isset($data->city) && isset($data->city->name)) {
            $cityName = $data->city->name;
            if ($cityName) {
                $adressFull = ' ' . $cityName;
            }
        }
    }
    $adress = CHtml::encode($data->getStrByLang('address'));
    if ($adress) {
        $adressFull .= ', ' . $adress;
    }
    if ($adressFull) {
        echo '<dt>' . tt('Address') . ':</dt><dd>' . $adressFull . '</dd>';
    }
}

if ($data->type == Apartment::TYPE_CHANGE && $data->getStrByLang('exchange_to')) {
    echo '<dt>' . tt('Exchange to', 'apartments') . ':</dt><dd>' . CHtml::encode($data->getStrByLang('exchange_to')) . '</dd>';
}

if ($data->canShowInView('note')) {
    echo '<dt>' . Yii::t('module_apartments', 'Note') . ':</dt><dd>' . $data->note . '</dd>';
}

if (issetModule('formeditor')) {
    Yii::import('application.modules.formeditor.models.HFormEditor');
    $rows = HFormEditor::getGeneralFields();
    HFormEditor::renderViewRows($rows, $data);
}

echo '</dl>';
