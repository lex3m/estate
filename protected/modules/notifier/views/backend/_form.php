<div class="form">

    <?php
    $rules = Notifier::getRules();

    $form=$this->beginWidget('CustomForm', array(
        'id'=>'News-form',
        'enableClientValidation'=>false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <p class="note">
        <?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?>
    </p>

    <?php echo $form->errorSummary($model); ?>

    <?php
    if(in_array($model->status, array(NotifierModel::STATUS_SEND_USER, NotifierModel::STATUS_SEND_ALL))){
        echo CHtml::tag('h3', array(), tt('Mail template for users'));
        //echo $form->dropDownListRow($model, 'status', NotifierModel::getStatusList());

        $this->widget('application.modules.lang.components.langFieldWidget', array(
            'model' => $model,
            'field' => 'subject',
            'type' => 'string'
        ));

        $this->widget('application.modules.lang.components.langFieldWidget', array(
            'model' => $model,
            'field' => 'body',
            'type' => 'text-editor',
            'note' => $model->getRulesFieldsString($rules, 'user'),
        ));

        echo '<hr>';
    }

    if($model->status != NotifierModel::STATUS_SEND_USER){
        echo CHtml::tag('h3', array(), tt('Mail template for admin'));

        $this->widget('application.modules.lang.components.langFieldWidget', array(
            'model' => $model,
            'field' => 'subject_admin',
            'type' => 'string'
        ));

        $this->widget('application.modules.lang.components.langFieldWidget', array(
            'model' => $model,
            'field' => 'body_admin',
            'type' => 'text-editor',
            'note' => $model->getRulesFieldsString($rules, 'user'),
        ));
    }
    ?>

    <div class="clear"></div>
    <br />

    <div class="rowold buttons">
        <?php $this->widget('bootstrap.widgets.TbButton',
            array('buttonType'=>'submit',
                'type'=>'primary',
                'icon'=>'ok white',
                'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
            )); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

