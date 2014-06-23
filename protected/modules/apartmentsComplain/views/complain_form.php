<?php
$this->pageTitle .= ' - '. tt('do_complain', 'apartmentsComplain');
$this->breadcrumbs=array(
	Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
	truncateText(CHtml::encode($modelApartment->getStrByLang('title')), 8) => $modelApartment->getUrl(),
	tt('do_complain', 'apartmentsComplain'),
);
?>

<?php
if (!Yii::app()->user->isGuest) {
	if (!$model->name) {
		$model->name = Yii::app()->user->username;
	}
	if (!$model->email) {
		$model->email = Yii::app()->user->email;
	}
}
?>

<div class="form min-fancy-width max-fancy-width">
	<?php $form = $this->beginWidget('CustomForm', array(
		'action' => Yii::app()->controller->createUrl('/apartmentsComplain/main/complain', array('id' => $apId)),
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	));
	?>

    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
		<?php echo $form->labelEx($model, 'complain_id'); ?>
		<?php echo $form->dropDownList($model,'complain_id', ApartmentsComplainReason::getAllReasons(), array('class' => 'width300')); ?>
		<?php echo $form->error($model, 'complain_id'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model, 'body'); ?>
		<?php echo $form->textArea($model, 'body', array('rows' => 3, 'cols' => 50, 'class' => 'width240')); ?>
		<?php echo $form->error($model, 'body'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 128, 'class' => 'width240')); ?>
		<?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model, 'email'); ?>
		<?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 128, 'class' => 'width240')); ?>
		<?php echo $form->error($model, 'email'); ?>
    </div>

	<?php if (Yii::app()->user->isGuest) : ?>
    <div class="row">
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php
		$cAction = '/apartmentsComplain/main/captcha';
		$this->widget('CCaptcha',
			array('captchaAction' => $cAction, 'buttonOptions' => array('style' => 'display:block;'), 'imageOptions'=>array('id'=>'complain_captcha'))
		);?>
        <br/>
		<?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
    </div>
	<?php endif; ?>

    <div class="row buttons">
		<?php echo CHtml::submitButton(tt('Send complain', 'apartmentsComplain')); ?>
    </div>

	<?php $this->endWidget(); ?>

</div>