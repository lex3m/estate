<?php
$this->pageTitle=Yii::app()->name . ' - '.tc('Join now');
$this->breadcrumbs=array(
	tc('Join now'),
);
?>

<div class="form">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->controller->createUrl('/site/register'),
		'id'=>'user-register-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<h2><?php echo Yii::t('common', 'Join now'); ?></h2>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', User::getTypeList(), array('class'=>'width200')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

    <div class="row" id="row_agency_name">
        <?php echo $form->labelEx($model,'agency_name'); ?>
        <?php echo $form->textField($model,'agency_name',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
        <?php echo $form->error($model,'agency_name'); ?>
    </div>

    <?php
    echo '<div class="row"  id="row_agency_user_id">';
    $agency = HUser::getListAgency();

    echo $form->labelEx($model, 'agency_user_id');
    echo $form->dropDownList($model, 'agency_user_id', $agency, array('class' => 'width200'));
    echo $form->error($model, 'agency_user_id');
    echo '</div>';
    ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>15,'class'=>'width200')); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'verifyCode');
		$this->widget('CCaptcha', array('captchaAction' => '/site/captcha', 'buttonOptions' => array('style' => 'display:block;'))); ?><br/>
		<?php echo $form->textField($model, 'verifyCode',array('class'=>'width200'));?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
	</div>

	<div class="row submit">
		<?php echo CHtml::submitButton(Yii::t('common', 'Registration')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function(){
        regCheckUserType();

        $('#User_type').change(function(){
            regCheckUserType();
        });
    });

    function regCheckUserType(){
        var type = $('#User_type').val();
        if(type == <?php echo CJavaScript::encode(User::TYPE_AGENCY);?>){
            $('#row_agency_name').show();
        } else {
            $('#row_agency_name').hide();
        }

        if(type == <?php echo CJavaScript::encode(User::TYPE_AGENT);?>){
            $('#row_agency_user_id').show();
        } else {
            $('#row_agency_user_id').hide();
        }
    }
</script>