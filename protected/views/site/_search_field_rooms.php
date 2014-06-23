<div class="<?php echo $divClass; ?>">
    <?php
    if (issetModule('selecttoslider') && param('useRoomSlider') == 1) {
    ?>
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Rooms range'); ?>:</div> </span>
        <span class="search">
            <?php
            $roomItems = array_merge(
                range(0, param('moduleApartments_maxRooms', 8))
            );
            $roomsMin = isset($this->roomsCountMin) ? CHtml::encode($this->roomsCountMin) : 0;
            $roomsMax = isset($this->roomsCountMax) ? CHtml::encode($this->roomsCountMax) : max($roomItems);

            SearchForm::renderSliderRange(array(
                'field' => 'room',
                'min' => 0,
                'max' => param('moduleApartments_maxRooms', 8),
                'min_sel' => $roomsMin,
                'max_sel' => $roomsMax,
                'step' => 1,
                'class' => 'rooms-search-select',
            ));
            echo '</span>';

            } else {
                ?>
                <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Number of rooms'); ?>:</div> </span>
                <?php
                $roomItems = array(
                    '0' => Yii::t('common', 'Any'),
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => Yii::t('common', '4 and more'),
                );
                echo CHtml::dropDownList('rooms', isset($this->roomsCount) ? CHtml::encode($this->roomsCount) : 0, $roomItems, array('class' => $fieldClass . ' searchField'));

                Yii::app()->clientScript->registerScript('rooms', '
            focusSubmit($("select#rooms"));
        ', CClientScript::POS_READY);
            }
            ?>
</div>