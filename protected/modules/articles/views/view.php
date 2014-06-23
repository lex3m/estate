<?php
$this->pageTitle .= ' - '.tt("FAQ").' - '.$model['page_title'];
$this->breadcrumbs=array(
	tt("FAQ")=>array('index'),
	truncateText(CHtml::encode($model->getStrByLang('page_title')), 10),
);
?>

<h1><?php echo tt("FAQ"); ?></h1>

<?php
	if ($articles) {
		echo '<ul class="apartment-description-ul">';
		foreach ($articles as $article) {
			echo '<li>'.CHtml::link($article['page_title'], $article->getUrl(), array('class'=>'title')).'</li>';
		}
		echo '</ul>';
	}
	if (!empty($model)) {
		?>
		<h2><?php echo $model['page_title'];?></h2>
		<p><?php echo $model['page_body'];?></p>
		<?php
	}


if(param('enableCommentsForFaq', 1)){
	?>
	<div id="comments">
		<?php
			$this->widget('application.modules.comments.components.commentListWidget', array(
				'model' => $model,
				'url' => $model->getUrl(),
				'showRating' => false,
			));
		?>
	</div>
	<?php
}