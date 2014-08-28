<div class="tab-pane active" id="tab-main">
<div class="rowold">
    <?php echo $form->labelEx($model, 'type'); ?>
    <?php echo $form->dropDownList($model, 'type', Apartment::getTypesArray(), array('class' => 'width240', 'id' => 'ap_type')); ?>
    <?php echo $form->error($model, 'type'); ?>
</div>

<div class="rowold">
    <?php echo $form->labelEx($model, 'obj_type_id'); ?>
    <?php echo $form->dropDownList($model, 'obj_type_id', Apartment::getObjTypesArray(), array('class' => 'width240', 'id' => 'obj_type')); ?>
    <?php echo $form->error($model, 'obj_type_id'); ?>
</div>

<?php $locations = Location::getLocationArray();?>
    <div class="rowold">
        <?php echo $form->labelEx($model,'location_id'); ?>
        <?php echo $form->dropDownList($model,'location_id',$locations,
            array('id'=>'ap_location',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/apartments/main/getSublocations'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"ap_location="+$("#ap_location").val()',
                    'success'=>'function(result){
								$("#ap_sublocation").html(result);
								$("#ap_sublocation").change();
							}'
                    //leave out the data key to pass all form values through
                )
            )
        ); ?>
        <?php echo $form->error($model,'location_id'); ?>
    </div>



    <div class="rowold"  id="sublocation_row" <?php
    $valueSubLocation = Apartment::getSublocationsOfLocation($model->location_id);
    if ($model->sublocation_id != 0){
        Yii::app()->clientScript->registerScript('selectSublocation',"
            $('select[name=\'options\']').find('option[value=\'".$model->sublocation_id."\']').attr('selected',true);
        ");
    }
    ?>>

        <?php echo $form->labelEx($model, 'sublocation_id'); ?>
        <?php echo $form->dropDownList($model, 'sublocation_id', $valueSubLocation, array(
            'id'=>'ap_sublocation',
            'class' => 'width240',
            'ajax' => array(
                'type'=>'GET', //request type
                'url'=>$this->createUrl('/apartments/main/getRegions'), //url to call.
                'data'=>'js:"sublocationID="+$("#ap_sublocation").val()',
                'success'=>"function(result) {
                            if (result!='<option value=\"0\">Выберите регион</option>')
                            {
                                $('#ap_region').parent().css('display', 'block');
                                $('#ap_region').html(result);
                                $('#ap_region').change();
						    }
						    else
						    {
						        $('#ap_region').parent().css('display', 'none');
						    }

						}"
            )
        )); ?>
        <?php echo $form->error($model, 'sublocation_id'); ?>
    </div>


<div class="rowold" style="display:none" id="region_row" >
    <?php echo $form->labelEx($model, 'region_id'); ?>
    <?php echo $form->dropDownList($model, 'region_id', array(), array('id'=>'ap_region', 'class' => 'width240')); ?>
    <?php echo $form->error($model, 'region_id'); ?>
</div>



<?php
if ($model->canShowInForm('address')) {
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'address',
        'type' => 'string'
    ));
}
?>

<div class="rowold no-mrg">
    <?php
    echo $form->label($model, 'price', array('required' => true));
    ?>



    <?php echo $form->checkbox($model, 'is_price_poa'); ?>
    <?php echo $form->labelEx($model, 'is_price_poa', array('class' => 'noblock')); ?>
    <?php echo $form->error($model, 'is_price_poa'); ?>






    <div id="price_fields">
        <?php
        echo CHtml::hiddenField('is_update', 0);

