<?php
//echo Yii::app()->request->getBaseUrl(true).'/media/publications/docs/'.$model->document;
/*$this->widget('ext.QPdfJs',array(
    'url'=>Yii::app()->request->getBaseUrl(true).'/media/publications/docs/'.$model->document,
    array('format'=>Files::PDF)));
*/
$this->widget('ext.QPdfJs.QPdfJs',array(
    'url'=>'http://project-8.dev.topsu.ru/media/publications/docs/Middlemarch.pdf',
    array(
      //  'sideBarOpen'=>false,

        // ltr = left to right, rtl=right to left
     //   'direction'=>'ltr',
       // 'format'=>Files::PDF,

    )
));

