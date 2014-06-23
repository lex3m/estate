<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'reference_category_id'); ?>
		<?php echo $form->dropDownList($model,'reference_category_id', $this->getCategories(1)); ?>
		<?php echo $form->error($model,'reference_category_id'); ?>
	</div>

    <?php
    $this->widget('application.modules.lang.components.langFieldWidget', array(
    		'model' => $model,
    		'field' => 'title',
            'type' => 'string'
    	));
    ?>

    <div class="clear"></div>

    <div class="rowold">
	    <?php echo $form->checkboxRow($model,'for_sale'); ?>
	    <?php echo $form->error($model,'for_sale'); ?>
    </div>

    <div class="rowold">
	    <?php echo $form->checkboxRow($model,'for_rent'); ?>
	    <?php echo $form->error($model,'for_rent'); ?>
    </div>

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