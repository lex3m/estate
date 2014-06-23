<div class="<?php echo $divClass; ?>">
    <?php

    if(!issetModule('currency')){
        $currency = param('siteCurrency', '$');
    } else {
        $currency = Currency::getCurrentCurrencyName();
    }

    if (issetModule('selecttoslider') && param('usePriceSlider') == 1) {


    ?>
    <span class="search"><div class="<?php echo $textClass; ?>" id="currency-title"><?php echo tc('Price range'); ?>:</div> </span>
		<span class="search">
			<?php
                if(isset($this->objType) && $this->objType){
                    $priceAll = Apartment::getPriceMinMax($this->objType);
                }else{
                    $priceAll = Apartment::getPriceMinMax(1, true);
                }

                $priceAll['price_min'] = isset($priceAll['price_min']) ? $priceAll['price_min'] : 0;
                $priceAll['price_max'] = isset($priceAll['price_max']) ? $priceAll['price_max'] : 1000;

                if(issetModule('currency')){
                    $priceAll['price_min'] = floor(Currency::convertFromDefault($priceAll['price_min']));
                    $priceAll['price_max'] = ceil(Currency::convertFromDefault($priceAll['price_max']));
                }

                $diffPrice = $priceAll['price_max'] - $priceAll['price_min'];
                $step = SearchForm::getSliderStep($diffPrice);

                $priceMinSel = (isset($this->priceSlider) && isset($this->priceSlider["min"]) && $this->priceSlider["min"] >= $priceAll["price_min"] && $this->priceSlider["min"] <= $priceAll["price_max"])
                    ? $this->priceSlider["min"] : $priceAll["price_min"];
                $priceMaxSel = (isset($this->priceSlider) && isset($this->priceSlider["max"]) && $this->priceSlider["max"] <= $priceAll["price_max"] && $this->priceSlider["max"] >= $priceAll["price_min"])
                    ? $this->priceSlider["max"] : $priceAll["price_max"];

                //$priceMinSel = Apartment::priceFormat($priceMin);
                //$priceMaxSel = Apartment::priceFormat($priceMax);

                SearchForm::renderSliderRange(array(
                    'field' => 'price',
                    'min' => $priceAll['price_min'],
                    'max' => $priceAll['price_max'],
                    'min_sel' => $priceMinSel,
                    'max_sel' => $priceMaxSel,
                    'step' => $step,
                    'measure_unit' => $currency,
                    'class' => 'price-search-select',
                ));

            echo '</span>';
            } else {
                ?>
                <span class="search"><div class="<?php echo $textClass; ?>" id="currency-title"><?php echo tc('Price up to'); ?>:</div> </span>
                <span class="search">
				<input type="text" id="priceTo" name="price" class="width70 search-input-new" value="<?php echo isset($this->price) && $this->price ? CHtml::encode($this->price) : ""; ?>"/>&nbsp;
				<span id="price-currency"><?php echo $currency; ?></span>
			</span>
                <?php

                Yii::app()->clientScript->registerScript('priceTo', '
		focusSubmit($("input#priceTo"));
	', CClientScript::POS_READY);
            }
            ?>
</div>