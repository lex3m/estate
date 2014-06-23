<?php
	$this->pageTitle = Yii::t('module_comments','Leave a Comment');

	$this->renderPartial('_commentForm', array('model' => $model));