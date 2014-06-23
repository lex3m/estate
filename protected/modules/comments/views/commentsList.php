<?php
	if(count($comments) > 0):?>
		<?php foreach($comments as $comment):?>

		<div class="comment" id="comment-<?php echo $comment->id; ?>">
			<div class="name">
				<?php
					if($comment->owner_id){
						echo '<strong>'.CHtml::encode($comment->username).'</strong>';
					} else {
						echo CHtml::encode($comment->user_name);
					}
					echo ' &nbsp; <span class="date">'.$comment->dateCreated.'</span>';
					echo ' &nbsp; '.CHtml::link(Yii::t('module_comments', 'Reply'), '#comment-form', array('class' => 'comment-reply-link', 'comment-id' => $comment->id));
					if($comment->owner_id && $comment->owner_id == Yii::app()->user->id || Yii::app()->user->hasState('isAdmin')){
						echo ' &nbsp; '.CHtml::link(tc('Delete'), '#', array('class' => 'comment-delete-link', 'comment-id' => $comment->id));
					}
				?>
			</div>
			<?php
				if($this->showRating && $comment->rating != -1){
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
			<div class="body" id="body-comment-<?php echo $comment->id;?>">
				<?php
					if($comment->status == Comment::STATUS_APPROVED){
						echo CHtml::encode($comment->body);
					}
				?>
			</div>
			<?php
				if(count($comment->childs) > 0){
					$this->render('commentsList', array('comments' => $comment->childs));
				}
			?>
		</div>
		<?php endforeach;?>
	<?php else:?>
    <p><?php echo Yii::t('module_comments', 'There are no comments');?></p>
	<?php endif;

	Yii::app()->clientScript->registerScript('comment-manipulate', '
		$(".comment-reply-link").fancybox({
			onClosed : function(){
				$("#comment-form .rating").show();
				$("#CommentForm_rel").val(0);
			},
			onStart: function(links, index){
				var self = $(links[index]);
				$("#comment-form .rating").hide();
				$("#CommentForm_rel").val(self.attr("comment-id"));
			}
		});

		$(".comment-delete-link").on("click", function(event){
			var id = $(this).attr("comment-id");
			event.preventDefault();
			$.ajax({
				type: "POST",
				data: {id: id},
				url: "'.Yii::app()->controller->createUrl('/comments/main/deleteComment').'",
				success: function(msg){
					var result = $.parseJSON(msg);
					if(result.status == 1){
						$("#comment-" + id + " > .comment").insertAfter("#comment-" + id);
						$("#comment-" + id).remove();
					} else {
						alert(result.message);
					}
				}
			});
		});
	', CClientScript::POS_READY);
