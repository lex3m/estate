<div class="video-block">
	<?php
		if($data->video){
			$videoFileExists = false;

			$videoHtml = array();
			$count = 0;

			foreach($data->video as $video){
				if($video->isFile()){
					if($video->isFileExists()){
						$videoFileExists = true;
						echo '<div class="video-file-block">';
						echo '<a class="view-video-file" href="'.$video->getFileUrl().'" id="player-'.$video->id.'"></a>';
						echo '</div>';

						Yii::app()->clientScript->registerScript('player-'.$video->id.'', '
							flowplayer("player-'.$video->id.'", "'.Yii::app()->request->baseUrl."/js/flowplayer/flowplayer-3.2.16.swf".'",
							{
								clip: {
									autoPlay: false
								},
							});
						', CClientScript::POS_READY);
					}
				}
				if($video->isHtml()){
					echo '<div class="video-html-block" id="video-block-html-'.$count.'"></div>';
					$videoHtml[$count] = CHtml::decode($video->video_html);
					$count++;
				}
			}
			if($videoFileExists){
				Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/flowplayer/flowplayer-3.2.12.min.js', CClientScript::POS_END);
			}
			$script = '';
			if($videoHtml){
				foreach($videoHtml as $key => $value){
					$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
				}
			}
			if($script){
				Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
			}
		}
	?>
    <div class="clear"></div>
</div>

<?php

	$script = '';
	if($videoHtml){
		foreach($videoHtml as $key => $value){
			$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
		}
	}

	if($script){
		Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
	}

