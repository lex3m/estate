
<?php  ?>
<div class="form">
<?php
	$this->adminTitle = tt("Change admin password");
	$this->pageTitle=Yii::app()->name . ' - ' . tt("Change admin password");
	$this->menu = array(
		array(),
	);

	$model->scenario = 'changeAdminPass';
	$model->password = '';
	$model->password_repeat = '';

	$form=$this->beginWidget('CustomForm', array(
		'enableAjaxValidation'=>false,
	));
	?>
	<div class="rowold">&nbsp;</div>
	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

    <?php echo $form->errorSummary($model); ?>

    <div class="rowold">
        <?php echo $form->labelEx($model,'old_password'); ?>
        <?php echo $form->passwordField($model,'old_password',array('size'=>20,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'old_password'); ?>
    </div>

    <div class="rowold">
        <?php echo $form->labelEx($model,'password'); ?>
        <?php echo $form->passwordField($model,'password',array('size'=>20,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'password'); ?>
    </div>

    <div class="rowold">
        <?php echo $form->labelEx($model,'password_repeat'); ?>
        <?php echo $form->passwordField($model,'password_repeat',array('size'=>20,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'password_repeat'); ?>
    </div>

    <div class="rowold buttons">
           <?php $this->widget('bootstrap.widgets.TbButton',
                       array('buttonType'=>'submit',
                           'type'=>'primary',
                           'icon'=>'ok white',
                           'label'=> tc('Change'),
                       )); ?>
    </div>

<?php $this->endWidget(); ?>

</div>