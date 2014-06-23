<?php

$this->adminTitle = Yii::t('module_comments', 'Update Comment #{id}', array('{id}'=>$model->id));

echo $this->renderPartial('_form', array('model'=>$model));

?>