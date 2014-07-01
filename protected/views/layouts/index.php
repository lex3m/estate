<?php $this->beginContent('//layouts/main'); ?>


	<div id="homeheader">
		<div class="slider-wrapper theme-default">
			<div id="slider" class="nivoSlider shadow">
				<?php
				$usePaid = false;
				$imgs = array();

				if(issetModule('paidservices')){
					$imgs = PaidServices::getImgForSlider();
					if ($imgs) {
						$usePaid = true;
						foreach($imgs as $img) { ?>
							<a href="<?php echo $img['url'];?>">
								<img src="<?php echo $img['src'];?>" alt="" width="500" height="310" title="<?php echo CHtml::encode($img['title']);?>" />
							</a>
						<?php }
					}
				}

				if(!$usePaid || count($imgs) < 3){
					if(issetModule('slider') && count(Slider::model()->getActiveImages())){
						$this->widget('application.modules.slider.components.SliderWidget', array());
					} else {
						?>
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/1.jpg" alt="1" width="716" height="375" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/2.jpg" alt="2" width="716" height="375" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/3.jpg" alt="3" width="716" height="375" />
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/slider/4.jpg" alt="4" width="716" height="375" />
						<?php
					}
				} ?>
			</div>
        </div>

		<?php
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/slider/themes/default/default.css');
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/slider/nivo-slider.css');

			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/slider/jquery.nivo.slider.pack.js', CClientScript::POS_END);
			Yii::app()->clientScript->registerScript('slider', '
				$("#slider").nivoSlider({effect: "random", randomStart: true});
			', CClientScript::POS_READY);
		?>
		
	</div>

	<?php
		if(issetModule('advertising')) {
			$this->renderPartial('//../modules/advertising/views/advert-top', array());
		}
	?>

	

	    <div class="main-content">
		    <div class="main-content-wrapper homecontent">
			    <?php
				    foreach(Yii::app()->user->getFlashes() as $key => $message) {
					    if ($key=='error' || $key == 'success' || $key == 'notice'){
						    echo "<div class='flash-{$key}'>{$message}</div>";
					   }
				   }
			    ?>
			    <?php echo $content; ?>
		    </div>
			
			<div class="column_right">
			    <a href="#" class="predlosheniya"><img src="/images/narezka/predlosheniya.jpg" alt="Предложения" class="shadow" /></a>
				
				<div class="new-right">
                 <div class="last-news-index">
                    <p class="title"><?php echo tt('News', 'news');?></p>
                    <?php
                    $criteriaNews = new CDbCriteria();
                    $criteriaNews->limit = 3;
                    $criteriaNews->order = 'date_created DESC'; 
                    $newsIndex = News::model()->findAll($criteriaNews);
                    foreach($newsIndex as $news) : ?>
                        <div class="last-news-item">
                            <div class="last-news-date">
                                <p class="ns-label">
                                    <?php
                                    echo '<div class="news_date">'.date("d",strtotime($news->date_created)).'</div>';
                                    $month = date("F",strtotime($news->date_created));
                                    $cut_month = mb_substr(Yii::t('date',$month),0,3, "utf-8");
                                    echo '<div class="cut_month">'.$cut_month.'</div>';?>
                                </p>
                            </div>
                            <div class="last-news-title">
                                <?php echo CHtml::link(truncateText($news->getStrByLang('title'), 8), $news->getUrl());?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
				</div>
				<div class="allnew">
                    <?php echo CHtml::link(tt('All news','news'), 'news/main/index',array('class'=>'btnsrch')); ?>
				</div>
				
				<div class="katalogy shadow">
                    <?php echo CHtml::link(tt('Our catalogs', 'common'), array('publications/main/index'));?>
				</div>
				
		    </div>
	    </div>
			
	
<?php $this->endContent(); ?>
