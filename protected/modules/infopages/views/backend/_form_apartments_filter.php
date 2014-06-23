<div id="apartments_filter" style="display: none;" class="well">
    <h4><?php echo tt('Filter for listings\' list') ?></h4>
    <?php
    if((issetModule('location') && param('useLocation', 1))){ ?>
        <div class="">
            <div class=""><?php echo tc('Country') ?>:</div>
            <?php
            echo CHtml::dropDownList(
                'filter[country_id]',
                $this->getFilterValue('country_id'),
                Country::getCountriesArray(2),
                array('class' => 'searchField', 'id' => 'country',
                    'ajax' => array(
                        'type'=>'GET',
                        'url'=>$this->createUrl('/location/main/getRegions'),
                        'data'=>'js:"country="+$("#country").val()+"&type=2"',
                        'success'=>'function(result){
							$("#region").html(result);
							$("#region").change();
						}'
                    )
                )
            );

            ?>
        </div>

        <div class="">
            <div class=""><?php echo tc('Region') ?>:</div>
            <?php
            echo CHtml::dropDownList(
                'filter[region_id]',
                $this->getFilterValue('region_id'),
                Region::getRegionsArray($this->getFilterValue('country_id'), 2),
                array('class' => 'searchField', 'id' => 'region',
                    'ajax' => array(
                        'type'=>'GET',
                        'url'=>$this->createUrl('/location/main/getCities'),
                        'data'=>'js:"region="+$("#region").val()+"&type=0"',
                        'success'=>'function(result){
							$("#ap_city").html(result);
						}'
                    )
                )
            );

            ?>
        </div>
    <?php

	$cities = City::getCitiesArray($this->getFilterValue('region_id'));
    }

    $objTypes = CArray::merge(array(0 => ''), ApartmentObjType::getList());
    $typeList = CArray::merge(array(0 => ''), Apartment::getTypesArray());
    ?>

    <div class="">
        <div class=""><?php echo Yii::t('common', 'City') ?>:</div>
        <?php
        $cities = (isset($cities) && count($cities)) ? $cities : CArray::merge(array(0 => tc('select city')), ApartmentCity::getAllCity());

        echo CHtml::dropDownList(
            'filter[city_id]',
            $this->getFilterValue('city_id'),
            $cities,
            array('class' => ' searchField', 'id' => 'ap_city') //, 'multiple' => 'multiple'
        );
        ?>
    </div>

    <div class="rowold">
        <div class=""><?php echo tc('Type') ?>:</div>
        <?php echo CHtml::dropDownList('filter[type]', $this->getFilterValue('type'), $typeList); ?>
    </div>

    <div class="rowold">
        <div class=""><?php echo tc('Property type') ?>:</div>
        <?php echo CHtml::dropDownList('filter[obj_type_id]', $this->getFilterValue('obj_type_id'), $objTypes); ?>
    </div>
</div>