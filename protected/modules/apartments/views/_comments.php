<?php
foreach($comments as $comment){
?>
	<div class="comment">
		<div class="name">
			<?php echo CHtml::encode($comment->name).' <span class="date">'.$comment->dateCreated.'</span>'; ?>
		</div>
		<?php
			if($comment->rating != -1){
			?>
			<div class="rating">
				<?php $this->widget('CStarRating',
						array(
							'name'=>'rating'.$comment->id,
							'id'=>'rating'.$comment->id,
							'value'=>$comment->rating,
							'readOnly'=>true,
						)
					);
				?>
			</div><br />
			<?php
			}
		?>
		<div class="body">
			<?php echo CHtml::encode($comment->body); ?>
		</div>
	</div>
<?php
}
?>