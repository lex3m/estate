<?php
$this->pageTitle=Yii::app()->name . ' - '.Yii::t('common','Login');
$this->breadcrumbs=array(
	Yii::t('common','Login')
);
?>

<h1><?php echo Yii::t('common', 'Login'); ?></h1>

<?php
if(demo()){
?>
	<p><a href="#" onclick="demoLogin(); return false;"><?php echo tc('Log in as user'); ?></a> <?php echo tc('or'); ?> <a href="#" onclick="adminLogin(); return false;"><?php echo tc('log in as administrator'); ?></a>.</p>
<?php
}
?>
<p><?php echo Yii::t('common', 'Already used our services? Please fill out the following form with your login credentials'); ?>:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>false,
	/*'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),*/
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row">
		<?php if(param('useUserads')): ?> <?php echo CHtml::link(tt("Join now"), 'register'); ?> | <?php endif; ?> <?php echo CHtml::link(tt("Forgot password?"), 'recover'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('common', 'Login')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php $this->widget('ext.eauth.EAuthWidget', array('action' => 'site/login')); ?>

<?php

if(demo()){
	Yii::app()->clientScript->registerScript('login-js', '
		function demoLogin(){
			login("demore@monoray.net", "demo");
		}

		function adminLogin(){
			login("adminre@monoray.net", "admin");
		}

		function login(username, password){
			$("#LoginForm_username").val(username);
			$("#LoginForm_password").val(password);
			$("#login-form").submit();
		}
	', CClientScript::POS_END);
}