//        if (issetModule('currency')) {
//            echo '<div class="padding-bottom10"><small>' . tt('Price will be saved (converted) in the default currency on the site', 'apartments') . ' - ' . Currency::getDefaultCurrencyModel()->name . '</small></div>';
//        }

        if ($model->isPriceFromTo()) {
            echo tc('price_from') . ' ' . $form->textField($model, 'price', array('class' => 'width100 noblock'));
            echo ' ' .tc('price_to') . ' ' . $form->textField($model, 'price_to', array('class' => 'width100'));
        } else {
            echo $form->textField($model, 'price', array('class' => 'width100'));
        }

        if(issetModule('currency')){
            // Даем вводить ценую только в дефолтной валюте
            echo '&nbsp;' . Currency::getDefaultCurrencyName();
            $model->in_currency = Currency::getDefaultCurrencyModel()->char_code;
            echo $form->hiddenField($model, 'in_currency');
            // $form->dropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'))
        } else {
            echo '&nbsp;'.param('siteCurrency', '$');
        }

        if ($model->price_old!=0){
            ?>
            <label class="required" for="Apartment_price_old"><?php echo tt('Old price', 'apartments'); ?>:</label>
            <span id="old_price_value">
                <span id="old_price_number"><?php
                    echo $model->price_old;
                    ?>
                </span>
         <?php
         if(issetModule('currency')){
            // Даем вводить ценую только в дефолтной валюте
            echo '&nbsp;' . Currency::getDefaultCurrencyName();
            $model->in_currency = Currency::getDefaultCurrencyModel()->char_code;
            echo $form->hiddenField($model, 'in_currency');
            // $form->dropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'))
        } else {
            echo '&nbsp;'.param('siteCurrency', '$');
        }
         }
         else { ?>
         <label class="required" for="Apartment_price_old">Редактировать старую цену</label>
         <?php }
        ?>
        </span>
        <a id="edit_old_price" href="#" rel="tooltip" class="update" data-original-title="Редактировать"><i class="icon-pencil"></i></a>
        <input style="display:none" type="text" value="<?php echo $model->price_old; ?>" id="Apartment_price_old_new" name="Apartment[price_old_new]" class="width100">
        <br><br>
        <?php

        if($model->type == Apartment::TYPE_RENT){
            $priceArray = Apartment::getPriceArray($model->type);
            if(!in_array($model->price_type, array_keys($priceArray))){
                $model->price_type = Apartment::PRICE_PER_MONTH;
            }
            echo '&nbsp;'.$form->dropDownList($model, 'price_type', Apartment::getPriceArray($model->type), array('class' => 'width150'));
        }
        ?>
    </div>

    <?php echo $form->error($model, 'price'); ?>
</div>
<div class="clear"></div>
<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
    'model' => $model,
    'field' => 'title',
    'type' => 'string'
));

echo '<br/>';

if ($model->canShowInForm('description')) {
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'description',
        'type' => 'text'
    ));
    echo '<div class="clear">&nbsp;</div>';
}
?>

<?php if($model->canShowInForm('square')){ ?>
    <div class="rowold">
        <?php echo $form->labelEx($model, 'square'); ?>
        <?php echo Apartment::getTip('square');?>
        <?php echo $form->textField($model, 'square', array('size' => 5, 'class' => 'width70')).' '.tc('site_square'); ?>
        <?php echo $form->error($model, 'square'); ?>
    </div>
<?php } ?>

<?php if($model->canShowInForm('land_square')){ ?>
    <div class="rowold">
        <?php echo $form->labelEx($model, 'land_square'); ?>
        <?php echo Apartment::getTip('land_square');?>
        <?php echo $form->textField($model, 'land_square', array('size' => 5, 'class' => 'width70')).' '.tc('site_land_square'); ?>
        <?php echo $form->error($model, 'land_square'); ?>
    </div>
<?php } ?>

<?php
if ($model->type == Apartment::TYPE_CHANGE) {
    echo '<div class="clear">&nbsp;</div>';
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'exchange_to',
        'type' => 'text'
    ));
}

if(issetModule('formeditor')){
    Yii::import('application.modules.formeditor.models.HFormEditor');
    $rows = HFormEditor::getGeneralFields();
    HFormEditor::renderFormRows($rows, $model);
}

$canSet = $model->canSetPeriodActivity() ? 1 : 0;

echo '<div class="rowold" id="set_period" ' . ( !$canSet ? 'style="display: none;"' : '' ) . '>';
echo $form->labelEx($model, 'period_activity');
echo $form->dropDownList($model, 'period_activity', Apartment::getPeriodActivityList());
echo CHtml::hiddenField('set_period_activity', $canSet);
echo $form->error($model, 'period_activity');
echo '</div>';

if(!$canSet) {
    echo '<div id="date_end_activity"><b>'.Yii::t('common', 'The listing will be active till {DATE}', array('{DATE}' => $model->getDateEndActivityLongFormat())).'</b>';
    echo '&nbsp;' . CHtml::link(tc('Change'), 'javascript:;', array(
            'onclick' => '$("#date_end_activity").hide(); $("#set_period_activity").val(1); $("#set_period").show();',
        ));
    echo '</div>';
}

?>

</div>
<?php
Yii::app()->clientScript->registerScript('old price script',"
    $('#edit_old_price').on('click',function(){
    $(this).hide();
    $('#old_price_value').hide()
    $('#Apartment_price_old_new').show();
    return false;
    });
");