<div class="comment">
	<div class="name">
		<?php
			echo CHtml::encode($data->name)
					." (<a href='mailto:".$data->email."'>"
					.$data->email."</a>) "
					.Yii::t('module_comments', 'says about');
			echo ' '.CHtml::link(CHtml::encode($data->apartment->getStrByLang('title')), $data->apartment->getUrl()); ?>
	</div>

	<div class="date">
		<?php if($data->status==Comment::STATUS_PENDING){ ?>
				<span class="pending"><?php echo Yii::t('module_comments', 'Pending approval'); ?></span> |
				<?php
				echo CHtml::link(Yii::t('module_comments', 'Approve'), array('/comments/backend/main/approve','id'=>$data->id));
				echo ' | ';
			}
			echo CHtml::link(Yii::t('module_comments', 'Update'),array('/comments/backend/main/update','id'=>$data->id));
			echo ' | ';
			echo CHtml::linkButton(tc('Delete'),
					array(
						'submit'=>array('/comments/backend/main/delete','id'=>$data->id),
						'confirm'=>tc('Are you sure you want to delete this item?'),
					)
				);
		echo ' | '.$data->dateCreated; ?>
	</div>

	<div class="body">
		<?php echo CHtml::encode($data->body); ?>
	</div>

	<?php
		if($data->rating != -1){
		?>
		<div class="rating">
			<?php $this->widget('CStarRating',array('name'=>'rating'.$data->id, 'id'=>'rating'.$data->id, 'value'=>$data->rating, 'readOnly'=>true)); ?>
		</div>
		<?php
		}
	?>
	<div class="clear"></div>
</div>