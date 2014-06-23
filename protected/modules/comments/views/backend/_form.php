<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
));

if(!Yii::app()->user->getState('isAdmin') && !Yii::app()->user->isGuest){
	$model->name = Yii::app()->user->username;
	$model->email = Yii::app()->user->email;
}

?>
	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

		<?php
			if($model->owner_id && $model->user){
				echo Yii::t('module_comments', 'Name').': '.$model->getUser();
			} else {
				echo $form->labelEx($model,'user_name');
				echo $form->textField($model,'user_name',array('size'=>60,'maxlength'=>128, 'class' => 'width500'));
				echo $form->error($model,'user_name');

				echo $form->labelEx($model,'user_email');
				echo $form->textField($model,'user_email',array('size'=>60,'maxlength'=>128, 'class' => 'width500'));
				echo $form->error($model,'user_email');
			}
		?>

		<?php echo $form->labelEx($model,'body'); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>3, 'cols'=>50, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'body'); ?>

		<?php if($model->rating > -1){ ?>
		<?php echo $form->labelEx($model,'rating'); ?>
		<?php $this->widget('CStarRating',array('name'=>'Comment[rating]', 'value'=>$model->rating, 'resetText' => tt('Remove the rate', 'comments'))); ?>
		<?php echo $form->error($model,'rating'); ?>
		<?php } ?>

	<div class="clear">&nbsp;</div>

	<?php $this->widget('bootstrap.widgets.TbButton',
		array('buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'ok white',
			'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
		)
	);?>
	<?php $this->endWidget(); ?>

</div><!-- form -->