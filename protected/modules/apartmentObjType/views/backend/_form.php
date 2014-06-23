<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <?php
    $this->widget('application.modules.lang.components.langFieldWidget', array(
    		'model' => $model,
    		'field' => 'name',
            'type' => 'string'
    	));
    ?>

	<?php
		if (!$model->isNewRecord && $model->icon_file):
	?>
        <div class="rowold padding-bottom10 padding-top10">
	        <div class="padding-bottom10"><?php echo tt('current_icon'); ?></div>
	        <div><?php echo CHtml::image(Yii::app()->getBaseUrl().'/'.$model->iconsMapPath.'/'.$model->icon_file); ?></div>
            <div><?php echo CHtml::link(tc('Delete'), $this->createUrl('deleteIcon', array('id' => $model->id))); ?></div>
        </div>
	<?php endif; ?>

    <div class="rowold">
		<?php echo $form->labelEx($model,'icon_file'); ?>
        <div class="padding-bottom10">
				<span class="label label-info">
					<?php echo Yii::t('module_apartmentObjType', 'Supported file: {supportExt}.', array('{supportExt}' => $model->supportExt)).'';?>
				</span>
        </div>
		<?php echo $form->fileField($model, 'icon_file'); ?>
		<?php echo $form->error($model,'icon_file'); ?>
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