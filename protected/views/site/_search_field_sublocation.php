<?php
$locationDropDownArray = CHtml::listData(Location::getLocationArray(),'id','name');
array_unshift($locationDropDownArray,  Yii::t('common', 'Select location'));
?>
<div class="<?php echo $divClass; ?>">
        <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tt("Choose location",'apartments'); ?>:</div></span>
<?php $locations = Location::getLocationArray();?>
<?php
echo CHtml::dropDownList(
    'location_id',
    'Выберите город',
    $locations,
    array('class' => $fieldClass . ' searchField', 'id' => 'country',
        'ajax' => array(
            'type'=>'GET', //request type
            'url'=>$this->createUrl('/apartments/main/getSublocations'), //url to call.
            'data'=>'js:"ap_location="+$("#country").val()',
            'success'=>"function(result) {

                          $('#selectSublocation').html(result);
						  $('#selectSublocation').change();
						}"
        )
    )
);

?>
</div>
<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tt("Choose sublocation",'apartments'); ?>:</div></span>

    <?php

    echo CHtml::dropDownList(
        'sublocation_id',
        '',
        array(),
        array(
            'id'=>'selectSublocation',
            'class' => 'width285 height17 searchField',
        'ajax' => array(
        'type'=>'GET', //request type
        'url'=>$this->createUrl('/apartments/main/getRegions'), //url to call.
        'data'=>'js:"sublocationID="+$("#selectSublocation").val()',
        'success'=>"function(result) {
                            if (result!='<option value=\"0\">Выберите регион</option>')
                            {
                                $('#selectRegion').parent().css('display', 'block');
                                $('#selectRegion').html(result);
                                $('#selectRegion').change();
						    }
						    else
						    {
						        $('#selectRegion').parent().css('display', 'none');
						    }

						}"
    )) //$fieldClass.
    );

    SearchForm::setJsParam('cityField', array('minWidth' => $minWidth)); //

    ?>
</div>
<?php

