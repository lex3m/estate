<?php
$this->pageTitle=Yii::app()->name . ' - '.tc('Error');
$this->breadcrumbs=array(
	tc('Error'),
);
?>

<h2><?php echo tc('Error');?> <?php echo CHtml::encode($code); ?></h2>

<div class="error">
	<?php echo CHtml::encode($message); ?>
</div>