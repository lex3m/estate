<?php
/**
 * @var $apartment Apartment
 */
?>
<?php
$this->pageTitle .= ' - '.tt("Message for the listing's owner №", 'notifier'). ' '. $apartment->id;

$this->breadcrumbs=array(
	Yii::t('common', 'Apartment search') => array('/quicksearch/main/mainsearch'),
	truncateText(CHtml::encode($apartment->getStrByLang('title')), 8) => $apartment->getUrl(),
	tt("Message for the listing's owner №", 'notifier'). ' '. $apartment->id,
);
?>

<h2><?php echo tt("Message for the listing's owner №", 'notifier'). ' '. CHtml::link($apartment->id, $apartment->getUrl()); ?></h2>

<?php
	if(!Yii::app()->user->isGuest){
	    if(!$model->senderName)
	        $model->senderName = Yii::app()->user->username;
	    if(!$model->senderPhone)
	        $model->senderPhone = Yii::app()->user->phone;
	    if(!$model->senderEmail)
	        $model->senderEmail = Yii::app()->user->email;
	}
?>

<div class="form min-fancy-width max-fancy-width">
    <?php $form=$this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->controller->createUrl('/apartments/main/sendEmail', array('id' => $apartment->id)),
    'id'=>'contact-form',
    'enableClientValidation'=>false,
));
    ?>

    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'senderName'); ?>
        <?php echo $form->textField($model,'senderName', array('size' => 60,'maxlength' => 128, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'senderName'); ?>
    </div>

	<div class="row">
        <?php echo $form->labelEx($model,'senderPhone'); ?>
        <?php echo $form->textField($model,'senderPhone', array('size' => 60,'maxlength' => 128, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'senderPhone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'senderEmail'); ?>
        <?php echo $form->textField($model,'senderEmail', array('size' => 60,'maxlength' => 128, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'senderEmail'); ?>
    </div>

    <div class="row">
    <?php echo $form->labelEx($model,'body'); ?>
        <?php echo $form->textArea($model,'body',array('rows' => 3, 'cols' => 50, 'class' => 'contact-textarea')); ?>
        <?php echo $form->error($model,'body'); ?>
    </div>

	<?php
	    if (Yii::app()->user->isGuest){
	        ?>
	        <div class="row">
	            <?php echo $form->labelEx($model, 'verifyCode');?>
	            <?php
		            $this->widget('CCaptcha',
		                array('captchaAction' => '/apartments/main/captcha', 'buttonOptions' => array('style' => 'display:block;'),
							'imageOptions'=>array('id'=>'send_email_captcha'))
		            );
		        ?>
	            <br />
	            <?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
	            <?php echo $form->error($model, 'verifyCode');?>
	        </div>
	        <?php
	    }
	?>

    <div class="row buttons">
        <?php echo CHtml::submitButton(tt('send_request', 'apartments')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>