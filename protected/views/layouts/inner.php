<?php

$this->beginContent('//layouts/main');

 /*if($this->showSearchForm){
   $this->renderPartial('//site/inner-search');
} */

if(issetModule('advertising')) {
    $this->renderPartial('//../modules/advertising/views/advert-top', array());
}
?>

	<div class="main-content">
		<div class="main-content-wrapper innercontent">

			<?php if(isset($this->breadcrumbs) && $this->breadcrumbs):?>
				<div class="clear"></div>
					<?php
						$this->widget('zii.widgets.CBreadcrumbs', array(
							'links'=>$this->breadcrumbs,
							'separator' => ' &#8594; ',
						));
					?>
				<div class="clear"></div>
			<?php endif?>

			<?php
				foreach(Yii::app()->user->getFlashes() as $key => $message) {
					if ($key=='error' || $key == 'success' || $key == 'notice'){
						echo "<div class='flash-{$key}'>{$message}</div>";
					}
				}
			?>
			<?php echo $content; ?>
		</div>
	</div>
<?php $this->endContent(); ?>