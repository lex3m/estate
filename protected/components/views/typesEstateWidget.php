<?php
if(!issetModule('currency')){
    $currency = param('siteCurrency', '$');
} else {
    $currency = Currency::getCurrentCurrencyName();
}
?>
<div class="sk-widget">
<ul id="realty_type_favs" class="sk-faq-list2">
<span class="sk-widget-title" href="#">Типы недвижимости </span>
<ul id="realty_type_list" class="sk-faq-list2">
    <?php
    $z=0;
    foreach ($objTypes as $objType):
        if(isset($objType) && $objType){
            $priceAll = Apartment::getPriceMinMax($objType->id);
        }else{
            $priceAll = Apartment::getPriceMinMax(1, true);
        }

        $priceAll['price_min'] = isset($priceAll['price_min']) ? $priceAll['price_min'] : 0;
        $priceAll['price_max'] = isset($priceAll['price_max']) ? $priceAll['price_max'] : 1000;
        ?>
        <li>
            <div>
                <span>
                    <a id="type_estate_link<?php echo $objType->id; ?>" data-price_min="<?php echo $priceAll['price_min'] ?>" data-price_max="<?php echo $priceAll['price_max'] ?>" onclick="doSearchActionIndexWidget(<?php echo $objType->id; ?>);" href="#"><?php echo $objType->name; ?></a>
                </span>
                <?php if ($priceAll['price_min']): ?>
                <span class="sk-type-list-price">
от
                    <?php echo $priceAll['price_min']; echo '&nbsp'; echo $currency?>
                </span>
                <?php endif; ?>
            </div>
        </li>
    <?php
    $z++;
    endforeach; ?>
</ul>
<?php $priceForScript = Apartment::getPriceMinMax(1, true); ?>
<div class="sk-cent-but-wrap">
    <a href="#" onclick='
    $("#search_term_text").val("");
    $("#objType").val(0);
    $("#apType").val(0);
    $("#squareTo").val(0);
    $("#squareTo").val(0);
    $("#rooms").val(0);
    $("#country").val(0);
    $("#price_min").val(<?php echo $priceForScript['price_min']; ?>);
    $("#price_max").val(<?php echo $priceForScript['price_max']; ?>);
    $("#floor_min").val(0);
    $("#floor_max").val(100);
    $("#rooms").val(0);
    $("#country").val(0);
    $("#selectSublocation").find("option")
    .remove()
    .end()
    .append("<option value=\"0\"></option>").val(0);
    $("#sApId").val("");
    $("#search-form").submit(); return false;' class="sk-cent-but">все предложения</a>
</div>

<script type="text/javascript">
    $(document).ready(function () {
       /* if (window.favs.GetCount() > 0) {
            var li2 = "&lt;li&gt;&lt;a href='#' id='openFavorites' &gt;Мое избранное&lt;/a&gt;&lt;span id='openFavoritesCount'  class='sk-type-list-price'&gt;&lt;/span&gt;&lt;/li&gt;";
            $('#realty_type_favs').append(li2);
        }
        if (window.lasts.GetCount() > 0) {
            var li = "&lt;li&gt;&lt;a href='#' id='openLastVisited' &gt;Мои просмотры&lt;/a&gt;&lt;span id='openLastVisitedCount'  class='sk-type-list-price'&gt;&lt;/span&gt;&lt;/li&gt;";
            $('#realty_type_favs').append(li);
        }*/
    });
</script>

</div>
<?php
Yii::app()->clientScript->registerScript('doSearchActionIndexWidgetScript', '
					function doSearchActionIndexWidget(estateType) {
					    $(".search #objType").val(estateType);
					    var pr_min, pr_max;
					    pr_min = $("#type_estate_link"+estateType).data("price_min");
					    pr_max = $("#type_estate_link"+estateType).data("price_max");
					    $("input#price_min").val(pr_min);

					    $("input#price_max").val(pr_max);
						var term = $(".search-term input#search_term_text").val();
					    if (term.length < '.Yii::app()->controller->minLengthSearch.' || term == "'.tc("Search by description or address").'") {
							$(".search-term input#search_term_text").attr("disabled", "disabled");
						}
						$("#search-form").submit();
						return false;
					}
				', CClientScript::POS_HEAD, array(), true);
?>