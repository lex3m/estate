<?php $this->renderPartial('//../modules/apartments/views/backend/__form_general', array('model' => $model, 'form' => $form));?>

<div class="tab-pane" id="tab-extended">
	<?php
	if ($model->is_free_from == '0000-00-00') {
		$model->is_free_from = '';
	}
	if ($model->is_free_to == '0000-00-00') {
		$model->is_free_to = '';
	}
	?>

	<?php if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="rowold">
		<?php echo $form->checkboxRow($model, 'is_special_offer'); ?>
	</div>
	<?php } ?>

	<?php if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="special-calendar">
		<?php echo $form->labelEx($model, 'is_free_from', array('class' => 'noblock')); ?> /
		<?php echo $form->labelEx($model, 'is_free_to', array('class' => 'noblock')); ?><br/>
		<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_from',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		/
		<?php
		$this->widget('application.extensions.FJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_to',
			'range' => 'eval_period',
			'language' => Yii::app()->language,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		<?php echo $form->error($model, 'is_free_from'); ?>
		<?php echo $form->error($model, 'is_free_to'); ?>
	</div>
	<?php } ?>


	<?php
	if (!isset($element)) {
		$element = 0;
	}

	if (issetModule('bookingcalendar') && $model->active != Apartment::STATUS_DRAFT) {
		$this->renderPartial('//../modules/bookingcalendar/views/_form', array('apartment' => $model, 'element' => $element));
	}
	?>

<?php if($model->canShowInForm('num_of_rooms')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'num_of_rooms'); ?>
        <?php echo Apartment::getTip('num_of_rooms');?>
		<?php echo $form->dropDownList($model, 'num_of_rooms',
		array_merge(
			array(0 => ''),
			range(1, param('moduleApartments_maxRooms', 8))
		), array('class' => 'width50')); ?>
		<?php echo $form->error($model, 'num_of_rooms'); ?>
	</div>
	<div class="clear5"></div>
<?php } ?>

    <?php if($model->canShowInForm('floor_all')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'floor', array('class' => 'noblock')); ?> /
		<?php echo $form->labelEx($model, 'floor_total', array('class' => 'noblock')); ?><br/>
        <?php echo Apartment::getTip('floor_all');?>
		<?php echo $form->dropDownList($model, 'floor',
		array_merge(
			array('0' => ''),
			range(1, param('moduleApartments_maxFloor', 30))
		), array('class' => 'width50')); ?> /
		<?php echo $form->dropDownList($model, 'floor_total',
		array_merge(
			array('0' => ''),
			range(1, param('moduleApartments_maxFloor', 30))
		), array('class' => 'width50')); ?>
		<?php echo $form->error($model, 'floor'); ?>
		<?php echo $form->error($model, 'floor_total'); ?>
        <?php echo Apartment::getTip('floor_all');?>
	</div>
    <?php } ?>

    <?php if($model->canShowInForm('window_to')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'window_to'); ?>
        <?php echo Apartment::getTip('window_to');?>
		<?php echo $form->dropDownList($model, 'window_to', WindowTo::getWindowTo(), array('class' => 'width150')); ?>
		<?php echo $form->error($model, 'window_to'); ?>
	</div>
    <?php } ?>

    <?php if($model->canShowInForm('berths')){ ?>
	<div class="rowold">
		<?php echo $form->labelEx($model, 'berths'); ?>
        <?php echo Apartment::getTip('berths');?>
		<?php echo $form->textField($model, 'berths', array('class' => 'width150', 'maxlength' => 255)); ?>
		<?php echo $form->error($model, 'berths'); ?>
	</div>
    <?php } ?>

    <?php if ($model->canShowInForm('references')) { ?>

	<div class="apartment-description-item">
		<?php

			$prev = '';
			$column1 = 0;
			$column2 = 0;
			$column3 = 0;

			$count = 0;
			foreach ($model->references as $catId => $category) {
				if (isset($category['values']) && $category['values'] && isset($category['title'])) {

					if ($prev != $category['style']) {
						$column2 = 0;
						$column3 = 0;
						echo '<div class="clear">&nbsp;</div>';
					}
					$$category['style']++;
					$prev = $category['style'];
					echo '<div class="' . $category['style'] . '">';
					echo '<input type="checkbox" class="ref-check-all" title="'.CHtml::encode(tc('check all')).'"/>
						<span class="viewapartment-subheader">'
						.$category['title'] . '</span>';

					echo '<ul class="no-disk">';
					foreach ($category['values'] as $valId => $value) {
						if ($value) {
							$checked = $value['selected'] ? 'checked="checked"' : '';
							if (array_key_exists('title', $value)) {
								echo '<li><input type="checkbox"  class="s-categorybox" id="category[' . $catId . '][' . $valId . ']" name="category[' . $catId . '][' . $valId . ']" ' . $checked . '/>
									<label for="category[' . $catId . '][' . $valId . ']" />' . $value['title'] . '</label></li>';
							}
						}
					}
					echo '</ul>';
					echo '</div>';
					if (($category['style'] == 'column2' && $column2 == 2) || $category['style'] == 'column3' && $column3 == 3) {
						echo '<div class="clear"></div>';
					}
				}

			}
			Yii::app()->clientScript->registerScript('ref-check-all', '
				$(".ref-check-all").on("click", function(){
					var elems = $(this).closest("div").find(".s-categorybox");
					if($(this).is(":checked")){
						elems.attr("checked", "checked");
					} else {
						elems.removeAttr("checked");
					}
				});

				$(".ref-check-all").each(function(){
					var elems = $(this).closest("div").find(".s-categorybox");
					if($(this).closest("div").find(".s-categorybox:checked").length == elems.length){
						$(this).attr("checked", "checked");
					}
				});
			', CClientScript::POS_READY);
		?>
		<div class="clear"></div>
	</div>

	<div class="clear">&nbsp;</div>
    <?php } ?>

	<?php

    if ($model->canShowInForm('description_near')) {
	$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $model,
		'field' => 'description_near',
		'type' => 'text'
	));
        echo '<div class="clear">&nbsp;</div>';
    }

    if ($model->canShowInForm('phone')) {
        echo $form->label($model, 'phone');
        echo Apartment::getTip('phone');
        echo $form->textField($model, 'phone');
    }

    if ($model->canShowInForm('note')){
        echo $form->label($model, 'note');
        echo Apartment::getTip('note');
        echo $form->textArea($model, 'note', array(
            'class' => 'width500',
        ));
    }

    if(issetModule('formeditor')){
        Yii::import('application.modules.formeditor.models.HFormEditor');
        $rows = HFormEditor::getExtendedFields();
        HFormEditor::renderFormRows($rows, $model);
    }

    ?>

</div>

	<?php

	/*if ($model->isNewRecord) {
		echo '<p>' . tt('After pressing the button "Create", you will be able to load photos for the listing and to mark the property on the map.', 'apartments') . '</p>';
	}*/

	if (Yii::app()->user->getState('isAdmin')) {
		$this->widget('bootstrap.widgets.TbButton',
			array('buttonType' => 'submit',
				'type' => 'primary',
				'icon' => 'ok white',
				'label' => $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'),
				'htmlOptions' => array(
					'onclick' => "$('#Apartment-form').submit(); return false;",
				)
			));
	} else {
		echo '<div class="row buttons save">';
		echo CHtml::button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), array(
			'onclick' => "$('#Apartment-form').submit(); return false;", 'class' => 'big_button',
		));
		echo '</div>';
	}
?>


