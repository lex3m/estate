<?php if (isset($newsIndex) && $newsIndex) : ?>
	<div class="clear"></div>
	<!--<div class="last-news-index">
		<p class="title"><?php echo tt('News', 'news');?></p>
		<?php foreach($newsIndex as $news) : ?>
			<div class="last-news-item">
				<div class="last-news-date">
					<p class="ns-label">
						<?php echo $news->dateCreatedLong;?>
					</p>
				</div>
				<div class="last-news-title">
					<?php echo CHtml::link(truncateText($news->getStrByLang('title'), 8), $news->getUrl());?>
				</div>
			</div>
		<?php endforeach;?>
	</div>-->
	<div class="clear"></div>
<?php endif;?>

<?php
if($page){
	if (isset($page->page)) {

		if($page->page->body){
			echo $page->page->body;   
		}

		if ($page->page->widget){
			echo '<div class="clear"></div><div>';
			Yii::import('application.modules.'.$page->page->widget.'.components.*');
			if($page->page->widget == 'contactform'){
				$this->widget('ContactformWidget', array('page' => 'index'));
			} else {
				//$this->widget(ucfirst($page->page->widget).'Widget');
                $this->widget('LastEstateObjects',array());
			} 
			echo '</div>';
		}
	}
}