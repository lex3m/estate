<div class="form">
<?php
	$form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	));
	$model->password = '';
	$model->password_repeat = '';
	?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="rowold">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', User::getTypeList(), array('class'=>'span2')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

    <div class="rowold" id="row_agency_name">
        <?php echo $form->labelEx($model,'agency_name'); ?>
        <?php echo $form->textField($model,'agency_name',array('size'=>20,'maxlength'=>128,'class'=>'span2')); ?>
        <?php echo $form->error($model,'agency_name'); ?>
    </div>

    <?php
    echo '<div class="rowold"  id="row_agency_user_id">';
    $agency = HUser::getListAgency();

    echo $form->labelEx($model, 'agency_user_id');
    echo $form->dropDownList($model, 'agency_user_id', $agency, array('class' => 'span2'));
    echo $form->error($model, 'agency_user_id');
    echo '</div>';
    ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>15,'class' => 'span2')); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<?php if(issetModule('paidservices')){ ?>
    <div class="rowold">
		<?php echo $form->labelEx($model,'balance'); ?>
		<?php echo $form->textField($model,'balance',array('size'=>20,'maxlength'=>15,'class' => 'span2')); ?>
		<?php echo $form->error($model,'balance'); ?>
    </div>
	<?php } ?>

	<div class="clear">&nbsp;</div>
	<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
				'model' => $model,
				'field' => 'additional_info',
				'type' => 'text'
			));
		?>
	<div class="clear">&nbsp;</div>

	<?php if (!$model->isAdmin) : ?>
		<?php if(!$model->isNewRecord) : ?>
			<div class="padding-bottom10">
				<span class="label label-info">
					<?php echo tt('admin_change_pass_user_help');?>
				</span>
			</div>
		<?php endif; ?>

		<div class="rowold">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>

		<div class="rowold">
			<?php echo $form->labelEx($model,'password_repeat'); ?>
			<?php echo $form->passwordField($model,'password_repeat',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
			<?php echo $form->error($model,'password_repeat'); ?>
		</div>
	<?php endif; ?>

    <div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
		array('buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'ok white',
			'label'=> $model->isNewRecord ? tc('Create') : tc('Save'),
		)); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

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