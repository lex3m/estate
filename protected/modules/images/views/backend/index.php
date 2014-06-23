<?php
	$this->pageTitle .= ' - '. tc('Images');
	$this->adminTitle = tc('Images');

	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/colorpicker.css');
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/ui/jquery-ui-1.8.16.custom.css');

	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/bootstrap-colorpicker.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerCoreScript('jquery.ui');

?>

<div class="form">

	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>true,
		'htmlOptions'=>array('class'=>'well', 'enctype'=>'multipart/form-data'),
	)); ?>

    <p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="rowold">
		<?php echo $form->labelEx($model, 'maxImageWidth'); ?>
		<?php echo $form->textField($model, 'maxImageWidth', array('size' => 10)); ?>
		<?php echo $form->error($model, 'maxImageWidth'); ?>
    </div>

    <div class="rowold">
		<?php echo $form->labelEx($model, 'maxImageHeight'); ?>
		<?php echo $form->textField($model, 'maxImageHeight', array('size' => 10)); ?>
		<?php echo $form->error($model, 'maxImageHeight'); ?>
    </div>

	<div class="rowold">
		<?php echo $form->labelEx($model, 'useWatermark'); ?>
		<?php echo $form->radioButtonList($model, 'useWatermark', array(
			'1' => tc('Yes'),
			'0' => tc('No'),
		), array(
			'onChange' => 'showArea(this.value);',
		)); ?>
	</div>

    <div id="watermarkSettings">
        <div class="rowold">
			<?php echo $form->labelEx($model, 'watermarkType'); ?>
			<?php echo $form->radioButtonList($model, 'watermarkType', array(
				ImageSettings::WATERMARK_FILE => tc('Image'),
				ImageSettings::WATERMARK_TEXT => tc('Text'),
			), array(
				'onChange' => 'showMarkArea(this.value);',
			)); ?>
        </div>

		<div id="watermarkImageSettings">
            <div class="rowold">
				<?php
					echo CHtml::activeLabel($model, 'watermarkFile', array('required' => true));
					if(param('watermarkFile')){
						echo CHtml::image(Yii::app()->request->getBaseUrl().'/'.Images::UPLOAD_DIR.'/'.param('watermarkFile'));
						echo '<br/><br/>';
					}
				?>

                <div class="padding-bottom10">
					<span class="label label-info">
						<?php
							echo Yii::t('module_slider', 'Supported file: {supportExt}.',
								array('{supportExt}' => param('watermarkFileTypes', 'gif, png, jpg'))).'';
						?>
					</span>
                </div>
				<?php echo $form->fileField($model, 'watermarkFile'); ?>
				<?php echo $form->error($model, 'watermarkFile'); ?>
			</div>
		</div>
		<div id="watermarkTextSettings">
            <div class="rowold">
				<?php echo $form->labelEx($model, 'watermarkContent'); ?>
				<?php echo $form->textField($model, 'watermarkContent', array('class' => 'span4')); ?>
				<?php echo $form->error($model, 'watermarkContent'); ?>
            </div>

            <div class="rowold">
				<?php echo $form->labelEx($model, 'watermarkTextColor'); ?>

                <div class="input-append color" data-color="<?php echo $model->watermarkTextColor; ?>" data-color-format="rgb" id="watermarkTextColor">
                    <input id="ImageSettings_watermarkTextColor" type="text" class="span2" value="<?php echo $model->watermarkTextColor; ?>" name="ImageSettings[watermarkTextColor]" />
                    <span class="add-on"><i style="background-color: <?php echo $model->watermarkTextColor; ?>;"></i></span>
                </div>

				<?php echo $form->error($model, 'watermarkTextColor'); ?>
            </div>

            <div class="rowold">
				<?php echo $form->labelEx($model, 'watermarkTextSize'); ?>
                <div id="sizeSelect" style="width: 225px;"></div>
                <br/>
				<?php echo $form->textField($model, 'watermarkTextSize', array('class' => 'span1')); ?>
				<?php echo $form->error($model, 'watermarkTextSize'); ?>
            </div>


            <div class="rowold">
				<?php echo $form->labelEx($model, 'watermarkTextOpacity'); ?>
                <div id="opacitySelect" style="width: 225px;"></div>
				<br/>
                <div class="input-append">
					<?php echo $form->textField($model, 'watermarkTextOpacity', array('class' => 'span1')); ?>
                    <span class="add-on">%</span>
                </div>
				<?php echo $form->error($model, 'watermarkTextOpacity'); ?>
            </div>
		</div>

        <div class="rowold">
			<?php echo CHtml::activeLabel($model, 'watermarkPosition', array('required' => true)); ?>
			<div id="waermarkPositionTemplate">
				<div class="relative">
					<input type="radio"
						style="position:absolute; top: 3px; left: 7px;"
						name="ImageSettings[watermarkPosition]"
						value="<?php echo ImageSettings::POS_LEFT_TOP; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_LEFT_TOP ? 'checked="checked"':''; ?>
					/>
                    <input type="radio"
                           style="position:absolute; top: 116px; left: 7px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_LEFT_MIDDLE; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_LEFT_MIDDLE ? 'checked="checked"':''; ?>
                    />
                    <input type="radio"
                           style="position:absolute; top: 224px; left: 7px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_LEFT_BOTTOM; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_LEFT_BOTTOM ? 'checked="checked"':''; ?>
					/>



                    <input type="radio"
                           style="position:absolute; top: 3px; left: 118px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_CENTER_TOP; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_CENTER_TOP ? 'checked="checked"':''; ?>
					/>
                    <input type="radio"
                           style="position:absolute; top: 116px; left: 118px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_CENTER_MIDDLE; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_CENTER_MIDDLE ? 'checked="checked"':''; ?>
					/>
                    <input type="radio"
                           style="position:absolute; top: 224px; left: 118px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_CENTER_BOTTOM; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_CENTER_BOTTOM ? 'checked="checked"':''; ?>
					/>


                    <input type="radio"
                           style="position:absolute; top: 3px; left: 230px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_RIGHT_TOP; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_RIGHT_TOP ? 'checked="checked"':''; ?>
                            />
                    <input type="radio"
                           style="position:absolute; top: 116px; left: 230px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_RIGHT_MIDDLE; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_RIGHT_MIDDLE ? 'checked="checked"':''; ?>
                            />
                    <input type="radio"
                           style="position:absolute; top: 224px; left: 230px;"
                           name="ImageSettings[watermarkPosition]"
                           value="<?php echo ImageSettings::POS_RIGHT_BOTTOM; ?>"
						<?php echo $model->watermarkPosition == ImageSettings::POS_RIGHT_BOTTOM ? 'checked="checked"':''; ?>
                            />
				</div>
			</div>
			<?php echo $form->error($model, 'watermarkPosition'); ?>
		</div>
    </div>

    <div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
		array('buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'ok white',
			'label'=> tc('Save'),
		)); ?>

		<!--<?php $this->widget('bootstrap.widgets.TbButton',
		array('buttonType'=>'button',
			'icon'=>'camera white',
			'label'=> tc('Preview'),
		)); ?>-->
    </div>

	<?php $this->endWidget(); ?>

