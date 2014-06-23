<?php if ($video) :?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/flowplayer/flowplayer-3.2.12.min.js', CClientScript::POS_END); ?>

	<?php
		$filePath = Yii::app()->request->baseUrl.'/uploads/video/'.$apartment_id.'/'.$video;
		$fileFolder = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$apartment_id.DIRECTORY_SEPARATOR.$video;
	?>

	<?php if (file_exists($fileFolder)) : ?>
		<a href="<?php echo $filePath; ?>" style="display:block;width:550px;height:320px" id="player-<?php echo $id;?>"></a>
		<?php
			Yii::app()->clientScript->registerScript('player-'.$id.'', '
				flowplayer("player-'.$id.'", "'.Yii::app()->request->baseUrl."/js/flowplayer/flowplayer-3.2.16.swf".'",
				{
					clip: {
						autoPlay: false
					}
				});
			', CClientScript::POS_END);
		?>
	<?php endif; ?>
<?php endif; ?>
