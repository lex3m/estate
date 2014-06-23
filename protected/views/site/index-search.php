<form id="search-form" action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch');?>" method="get">
    <div class="searchform-back">
 
        <div class="searchform-index" align="left">
            <div class="index-header-form" id="search_form">
                <?php $this->renderPartial('//site/_search_form', array('isInner' => 0)); ?>
            </div>

            <div class="index-search-button-line">
               <!-- <a href="javascript: void(0);" id="more-options-link"><?php echo tc('More options'); ?></a>-->
                <a href="javascript: void(0);" onclick="doSearchAction();" id="btnleft" class="btnsrch"><?php echo tc('Search'); ?></a>
				<?php
					Yii::app()->clientScript->registerScript('doSearchActionIndex', '
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
	'isInner' => 0
	),
	true,
	false
);
Yii::app()->clientScript->registerScript('search-params-index-search', $content, CClientScript::POS_HEAD, array(), true);
?>



