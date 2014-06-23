<div class="form">

<?php
	Yii::app()->user->setFlash('help', Yii::t('module_lang', 'help upload icon', array('flag_dir' => Lang::FLAG_DIR)));

	Lang::publishAssetsDD();

	$form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="rowold">
		<?php echo $form->labelEx($model,'flag_img'); ?>
		<?php
		$flags = Lang::getFlagImgArray();
		echo '<select name="Lang[flag_img]" id="flag_img">';
		foreach($flags as $flag){
			$selected = $model->flag_img == $flag ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="'.$flag.'" title="'.Yii::app()->baseUrl.Lang::FLAG_DIR.$flag.'">'.$flag.'</option>';
		}
		echo '</select>';
		?>
		<?php echo $form->error($model,'flag_img'); ?>
    </div>

	<br/>

	<?php if($model->isNewRecord) { ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'name_iso'); ?>
		<?php echo $form->dropDownList($model,'name_iso', Lang::getISOlangForAdd()); ?>
		<?php echo $form->error($model,'name_iso'); ?>
	</div>

    <br/>

	<?php
		$activeLangs = Lang::getActiveLangsTranslated();
		$activeLangs[0] = tt('do_not_copy', 'lang');
	?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'copy_lang_from'); ?>
		<?php echo $form->dropDownList($model,'copy_lang_from', $activeLangs, array('class' => 'width150')); ?>
		<?php echo $form->error($model,'copy_lang_from'); ?>
	</div>

	<?php } else { ?>
	<div class="rowold">
		<b><?php echo tt('Name ISO'); ?></b>: <?php echo $model->name_iso.' ('.Lang::getISOname($model->name_iso).')'; ?>
	</div>
	<?php } ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'currency_id'); ?>
		<?php echo $form->dropDownList($model,'currency_id', Currency::getCurrencyArray(true), array('class' => 'width150')); ?>
		<?php echo $form->error($model,'currency_id'); ?>
	</div>

	<?php
	$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $model,
			'field' => 'name',
			'type' => 'string'
		));
	?>

	<div class="clear"></div>

	<div class="rowold buttons">
		   <?php $this->widget('bootstrap.widgets.TbButton',
					   array('buttonType'=>'submit',
						   'type'=>'primary',
						   'icon'=>'ok white',
						   'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
					   )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(document).ready(function(e) {
        try {
            $("#flag_img").msDropDown();
        } catch(e) {
            alert(e.message);
        }
    });
</script>
