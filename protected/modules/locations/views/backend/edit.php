<h4>Редактирование региона <?php echo $region->name; ?><h4>

<?php
$form=$this->beginWidget('CustomForm', array(
    'id'=>'region-form',
    'enableClientValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
<?php
        echo $form->textArea($region, 'content', array(
        'class' => 'width500',
        ));
?>

<div class="rowold buttons">
   <?php $this->widget('bootstrap.widgets.TbButton',
       array('buttonType'=>'submit',
             'type'=>'primary',
             'icon'=>'ok white',
             'label'=> $region->isNewRecord ? tc('Add') : tc('Save'),
       )); ?>
        </div>

<?php $this->endWidget(); ?>

        <?php $this->widget('application.extensions.tinymce.SladekTinyMce'); ?>

        <script>
            tinymce.init({
                selector:'textarea',
                theme: "modern",
                width: 900,
                height: 300,
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            });
        </script>

