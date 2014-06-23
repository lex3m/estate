<?php
$this->pageTitle=Yii::app()->name . ' - ' . tt('Manage social settings');
$this->breadcrumbs=array(
	tt('Social settings'),
);
$this->menu = array(
	array(),
);
$this->adminTitle = tt('Manage social settings');


$info = '';
if (!SocialauthModel::getSocialParamValue('useGoogleOauth'))
	$info .= Yii::t('module_socialauth', 'Go to link for register Google application - {link}', array('{link}' => CHtml::link('https://code.google.com/apis/console/', 'https://code.google.com/apis/console/', array('target' => '_blank')))).'<br />';

if (!SocialauthModel::getSocialParamValue('useTwitter'))
	$info .= Yii::t('module_socialauth', 'Go to link for register Twitter application - {link}', array('{link}' => CHtml::link('https://dev.twitter.com/apps/new', 'https://dev.twitter.com/apps/new',  array('target' => '_blank')))).'<br />';

if (!SocialauthModel::getSocialParamValue('useFacebook'))
	$info .= Yii::t('module_socialauth', 'Go to link for register Facebook application - {link}', array('{link}' => CHtml::link('https://developers.facebook.com/apps/', 'https://developers.facebook.com/apps/',  array('target' => '_blank')))).'<br />';

if (!SocialauthModel::getSocialParamValue('useVkontakte'))
	$info .= Yii::t('module_socialauth', 'Go to link for register VK.com application - {link}', array('{link}' => CHtml::link('http://vk.com/editapp?act=create&site=1', 'http://vk.com/editapp?act=create&site=1',  array('target' => '_blank')))).'<br />';

if (!SocialauthModel::getSocialParamValue('useMailruOAuth'))
	$info .= Yii::t('module_socialauth', 'Go to link for register Mail.ru application - {link}', array('{link}' => CHtml::link('http://api.mail.ru/sites/my/add', 'http://api.mail.ru/sites/my/add',  array('target' => '_blank')))).'<br />';

if ($info)
	Yii::app()->user->setFlash('info', $info);



$this->widget('CustomGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
    'id'=>'socialauth-table',
	'columns'=>array(
        array(
            'header'=>tt('Section'),
            'value' => 'tt($data->section)',
            'filter' => CHtml::dropDownList('section_filter', $currentSection, $this->getSections()),
        ),
		array(
            'header' => tt('Setting'),
			'value'=>'$data->title',
			'type'=>'raw',
			'htmlOptions' => array('class' => 'width250'),
		),
		array(
			'name'=>'value',
            'type'=>'raw',
			'value' => 'SocialauthModel::getAdminValue($data)',
			'htmlOptions' => array('class' => 'width150'),
            'filter' => false,
            'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{update}',
			'buttons' => array(
				'update' => array(
					'visible' => 'SocialauthModel::getVisible($data->type)',
					//'options' => array('data-toggle' => 'modal'),
					'click' => 'js: function() { updateConfig($(this).attr("href")); return false; }'
					)
				)
		),
	),
)); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>

<div id="form_param"></div>

<div class="modal-footer">
    <a href="#" class="btn btn-primary" onclick="saveChanges(); return false;"><?php echo tc('Save'); ?></a>

    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>tc('Close'),
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    function updateConfig(href){
        $('#myModal').modal('show');
        $('#form_param').html('<img src="<?php echo Yii::app()->request->baseUrl."/images/pages/indicator.gif"; ?>" alt="<?php echo tc('Content is loading ...'); ?>" style="position:absolute;margin: 10px;">');
        $('#form_param').load(href + '&ajax=1');
    }

    function saveChanges(){
        var val = $('#config_value').val();

        if(!val) {
            alert('<?php echo tt('Enter the required value');?>');
            return false;
        }

        var id = $('#config_id').val();
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl.'/socialauth/backend/main/updateAjax'; ?>",
            data: { "id": id, "val": val },
			success: function(msg){
				$('#socialauth-table').yiiGridView.update('socialauth-table');
				$('#myModal').modal('hide');
				//console.log( "Data Saved: " + msg );
				if (msg == 'error_save') {
					document.location.href = '<?php echo Yii::app()->createUrl("/socialauth/backend/main/admin"); ?>';
				}
			}
        });
        return;
    }

</script>