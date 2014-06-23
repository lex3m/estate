<!--<div class="flash-notice"><?php //echo tc(''); ?></div>-->

<?php
	$panExists = false;
	if($model->panorama){
		foreach($model->panorama as $panoramas){
			if($panoramas->isFileExists()){
				$panExists = true;
				$panoramas->renderPanorama();

				echo '<div>'.CHtml::button(tc('Delete'), array(
					'onclick' => 'document.location.href="'.Yii::app()->controller->createUrl('deletepanorama', array('id' => $panoramas->id, 'apId' => $model->id)).'";'
				)).'</div><br/>';
			}
		}
	}

	if($panExists){
		echo '<div>'.CHtml::button(tc('Add'), array(
			'onclick' => '$(".add-panorama").toggle();',
		)).'</div>';
		Yii::app()->clientScript->registerScript('add-panorama-toggle', '
			$(".add-panorama").hide();
		', CClientScript::POS_READY);
	}



?>

<div class="rowold add-panorama" >
	<?php echo $form->labelEx($model,'panoramaFile'); ?>
	<?php echo $form->fileField($model, 'panoramaFile'); ?>
    <div class="padding-bottom10">
		<span class="label label-info">
			<?php echo Yii::t('module_apartments', 'Supported file: {supportExt}.', array('{supportExt}' => ApartmentPanorama::model()->supportedExt));?>
		</span>
    </div>
	<?php echo $form->error($model,'panoramaFile'); ?>
</div>