<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Property type'); ?>:</div> </span>
    <span class="search">
    <?php
    echo CHtml::dropDownList(
        'objType',
        isset($this->objType) ? CHtml::encode($this->objType) : 0, CMap::mergeArray(array(0 => Yii::t('common', 'Please select')),
        Apartment::getObjTypesArray()),
        array('class' => $fieldClass)
    );
    Yii::app()->clientScript->registerScript('objType', '
		focusSubmit($("select#objType"));
	', CClientScript::POS_READY);
    ?>
    </span>
</div>