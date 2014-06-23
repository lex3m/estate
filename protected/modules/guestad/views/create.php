<?php
$this->pageTitle .= ' - '.tc("List your property");
$this->breadcrumbs=array(
	tt('Add ad', 'common')
);
?>

<?php
Yii::app()->clientScript->registerCssFile( Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css' );

Yii::app()->clientScript->registerScript('redirectType', "
    $(document).ready(function() {
        var BASE_URL = ".CJavaScript::encode(Yii::app()->baseUrl).";

        $('#obj_type, #ap_type').live('change', function() {
            $('#update_overlay').show();
            $('#is_update').val(1);
            $('#Apartment-form').submit(); return false;
        });
    });
	",
    CClientScript::POS_HEAD, array(), true);

Yii::app()->clientScript->registerScript('show-special', '
		// price poa
		if($("#Apartment_is_price_poa").is(":checked")){
			$("#price_fields").hide();
		}
		$("#Apartment_is_price_poa").bind("change", function(){
			if($(this).is(":checked")){
				$("#price_fields").hide();
			} else {
				$("#price_fields").show();
			}
		});
	', CClientScript::POS_READY);
?>

<div class="form">
    <div id="update_overlay"><p><?php echo tc('Loading content...');?></p></div>

    <?php
    $ajaxValidation = false;
    if(!$model->isNewRecord){
        $htmlOptions = array('enctype' => 'multipart/form-data');
    }else{
        $htmlOptions = array();
    }

    /** @var $form BootActiveForm */
    $form = $this->beginWidget('CustomForm', array(
        'id'=>'Apartment-form',
        'enableAjaxValidation'=>$ajaxValidation,
        'htmlOptions'=> $htmlOptions,
    ));

    ?>

    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

    <?php echo $form->errorSummary(array($model, $user, $login)); ?>

    <?php
    $this->renderPartial('//../modules/apartments/views/backend/__form_general', array(
        'model' => $model,
        'form' => $form,
    ));

    $this->widget('CTabView', array(
        'tabs' => array(
            'tab_register' => array(
                'title' => tc('Join now'),
                'content' => $this->renderPartial('_create_tab_register', array('user' => $user, 'form' => $form), true),
            ),
            'tab_login' => array(
                'title' => tc('Login'),
                'content' => $this->renderPartial('_create_tab_login', array('model' => $login, 'form' => $form), true),
            ),
        ),
        'activeTab' => $activeTab,
    ));
    ?>

    <?php
    echo '<div class="row buttons save">';
    echo CHtml::button(tc('Save'), array(
        'onclick' => "$('#Apartment-form').submit(); return false;", 'class' => 'big_button',
    ));
    echo '</div>';
    ?>

    <?php $this->endWidget(); ?><!-- form -->
</div>

