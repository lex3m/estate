<div class="form">
	<?php
		$form=$this->beginWidget('CustomForm', array(
			'id'=>'InfoPages-form',
			'enableClientValidation'=>false,
		));
	?>

	<p class="note">
		<?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?>
	</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->labelEx($model,'active'); ?>
	<?php echo $form->dropDownList($model, 'active', array(
		InfoPages::STATUS_ACTIVE => tc('Active'),
		InfoPages::STATUS_INACTIVE => tc('Inactive'),
	), array('class' => 'width150')); ?>
	<?php echo $form->error($model,'active'); ?>

	<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $model,
			'field' => 'title',
			'type' => 'string'
		));
	?>
	<div class="clear"></div>

	<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $model,
			'field' => 'body',
			'type' => 'text-editor'
		));
	?>
    <div class="clear"></div>

	<div class="rowold">
        <?php echo $form->labelEx($model,'widget'); ?>
        <?php echo $form->dropDownList($model,'widget', InfoPages::getWidgetOptions()); ?>
        <?php echo $form->error($model,'widget'); ?>
    </div>

    <div class="rowold">
        <?php echo $form->labelEx($model,'widget_position'); ?>
        <?php echo $form->dropDownList($model,'widget_position', InfoPages::getPositionList()); ?>
        <?php echo $form->error($model,'widget_position'); ?>
    </div>

    <?php echo $this->renderPartial('_form_apartments_filter'); ?>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
			));
		?>
	</div>

<?php $this->endWidget(); ?>

	<div class="clear"></div>
	<?php
	if (issetModule('seo') && !$model->isNewRecord) {
		$this->widget('application.modules.seo.components.SeoWidget', array(
			'model' => $model,
			'canUseDirectUrl' => true,
		));
	}
	?>
</div><!-- form -->

<script type="text/javascript">
    $(function(){
        checkWidget();

        $('#InfoPages_widget').change(function(){
            checkWidget();
        });
    });

    function checkWidget(){
        var el = $('#InfoPages_widget');
        console.log(el.val());
        if(el.val() == 'apartments'){
            $('#apartments_filter').show();
        } else {
            $('#apartments_filter').hide();
        }
    }
</script>