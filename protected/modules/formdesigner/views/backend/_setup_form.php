<p>
    <strong><?php echo tt('Settings for the field', 'formdesigner'); ?></strong>: <?php echo Apartment::model()->getAttributeLabel($model->field); ?>
</p>

<?php
/** @var CustomForm $form */
$form=$this->beginWidget('CustomForm', array(
    'id'=>'form-designer-filter'
));

echo $form->errorSummary($model);

echo CHtml::hiddenField('id', $model->id);

echo CHtml::hiddenField('FormDesigner[save]', $model->id);

echo $form->checkBoxRow($model, 'visible');

echo $form->checkBoxListRow($model, 'objTypesArray', ApartmentObjType::getList());

$this->widget('application.modules.lang.components.langFieldWidget', array(
    'model' => $model,
    'field' => 'tip',
    'type' => 'string'
));

echo '<div class="clear"></div>';

$this->widget('bootstrap.widgets.TbButton',
    array('buttonType'=>'submit',
        'type'=>'primary',
        'icon'=>'ok white',
        'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
    )
);

$this->endWidget();
?>