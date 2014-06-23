<div class="form">

	<?php
	Yii::app()->clientScript->registerScript('selectFieldsReady', '
		selectFields($("#Menu_type").val());
	', CClientScript::POS_READY);


	if($model->special) {
		$js = '
			hideAll();
			$("#menu_type").hide();
			$("#menu_title").show();
			$("#menu_pageid").hide();
			$("#menu_is_blank").hide();

		';
		if($model->id == InfoPages::MAIN_PAGE_ID) {
			$js .= '

			';
		}
		Yii::app()->clientScript->registerScript('selectSpecial', $js, CClientScript::POS_READY);
	}


	Yii::app()->clientScript->registerScript('selectFields', '
		function hideAll(){
			$("#menu_title").hide();
			$("#menu_href").hide();
			$("#menu_pageid").hide();
			$("#menu_is_blank").hide();
		}

		function selectFields(type){
			hideAll();
			if(type == ' . Menu::LINK_NONE . '){
				$("#menu_title").show();
			}

			if(type == ' . Menu::LINK_NEW_MANUAL . '){
				$("#menu_title").show();
				$("#menu_href").show();
				$("#menu_is_blank").show();
			}

			if(type == ' . Menu::LINK_NEW_INFO . '){
				$("#menu_title").show();
				$("#menu_pageid").show();
			}
		}
	', CClientScript::POS_END);

	$form = $this->beginWidget('CustomForm', array(
		'id' => $this->modelName . '-form',
		//'enableAjaxValidation'=>true,
	));
	?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold" id="menu_type">
		<?php echo $form->label($model, 'type', array('required' => true)); ?>
		<?php
		echo $form->dropDownList($model, 'type', $model->getTypes(), array(
			'class' => 'width450',
			'onChange' => 'js: selectFields(this.value);',
		));
		?>
		<?php echo $form->error($model, 'type'); ?>
	</div>

	<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $model,
			'field' => 'title',
			'type' => 'string'
		));
	?>

	<div class="rowold" id="menu_href">
		<?php
			$this->widget('application.modules.lang.components.langFieldWidget', array(
				'model' => $model,
				'field' => 'href',
				'type' => 'string'
			));
		?>
	</div>

	<div class="rowold" id="menu_is_blank">
		<?php echo $form->checkboxRow($model,'is_blank'); ?>
		<?php echo $form->error($model,'is_blank'); ?>
	</div>

	<div class="rowold" id="menu_pageid">
		<?php echo $form->labelEx($model,'pageId'); ?>
		<?php echo $form->dropDownList($model,'pageId', InfoPages::getInfoPagesAddList(), array('id'=>'pageid')); ?>
		<?php echo $form->error($model,'pageId'); ?>
	</div>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
			));
		?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- form -->