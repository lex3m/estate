<?php
$isInner = isset($isInner) ? $isInner : 0;
$compact = param("useCompactInnerSearchForm", true);
$loc = (issetModule('location') && param('useLocation', 1)) ? 1 : 0;
?>

    var sliderRangeFields = <?php echo CJavaScript::encode(SearchForm::getSliderRangeFields());?>;
    var cityField = <?php echo CJavaScript::encode(SearchForm::getCityField());?>;
    var loc = <?php echo CJavaScript::encode($loc);?>;
    var countFiled = <?php echo CJavaScript::encode(SearchForm::getCountFiled() + ($loc ? 2 : 0));?>;
    var isInner = <?php echo CJavaScript::encode($isInner);?>;
    var heightField = 38;
    var advancedIsOpen = 0;
    var compact = <?php echo $compact ? 1 : 0;?>;
    var minHeight = isInner ? 80 : 260;
    var searchCache = [];
    var objType = <?php echo isset($this->objType) ? $this->objType : SearchFormModel::OBJ_TYPE_ID_DEFAULT;?>;
    var useSearchCache = false;

    var search = {
        init: function(){

            if(sliderRangeFields){
                $.each(sliderRangeFields, function() {
                    search.initSliderRange(this.params);
                });
            }

            if(cityField){
                $("#city")
                    .multiselect({
                        noneSelectedText: "<?php echo Yii::t('common', 'select city')?>",
                        checkAllText: "<?php echo Yii::t('common', 'check all')?>",
                        uncheckAllText: "<?php echo Yii::t('common', 'uncheck all')?>",
                        selectedText: "<?php echo Yii::t('common', '# of # selected')?>",
                        //minWidth: cityField.minWidth,
                        classes: "search-input-new search-city-height",
                        multiple: "false",
                        selectedList: 1,
                        width: 290
                    }).multiselectfilter({
                        label: "<?php echo Yii::t('common', 'quick search')?>",
                        placeholder: "<?php echo Yii::t('common', 'enter initial letters')?>",
                        width: 185
                    });
            }

            if(countFiled <= 10){
                if(advancedIsOpen){
                    if(isInner){
                        search.innerSetAdvanced();
                    }else{
                        search.indexSetNormal();
                        $('#more-options-link').hide();
                    }
                } else if(!isInner){
                    $('#more-options-link').hide();
                }
            } else {
                if(!isInner){
                    $('#more-options-link').show();
                }

                if(advancedIsOpen){
                    if(isInner){
                        search.innerSetAdvanced();
                    } else {
                        search.indexSetAdvanced();
                    }
                }
            }

        },

        initSliderRange: function(sliderParams){
            $( "#slider-range-"+sliderParams.field ).slider({
                range: true,
                min: sliderParams.min,
                max: sliderParams.max,
                values: [ sliderParams.min_sel , sliderParams.max_sel ],
                step: sliderParams.step,
                slide: function( e, ui ) {
                    $( "#"+sliderParams.field+"_min_val" ).html( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_min" ).val( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_max_val" ).html( ui.values[ 1 ] );
                    $( "#"+sliderParams.field+"_max" ).val( ui.values[ 1 ] );
                },
                stop: function(e, ui) {  changeSearch(); }
            });
        },

       /* indexSetNormal: function(){
            $("#homeintro").animate({"height" : "270"});
            $("div.index-header-form").animate({"height" : "234"});
            $("div.searchform-index").animate({"height" : "267"});
            $("#more-options-link").html("<?php echo tc("More options");?>");
            advancedIsOpen = 0;
        },

        indexSetAdvanced: function(){
            var height = search.getHeight();
            $("#homeintro").animate({"height" : height + 10});
            $("div.index-header-form").animate({"height" : height});
            $("div.searchform-index").animate({"height" : height + 10});
            $("#more-options-link").html("<?php echo tc("Less options");?>");
            advancedIsOpen = 1;
        },

        innerSetNormal: function(){
            $("#searchform-block").addClass("compact");
            $("#search-more-fields").hide();
            $("#more-options-link-inner").show();
            $("#more-options-img").hide();
            advancedIsOpen = 0;
        },

        innerSetAdvanced: function(){
            var height = search.getHeight();
            $("#searchform-block").removeClass("compact").animate({"height" : height + 20});
            $("#search_form").animate({"height" : height});
            $("#btnleft").removeClass("btnsrch-compact");
            $("#search-more-fields").show();
            $("#more-options-link-inner").hide();
            $("#more-options-img").show();
            advancedIsOpen = 1;
        },*/

        getHeight: function(){
            var height = countFiled * heightField + 30;

            if(height < minHeight){
                return minHeight;
            }

            return isInner ? height/2 + 20 : height;
        },

        renderForm: function(obj_type_id){
            $('#search_form').html(searchCache[obj_type_id].html);
            sliderRangeFields = searchCache[obj_type_id].sliderRangeFields;
            cityField = searchCache[obj_type_id].cityField;
            countFiled = searchCache[obj_type_id].countFiled + (loc ? 2 : 0);
            search.init();
            if(!useSearchCache){
                delete(searchCache[obj_type_id]);
            }
            changeSearch();
        }
    }

    $(function(){
        search.init();

        $('#objType').live('change', function(){
            var obj_type_id = $(this).val();
            if(typeof searchCache[obj_type_id] == 'undefined'){
                $.ajax({
                    url: BASE_URL + '/quicksearch/main/loadForm?' + $('#search-form').serialize(),
                    dataType: 'json',
                    type: 'GET',
                    data: { obj_type_id: obj_type_id, is_inner: <?php echo CJavaScript::encode($isInner);?>, compact: advancedIsOpen ? 0 : 1 },
                    success: function(data){
                        if(data.status == 'ok'){
                            searchCache[obj_type_id] = [];
                            searchCache[obj_type_id].html = data.html;
                            searchCache[obj_type_id].sliderRangeFields = data.sliderRangeFields;
                            searchCache[obj_type_id].cityField = data.cityField;
                            searchCache[obj_type_id].countFiled = data.countFiled;
                            search.renderForm(obj_type_id);
                        }
                    }
                })
            } else {
                search.renderForm(obj_type_id);
            }
        });

       /* if(isInner){
            $("#more-options-link-inner, #more-options-img").live('click', function(){
                if (advancedIsOpen) {
                    search.innerSetNormal();
                } else {
                    search.innerSetAdvanced();
                }
            });
        } else {
            $("#more-options-link").live('click', function(){
                if(advancedIsOpen){
                    search.indexSetNormal();
                } else {
                    search.indexSetAdvanced();
                }
            });
        }*/

        if(isInner && !compact){
            search.innerSetAdvanced();
        }
    });