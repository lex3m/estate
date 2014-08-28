<div style="display:none;" class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Select region'); ?>:</div></span>

    <?php

    echo CHtml::dropDownList(
        'region_id',
        '',
        array(),
        array(
            'id'=>'selectRegion',
            'class' => 'width285 height17 searchField',
            'style'=>'height: 29px'
            ) //$fieldClass.
    );

    SearchForm::setJsParam('cityField', array('minWidth' => $minWidth)); //

    ?>
</div>

