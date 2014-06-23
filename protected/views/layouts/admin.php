<?php $this->beginContent('//layouts/main-admin', array('adminView' => 1)); ?>

<h2><?php echo $this->adminTitle; ?></h2>

<?php
	if ($this->menu) {
		$this->widget('bootstrap.widgets.TbMenu', array(
	        'type'=>'pills', // '', 'tabs', 'pills' (or 'list')
	        'stacked'=>false, // whether this is a stacked menu
			'items'=>$this->menu
		));
	}
	$this->widget('bootstrap.widgets.TbAlert');
?>

<?php echo $content; ?>

<?php $this->endContent(); ?>