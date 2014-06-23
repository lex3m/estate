<?php
$compact = param("useCompactInnerSearchForm", true);

if(!isset($this->aData['searchOnMap'])){ ?>
    <form id="search-form" action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch');?>" method="get">
        <?php
        if (isset($this->userListingId) && $this->userListingId) {
            echo CHtml::hiddenField('userListingId', $this->userListingId);
        }

        $loc = (issetModule('location') && param('useLocation', 1)) ? "-loc" : "";
        ?>
        <div class="searchform-back">
            <div class="searchform<?php if($compact) echo ' compact';?>" align="left" id="searchform-block">
                <div class="header-form-line select-num-of-rooms-inner header-small-search" id="search_form">
                    <?php $this->renderPartial('//site/_search_form', array(
                        'isInner' => 1,
                        'compact' => $compact,
                    ));
                    ?>
                </div>
                <?php
                if($compact){
                    echo '<img style="display: none;" id="more-options-img" class="search-collapse" src="'.Yii::app()->baseUrl . '/images/design/collapse.png" title="'.tc("Collapse search").'">';
                }
                ?>
                <div class="inner-search-button-line">
                    <a href="javascript: void(0);" onclick="doSearchAction();" id="btnleft" class="btnsrch btnsrch-inner<?php echo $compact ? ' btnsrch-compact' : ''; ?>"><?php echo Yii::t('common', 'Search'); ?></a>
					<?php
					Yii::app()->clientScript->registerScript('doSearchActionInner', '
						function doSearchAction() {
							var term = $(".search-term input#search_term_text").val();
							if (term.length < '.Yii::app()->controller->minLengthSearch.' || term == "'.tc("Search by description or address").'") {
								$(".search-term input#search_term_text").attr("disabled", "disabled");
							}

							$("#search-form").submit();
						}
					', CClientScript::POS_HEAD, array(), true);
					?>
                </div>
            </div>
        </div>
    </form>
<?php
	$content = $this->renderPartial('//site/_search_js', array(
		'isInner' => 1
		),
		true,
		false
	);
	Yii::app()->clientScript->registerScript('search-params-inner-search', $content, CClientScript::POS_HEAD, array(), true);

} else {
    //echo '<br>';
}
?>
