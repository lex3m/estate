
<div class="form_designer">
<?php
$this->renderPartial('_setup_form', array(
    'model' => $model,
));
?>
</div>

<script type="text/javascript">
    var formSetup = {
        apply: function(){
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('/formdesigner/backend/main/setup'); ?>',
                type: 'post',
                dataType: 'json',
                data: $('#form-designer-filter').serialize(),
                success: function(data){
                    if(data.status == 'ok'){
                        message('Настройки успешно сохранены');
                        $('#form_el_'+data.id).replaceWith(data.html);
                        tempModal.close();
                        tempModal.init();
                    }else{
                        $('#setup_form').html(data.html);
                    }
                },
                error: function(){
                    error('<?php echo tc('Error. Repeat attempt later'); ?>');
                }
            });
        }
    }
</script>
