<?php
	$this->pageTitle .= ' - '.NewsModule::t('News').' - '.CHtml::encode($model->getStrByLang('title'));
	$this->breadcrumbs=array(
		NewsModule::t('News') => array('/news/main/index'),
		truncateText(CHtml::encode($model->getStrByLang('title')), 10),
	);
?>

<h2><?php echo CHtml::encode($model->getStrByLang('title'));?></h2>
<span class="date"><?php echo NewsModule::t('Created on').' '.$model->dateCreated; ?></span>

<?php if($model->image) : ?>
	<?php $src = $model->image->getFullThumbLink(); ?>
	<?php if($src) : ?>
			<div class="clear"></div>
			<div class="news-image">
				<?php echo CHtml::link(CHtml::image($src, $model->getStrByLang('title')), $model->image->fullHref(), array('class' => 'fancy'));?>
			</div>
		<div class="clear"></div>
	<?php endif; ?>
<?php endif; ?>

<?php
	echo $model->body;
?>
<div class="clear"></div>

<?php if(param('enableCommentsForNews', 1)){ ?>
<div id="comments">
	<?php
		$this->widget('application.modules.comments.components.commentListWidget', array(
			'model' => $model,
			'url' => $model->getUrl(),
			'showRating' => false,
		));
	?>
</div>
<?php } ?>