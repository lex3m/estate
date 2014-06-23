<div class="gmap-marker">
	<div align="center" class="gmap-marker-adlink">
		<?php echo CHtml::link('<strong>'.tt('ID', 'apartments').': '.$model->id.'</strong>, '.
		Apartment::getNameByType($model->type).', '.CHtml::encode($model->getStrByLang('title')), $model->getUrl()); ?>
	</div>
	<?php
		$res = Images::getMainThumb(150, 100, $model->images);
			?>
				<div align="center" class="gmap-marker-img">
					<?php
						echo CHtml::image($res['thumbUrl'], $model->getStrByLang('title'), array(
							'title' => $model->getStrByLang('title'),
						));
					?>
				</div>
			<?php
	?>
	<div align="center" class="gmap-marker-adress">
		<?php echo CHtml::encode($model->getStrByLang('address')); ?>
	</div>
</div>