<div class="rowold">
    <?php echo $form->labelEx($user,'username'); ?>
    <?php echo $form->textField($user,'username',array('size'=>20,'maxlength'=>128)); ?>
    <?php echo $form->error($user,'username'); ?>
</div>

<div class="rowold">
    <?php echo $form->labelEx($user,'email'); ?>
    <?php echo $form->textField($user,'email',array('size'=>20,'maxlength'=>128)); ?>
    <?php echo $form->error($user,'email'); ?>
</div>

<div class="rowold">
    <?php echo $form->labelEx($user,'phone'); ?>
    <?php echo $form->textField($user,'phone',array('size'=>20,'maxlength'=>15)); ?>
    <?php echo $form->error($user,'phone'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($user, 'verifyCode');
    $this->widget('CCaptcha', array('captchaAction' => '/guestad/main/captcha', 'buttonOptions' => array('style' => 'display:block;'))); ?><br/>
    <?php echo $form->textField($user, 'verifyCode');?><br/>
    <?php echo $form->error($user, 'verifyCode');?>
</div>