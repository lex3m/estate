<div class="form min-fancy-width">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->controller->createUrl('/comments/main/writeComment'),
		'enableAjaxValidation'=>false,
	)); ?>
	<h2><?php echo Yii::t('module_comments','Leave a Comment'); ?></h2>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo $form->errorSummary($model); ?>

	<?php if(Yii::app()->user->isGuest){ ?>
	<div class="row">
		<?php echo $form->labelEx($model,'user_name'); ?>
		<?php echo $form->textField($model, 'user_name', array('class' => 'width200')); ?>
		<?php echo $form->error($model,'user_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_email'); ?>
		<?php echo $form->textField($model, 'user_email', array('class' => 'width200')); ?>
		<?php echo $form->error($model,'user_email'); ?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>3, 'cols'=>50, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'body'); ?>
	</div>

	<?php if($model->enableRating){ ?>
	<div class="row rating">
		<?php echo $form->labelEx($model,'rating'); ?>
		<?php $this->widget('CStarRating',array('name'=>'CommentForm[rating]', 'value'=>$model->rating, 'resetText' => tt('Remove the rate', 'comments'))); ?>
		<?php echo $form->error($model,'rating'); ?>
		<div class="clear"></div>
	</div>
	<?php } ?>

	<?php if(Yii::app()->user->isGuest || param('useCaptchaCommentsForRegistered', 1)){ ?>
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php $this->widget('CCaptcha', array('captchaAction' => '/comments/main/captcha', 'buttonOptions' => array('style' => 'display:block;') ));?><br/>
		<?php echo $form->textField($model, 'verifyCode');?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
	<?php } ?>

	<div class="row buttons">
		<?php
			echo $form->hiddenField($model, 'url');
			echo $form->hiddenField($model, 'rel');
			echo $form->hiddenField($model, 'modelName');
			echo $form->hiddenField($model, 'modelId');

			echo CHtml::submitButton(Yii::t('common', 'Add'));
		?>
	</div>
	<?php $this->endWidget(); ?>
</div>