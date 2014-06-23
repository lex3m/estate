<?php
	if($this->images){
        ?>
		<div class="images-area">
			<?php
				foreach($this->images as $image){

					if($this->withMain && $image['is_main'] || !$this->withMain && !$image['is_main'] || !$image['is_main']){
						?>
						<div class="image-item" id="image_<?php echo $image['id']; ?>">
                            <div class="image-drag-area"></div>
                        	<div class="image-link-item">
								<?php
									$imgTag = CHtml::image(Images::getThumbUrl($image, 150, 100), Images::getAlt($image));
									echo CHtml::link($imgTag, Images::getFullSizeUrl($image), array(
										'class' => 'fancy',
										'rel' => 'gallery',
										'title' => Images::getAlt($image),
									));
								?><br/>
								<span class="setAsMain" link-id="<?php echo $image['id']; ?>">
									<?php
										if($image['is_main']){
											echo tc('Main photo');
										} else {
											echo '<a class="setAsMainLink" href="#">'.tc('Set as main photo').'</a>';
										}
										?>
								</span><br/>
                                <a class="deleteImageLink" link-id="<?php echo $image['id']; ?>" href="#"><?php echo tc('Delete');?></a><br/>
							</div>
                            <div class="image-comment-input">
								<?php
									echo tt('Comment', 'images').': <br />';
									echo CHtml::textArea('photo_comment['.$image['id'].']', $image['comment']);
								?>
                            </div>
						</div>
						<?php
					}
				}
			?>
			<div class="clear"></div>
		</div>
		<?php
	}
