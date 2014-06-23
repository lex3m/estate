<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <?php
    $this->widget('application.modules.lang.components.langFieldWidget', array(
    		'model' => $model,
    		'field' => 'title',
            'type' => 'string'
    	));

    if(issetModule('formeditor')){
        echo $form->dropDownListRow($model, 'type', ReferenceCategories::getTypeList());
    }
    ?>

	<div class="clear"></div>
	<div class="rowold">
		<?php echo $form->labelEx($model,'style'); ?>
		<?php echo $form->dropDownList($model,'style', $model->getStyles(), array('class' => 'width150')); ?>
		<?php echo $form->error($model,'style'); ?>
	</div>


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