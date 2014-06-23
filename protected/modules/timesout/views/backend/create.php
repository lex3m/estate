<?php

$this->adminTitle = tt('Add value', 'windowto');

$this->menu=array(
    array('label'=>tt('Manage reference', 'windowto'), 'url'=>array('admin')),
	//array('label'=>tt('Add value'), 'url'=>array('create')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>