</div><!-- form -->

<?php
	Yii::app()->clientScript->registerScript('watermarkLoad', '
		$("#watermarkTextColor").colorpicker({
			format: "hex"
		});

		$("#opacitySelect").slider({
			orientation: "horizontal",
			range: "min",
			min: 0,
			max: 100,
			value: "'.$model->watermarkTextOpacity.'",
			slide: function (event, ui) {
				$(\'[name="ImageSettings[watermarkTextOpacity]"]\').val(ui.value);
			}
   		});

		$("#sizeSelect").slider({
			orientation: "horizontal",
			range: "min",
			min: 0,
			max: 48,
			value: "'.$model->watermarkTextSize.'",
			slide: function (event, ui) {
				$(\'[name="ImageSettings[watermarkTextSize]"]\').val(ui.value);
			}
   		});

		showArea($(\'[name="ImageSettings[useWatermark]"]:checked\').val());
		showMarkArea($(\'[name="ImageSettings[watermarkType]"]:checked\').val());

		function showMarkArea(val){
			$("#watermarkTextSettings").hide();
			$("#watermarkImageSettings").hide();

			if(val == "'.ImageSettings::WATERMARK_FILE.'"){
				$("#watermarkImageSettings").show();
			}
			if(val == "'.ImageSettings::WATERMARK_TEXT.'"){
				$("#watermarkTextSettings").show();
			}
		}

		function showArea(val){
			if(val == 1){
				$("#watermarkSettings").show();
			} else {
				$("#watermarkSettings").hide();
			}
		}
	', CClientScript::POS_END);



