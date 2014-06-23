<?php
$this->pageTitle .= ' - '.ReviewsModule::t('Reviews');
$this->breadcrumbs=array(
	ReviewsModule::t('Reviews'),
);
?>
<h1><?php echo ReviewsModule::t('Reviews'); ?></h1>

<div class="add-review-link">
	<?php echo CHtml::link(tt('Add_feedback', 'reviews'), Yii::app()->createUrl('/reviews/main/add'), array('class' => 'apt_btn fancy'));?>
</div>

<?php
if ($reviews) : ?>
	<div id="reviews-wrap">
		<ol class="reviewslist">
			<?php foreach ($reviews as $review) :?>
				<li class="review thread-even depth-1" id="li-review-<?php echo $review->id;?>">
					<div id="review-<?php echo $review->id;?>" class="review-body clearfix">
						<img alt='' src='http://0.gravatar.com/avatar/4f64c9f81bb0d4ee969aaf7b4a5a6f40?s=35&amp;d=&amp;r=G' class='avatar avatar-35 photo' height='35' width='35' />
						<div class="review-author vcard"><?php echo CHtml::encode($review->name); ?></div>
						<div class="review-meta reviewmetadata">
							<span class="review-date"><?php echo $review->dateCreatedFormat; ?></span>
						</div>
						<div class="review-inner">
							<p><?php echo CHtml::encode($review->body); ?></p>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
<?php endif; ?>

<?php if(!$reviews) : ?>
	<div><?php echo tt('Review list is empty');?></div>
<?php endif; ?>

<div class="add-review-link">
	<?php echo CHtml::link(tt('Add_feedback', 'reviews'), Yii::app()->createUrl('/reviews/main/add'), array('class' => 'apt_btn fancy'));?>
</div>

<?php
if($pages){
	$this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>