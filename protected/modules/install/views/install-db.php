<?php
	Yii::app()->clientScript->registerCoreScript( 'jquery.ui' );
	Yii::app()->clientScript->scriptMap=array(
		'jquery-ui.css'=>false,
	);
	Yii::app()->clientScript->registerCssFile( Yii::app()->request->baseUrl.'/css/ui/jquery-ui-1.8.16.custom.css');

Yii::app()->clientScript->registerCss('xxx',
	'#install-progress .ui-progressbar-value{
		background-image:url('.Yii::app()->baseUrl.'/css/ui/images/pbar-ani.gif)
	}
');


?>

<div>
    <h2>Installation progress</h2>
</div>
<?php
	$this->widget('zii.widgets.jui.CJuiProgressBar',array(
		'value'=> 5,
		'id' => 'install-progress',
		// additional javascript options for the progress bar plugin
		'options' => array(
			'change' => new CJavaScriptExpression('function(event, ui){}'),
		),
		'htmlOptions'=>array(
			'style'=>'height:20px;',
		),
	));

	echo '<div id="log-area" style="width: 800px; margin: 20px auto; height: 350px; overflow: auto;">';
	echo '</div>';

	Yii::app()->clientScript->registerScript('install-db','
		var slices = "'.$slices.'";
		var currentSlice = 0;
		var sliceWeight = 100/slices;
		var currentProgress = 0;

		function startInstall(){
			getSlice(currentSlice);
		}

		function getSlice(num){
			$.ajax({
				url: "'.$this->createUrl('/install/main/getSlice').'",
				data: {num: num},
				success: function(msg){
					currentSlice++;
					if(currentSlice > slices){
						$("#log-area").html($("#log-area").html() + "Closing database ... OK<br/>Wait for final action...<br/>");
						scrollArea();
						finalRequest();
					} else {
						currentProgress = currentProgress + sliceWeight;
						$("#install-progress").progressbar({value: currentProgress});
						$("#log-area").html($("#log-area").html() + msg);

						scrollArea();
						getSlice(currentSlice);
					}
				},
			});
		}

		function finalRequest(){
			$.ajax({
				url: "'.$this->createUrl('/install/main/finalRequest').'",
				success: function(msg){
					$("#log-area").html($("#log-area").html() + msg)
					scrollArea();
					document.location.href="'.$this->createUrl('/site/index').'";
				},
			});
		}

		function scrollArea(){
			$("#log-area").each(function(){
			   // certain browsers have a bug such that scrollHeight is too small
			   // when content does not fill the client area of the element
			   var scrollHeight = Math.max(this.scrollHeight, this.clientHeight);
			   this.scrollTop = scrollHeight - this.clientHeight;
			});
		}

	', CClientScript::POS_END);

	Yii::app()->clientScript->registerScript('disable-links','
		$("a").on("click", function(){ return false; });
		startInstall();

	', CClientScript::POS_READY);