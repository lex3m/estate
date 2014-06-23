<div class="<?php echo $divClass; ?>">
	<span class="search-term<?php echo $isInner ? ' search-term-inner' : ''?>">
		<input type="text" onblur="if (this.value == '') {this.value = <?php echo CJavaScript::encode(tc("Search by description or address"));?>;}" onfocus="if (this.value == <?php echo CJavaScript::encode(tc("Search by description or address"));?> ) { this.value = ''; }" value="<?php echo (isset($this->term)) ? $this->term : tc("Search by description or address");?>" class="textbox" name="term" id="search_term_text" maxlength="50">
		<input type="submit" value="<?php echo tc("Search");?>" onclick="prepareSearch(); return false;">
		<input type="hidden" value="0" id="do-term-search" name="do-term-search">
	</span>
</div>

<?php
Yii::app()->clientScript->registerScript('search-term-init', '
			$(document).ready(function() {
				$(".search-term input#search_term_text").keypress(function(e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if(code == 13) { // Enter keycode
						prepareSearch();
						return false;
					}
				});
			});

			function prepareSearch() {
				var term = $(".search-term input#search_term_text").val();

				if (term != '.CJavaScript::encode(tc("Search by description or address")).') {
					if (term.length >= '.Yii::app()->controller->minLengthSearch.') {
						term = term.split(" ");
						term = term.join("+");
						$("#do-term-search").val(1);
						window.location.replace("'.Yii::app()->createAbsoluteUrl('/quicksearch/main/mainsearch').'?term="+term+"&do-term-search=1");
					}
					else {
						alert("'.Yii::t('common', 'Minimum {min} characters.', array('{min}' => Yii::app()->controller->minLengthSearch)).'");
					}
				}
			}
		', CClientScript::POS_END);