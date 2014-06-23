<?php

$this->pageTitle=Yii::app()->name . ' - ' . tc('Manage modules');
$this->breadcrumbs=array(
	tc('Modules'),
);
$this->menu = array(
	array(),
);
$this->adminTitle = tc('Manage modules');

	$freeModules = ConfigurationModel::getFreeModules();
	$proModules = ConfigurationModel::getProModules();
	$modules = CMap::mergeArray($freeModules, $proModules);


	if($modules){
		foreach($modules as $module){

			$enabled = false;
			$isFree = true;
			if(param('module_enabled_'.$module)){
				$type = 'success';
				$enabled = true;
			} else {
				$type = 'warning';
			}

			if(in_array($module, $proModules)){
				$isFree = false;
			}

			if(!$isFree){
				if(!issetModule($module, true)){
					$type = 'danger';
					$enabled = false;
				}
			}

			echo '<div class="alert in alert-block fade alert-'.$type.'">';

			echo '<div class="row-fluid">';
			echo '<div class="span3">';

			echo '<strong>';
			if($enabled){
				echo tc('Module is enabled');
			} else {
				if($type == 'danger'){
					echo tc('Module is not installed');
				} else {
					echo tc('Module is disabled');
				}
			}
			echo '</strong></br>';

			if($type != 'danger'){
				$this->widget('bootstrap.widgets.TbButton',
					array('buttonType' => 'link',
						'type' => 'success',
						'icon' => 'play white',
						'label' => tc('Enable'),
						'url' => Yii::app()->controller->createUrl('manipulate', array('type' => 'enable', 'module' => $module)),
						'htmlOptions' => array(
							'disabled' => $enabled ? 'disabled' : '',
						)
					)
				);
				echo '&nbsp;';
				$this->widget('bootstrap.widgets.TbButton',
					array('buttonType' => 'link',
						'type' => 'warning',
						'icon' => 'stop white',
						'label' => tc('Disable'),
						'url' => Yii::app()->controller->createUrl('manipulate', array('type' => 'disable', 'module' => $module)),
						'htmlOptions' => array(
							'disabled' => !$enabled ? 'disabled' : '',
						)
					)
				);
			} else {
				if(Yii::app()->language == 'ru'){
					$url = 'http://open-real-estate.info/ru/open-real-estate-modules';
				} else {
					$url = 'http://open-real-estate.info/en/open-real-estate-modules';
				}
				$this->widget('bootstrap.widgets.TbButton',
					array('buttonType' => 'link',
						'type' => 'primary',
						'icon' => 'plus white',
						'label' => tc('Buy module'),
						'url' => $url,
						'htmlOptions' => array(
							'disabled' => $enabled ? 'disabled' : '',
							'target' => '_blank',
						)
					)
				);
			}
			echo '</div>'; // span3

			echo '<div class="span9"><strong>'.tc('module_name_'.$module).'</strong><br/>'.tc('module_description_'.$module).'</div>';

			echo '</div>'; // class=row
			echo '</div>'; // class=alert
		}
	}




