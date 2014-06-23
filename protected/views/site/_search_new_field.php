<?php if($search->formdesigner){ ?>
<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo $search->getLabel(); ?>:</div> </span>
    <?php
    if($search->formdesigner->type == FormDesigner::TYPE_INT){
        $width = 'search-input-new width70';
    } elseif($search->formdesigner->type == FormDesigner::TYPE_REFERENCE){
        $width = 'search-input-new width290';
    } else {
        $width = 'search-input-new width285';
    }

    $value = isset($this->newFields[$search->field]) ? CHtml::encode($this->newFields[$search->field]) : '';

    echo '<span class="search">';

    if($search->formdesigner->type == FormDesigner::TYPE_REFERENCE){
        echo CHtml::dropDownList($search->field, $value, CMap::mergeArray(array(0 => Yii::t('common', 'Please select')), FormDesigner::getListByCategoryID($search->formdesigner->reference_id)),
            array('class' => 'searchField ' . $fieldClass)
        );
    }else{
        echo CHtml::textField($search->field, $value, array(
            'class' => $width,
            'onChange' => 'changeSearch();',
        ));

        if($search->formdesigner->type == FormDesigner::TYPE_INT && $search->formdesigner->measure_unit){
            echo '&nbsp;<span>' . $search->formdesigner->measure_unit.'</span>';
        }
    }

    echo '</span>';
    ?>
</div>
<?php } ?>