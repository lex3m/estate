<?php
foreach ($news as $item) : ?>
    <div class="news-items">
        <p>
            <span class="date"><?php echo $item->dateCreated; ?></span>
        </p>

		<?php if($item->image):?>
			<?php $src = $item->image->getSmallThumbLink(); ?>
			<?php if($src) : ?>
				<div class="news-image-list">
					<?php echo CHtml::link(CHtml::image($src, $item->getStrByLang('title')), $item->image->fullHref(), array('class' => 'fancy')); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<p>
			<span class="title"><?php echo CHtml::link(CHtml::encode($item->getStrByLang('title')), $item->getUrl()); ?></span>
		</p>
		<?php
			echo $item->getAnnounce();
		?>
        <p class="dalee">
            <?php echo CHtml::link(tt('Read more &raquo;', 'news'), $item->getUrl()); ?>
        </p>
        <div class="clear"></div>
    </div>
<?php endforeach; ?>

<?php

if(!$news){
	echo tt('News list is empty.', 'news');
}

if($pages){
	$this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>
