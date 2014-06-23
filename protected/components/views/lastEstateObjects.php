<span class="Novye_objects">Новые</span>
<div id="appartment_box" class="appartment_box">
<?php foreach($lastEstateObjects as $lastEstateObject):
    if(empty($apartments)){
        $criteria = new CDbCriteria(array(
            'limit'=>6,
            'order'=>'t.id DESC',
        ));

        $apartments = Apartment::findAllWithCache($criteria);
    }
    $findIds = $countImagesArr = array();
    foreach($apartments as $item) {
        $findIds[] = $item->id;
    }
    if (count($findIds) > 0)
        $countImagesArr = Images::getApartmentsCountImages($findIds);
    ?>
<div ap_id="29" lng="<?php echo $lastEstateObject->lng;?>" lat="<?php echo $lastEstateObject->lat;?>" class="appartment_item ">
    <div class="offer">
        <div align="left" class="offer-photo">
            <div style="position: relative;">
                <div class="apartment_count_img"><img src="<?php echo Yii::app()->baseUrl;?>/images/photo_count.png"><b><?php  if (isset($countImagesArr[$lastEstateObject->id])) echo $countImagesArr[$lastEstateObject->id]; ?></b></div>

                <div class="apartment_type"><?php echo Apartment::getNameByType($lastEstateObject->type); ?></div>

                <?php
                $res = Images::getMainThumb(150,100, $lastEstateObject->images);
                $img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
                    'title' => $lastEstateObject->getStrByLang('title'),
                    'class' => 'apartment_type_img'
                ));
                echo CHtml::link($img, $lastEstateObject->getUrl(), array('title' =>  $lastEstateObject->getStrByLang('title')));
                ?>
            </div>
        </div>
        <div class="offer-text">
            <div class="apartment-title">
                <?php
                $title = CHtml::encode($lastEstateObject->getStrByLang('title'));

                if($lastEstateObject->rating && !isset($booking)){
                    //$title = truncateText($item->getStrByLang('title'), 5);
                    if (utf8_strlen($title) > 21)
                        $title = utf8_substr($title, 0, 21) . '...';
                }
                else {
                    //$title = truncateText($item->getStrByLang('title'), 8);
                    if (utf8_strlen($title) > 65)
                        $title = utf8_substr($title, 0, 65) . '...';
                }
                echo CHtml::link($title,
                    $lastEstateObject->getUrl(), array('class' => 'offer'));
                ?>
            </div>
            <?php
            if($lastEstateObject->rating && !isset($booking)){
                echo '<div class="ratingview">';
                $this->widget('CStarRating',array(
                    'model'=>$item,
                    'attribute' => 'rating',
                    'readOnly'=>true,
                    'id' => 'rating_' . $item->id,
                    'name'=>'rating'.$item->id,
                ));
                echo '</div>';
            }
            ?>
            <div class="clear"></div>
            <?php if ($lastEstateObject->price_old!=0){ echo '<div class="new_price">'.$lastEstateObject->getPrettyPrice().'</div>'; ?>
                <p class="staraya">
                    <?php
                    echo $lastEstateObject->getOldPrettyPrice();
                    ?>
                </p>
            <?php } else { ?>
                <p class="cost">
                    <?php
                    echo $lastEstateObject->getPrettyPrice();
                    ?>
                </p>
            <?php } ?>
            <?php
            if( $lastEstateObject->floor || $lastEstateObject->floor_total || $lastEstateObject->square || $lastEstateObject->berths){
                echo '<p class="desc">';

                $echo = array();

                if($lastEstateObject->canShowInView('floor_all')){
                    if($lastEstateObject->floor && $lastEstateObject->floor_total){
                        $echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($lastEstateObject->floor, '{total}' => $lastEstateObject->floor_total));
                    } else {
                        if($lastEstateObject->floor){
                            $echo[] = $lastEstateObject->floor.' '.tt('floor', 'common');
                        }
                        if($lastEstateObject->floor_total){
                            $echo[] = tt('floors', 'common').': '.$lastEstateObject->floor_total;
                        }
                    }
                }

                if($lastEstateObject->canShowInView('square')){
                    $echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'total square: {n}', $lastEstateObject->square)." ".tc('site_square')."</span>";
                }
                if($lastEstateObject->canShowInView('berths')){
                    $echo[] = '<span class="nobr">'.Yii::t('module_apartments', 'berths').': '.CHtml::encode($lastEstateObject->berths)."</span>";
                }
                echo implode(', ', $echo);
                unset($echo);

                echo '</p>';
            }
            ?>
        </div>
        <?php if (issetModule('comparisonList')):?>
            <div class="clear"></div>
            <?php
            $inComparisonList = false;
            if (in_array($lastEstateObject->id, Yii::app()->controller->apInComparison))
                $inComparisonList = true;
            ?>
            <div class="row compare-check-control" id="compare_check_control_<?php echo $lastEstateObject->id; ?>">
                <?php
                $checkedControl = '';

                if ($inComparisonList)
                    $checkedControl = ' checked = checked ';
                ?>
                <input type="checkbox" name="compare<?php echo $lastEstateObject->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $lastEstateObject->id; ?>" <?php echo $checkedControl;?>>

                <a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $lastEstateObject->id; ?>" class="compare-label">
                    <?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
                </a>
            </div>
        <?php endif;?>
    </div>
</div>
<?php endforeach; ?>
</div>