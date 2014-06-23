<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
                'id'=>'licensewidget',
                'cssFile'=>'jquery-ui-1.8.16.custom.css',
                'theme'=>'redmond',
                'themeUrl'=>Yii::app()->request->baseUrl.'/css/ui',
                'options'=>array(
                    'title'=>tFile::getT('module_install', 'License_agreement_s'),
                    'autoOpen'=>$this->autoOpen ? true : false,
                    'modal'=>'true',
                    'show'=>'puff',
                    'hide'=>'slide',
                    'width'=>'920px',
                    'height'=>'auto',
                    'resizable' =>false,
                    'buttons'=>array(tFile::getT('module_install', 'Accept')=>'js:function() {
                        $("#InstallForm_agreeLicense").attr("checked", "checked");
                        $(this).dialog("close");
                    }'),
                ),
            ));
?>

<h2><?php echo tFile::getT('module_install', 'License_agreement_s');?></h2>
<?php
if(isFree()){
	echo tFile::getT('module_install', 'freeLicenseText');
} else {
	echo tFile::getT('module_install', 'licenseText');
}

$this->endWidget('zii.widgets.jui.CJuiDialog');