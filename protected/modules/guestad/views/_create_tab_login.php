<div class="row">
    <?php echo $form->labelEx($model,'username'); ?>
    <?php echo $form->textField($model,'username', array('autocomplete' => 'off')); ?>
    <?php echo $form->error($model,'username'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'password'); ?>
    <?php echo $form->passwordField($model,'password', array('autocomplete' => 'off')); ?>
    <?php echo $form->error($model,'password'); ?>
</div>