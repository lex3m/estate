<?php
$this->pageTitle .= ' - '.tc('My data');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tc('My data'),
);

echo tt('Register as', 'usercpanel') . ': <strong>' . $model->getTypeName() . ', ' . HDate::formatDateTime($model->date_created, 'long') . '</strong>';
echo '<br/>';

//echo tt('Registered', 'usercpanel') . ': <strong>' . $model->date_created . '</strong>';
//echo '<br/>';
?>

<div class="form">
    <?php
    $model->scenario = 'usercpanel';
    $form=$this->beginWidget('CActiveForm', array(
        'enableAjaxValidation'=>false,
    )); ?>
    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

    <?php
    if(!$model->hasErrors('password')){
        echo $form->errorSummary($model);
    }
    ?>

    <div class="profile-ava">
        <?php
        echo $model->renderAva();

        $this->widget('ext.EAjaxUpload.EAjaxUpload',
            array(
                'id'=>'uploadFile',
                'label' => tc('Upload file'),
                'config'=>array(
                    'action'=>Yii::app()->createUrl('/users/main/uploadAva'),
                    'allowedExtensions'=>array("jpg","jpeg","gif", "png"),//array("jpg","jpeg","gif","exe","mov" and etc...
                    'sizeLimit'=>1*1024*1024,// maximum file size in bytes
                    'minSizeLimit'=>1024,// minimum file size in bytes
                    'onComplete'=>"js:function(id, fileName, responseJSON){ profile.showAva(responseJSON); }",
                    'multiple'=>false,
                    'showMessage'=>"js:function(message){ error(message); }",
                )
            ));

        echo CHtml::link(tc('Delete'), 'javascript:;', array('id' => 'delete_ava', 'style' => 'display: show;'));
        ?>
    </div>

    <script type="text/javascript">
        var ava = <?php echo $model->ava ? 1 : 0 ?>;

        var profile = {
            showAva: function(data){
                if(data.success == true){
                    $('#user-ava-<?php echo $model->id;?>').html(data.avaHtml);
                    $('#delete_ava').show();
                }
            }
        }

        $(function(){
            if(ava){
                $('#delete_ava').show();
            } else {
                $('#delete_ava').hide();
            }

            $('#delete_ava').live('click', function(){
                $.ajax({
                    url: '<?php echo Yii::app()->createUrl('/usercpanel/main/ajaxDelAva') ?>',
                    dataType: 'json',
                    type: 'get',
                    success: function(data){
                        if(data.status == 'ok'){
                            $('#user-ava-<?php echo $model->id;?>').html(data.avaHtml);
                            $('#delete_ava').hide();
                        }
                    }
                });
            });
        });
    </script>

    <div class="clear"></div>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

    <?php
    if($model->type == User::TYPE_AGENCY){
        echo '<div class="row">';
        echo $form->labelEx($model, 'agency_name');
        echo $form->textField($model, 'agency_name', array('size'=>20,'maxlength'=>128,'class' => 'width240'));
        echo $form->error($model, 'agency_name');
        echo '</div>';
    }
    ?>

    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>128, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'phone'); ?>
        <?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>15, 'class' => 'width240')); ?>
        <?php echo $form->error($model,'phone'); ?>
    </div>

    <?php

    if($model->type == User::TYPE_AGENT){
        echo '<div class="row">';
        $agency = HUser::getListAgency();

        echo $form->labelEx($model, 'agency_user_id');
        echo $form->dropDownList($model, 'agency_user_id', $agency, array('class' => 'width240'));
        if($model->agency_user_id){
            echo '&nbsp;' . $model->getAgentStatusName();
        }
        echo $form->error($model, 'agency_user_id');
        echo '</div><br>';
    }
    ?>

    <div class="row">
        <?php
        $this->widget('application.modules.lang.components.langFieldWidget', array(
            'model' => $model,
            'field' => 'additional_info',
            'type' => 'text'
        ));
        ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(tt('Change')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->