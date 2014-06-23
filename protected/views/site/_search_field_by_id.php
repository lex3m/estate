<div class="<?php echo $divClass; ?>">
	<span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Apartment ID'); ?>:</div> </span>
	<?php
	echo CHtml::textField('sApId', (isset($this->sApId) && $this->sApId) ? CHtml::encode($this->sApId) : '', array(
        'class' => 'width70 search-input-new',
        'onChange' => 'changeSearch();',
    ));
	Yii::app()->clientScript->registerScript('sApId', '
		focusSubmit($("input#sApId"));
	', CClientScript::POS_READY);
	?>
</div>