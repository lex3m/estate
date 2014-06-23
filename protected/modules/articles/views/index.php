<?php
$this->pageTitle .= ' - '.tt("FAQ");
$this->breadcrumbs=array(
	tt("FAQ"),
);
?>

<h1><?php echo tt("FAQ"); ?></h1>
<?php
if ($articles) {
        foreach ($articles as $article) {
	?>
		<div class="articles-index">
			<?php echo CHtml::link($article['page_title'], $article->getUrl(), array('class'=>'title')); ?>
			<p class="desc">
				<?php echo truncateText(
					$article['page_body'],
					param('module_articles_truncateAfterWords', 50),
					CHtml::link(Yii::t('module_articles', 'Read more &raquo;'), $article->getUrl())
				); ?>
			</p>
		</div>
	<?php
	}
}

if ($pages) {
    $this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>
