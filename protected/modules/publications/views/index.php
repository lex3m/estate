<h1>Электронные каталоги</h1>
<?php foreach ($publications as $publication): ?>
 <div class="pubs">
 <table border="0" style="width: 982px; height:">
    <tbody>
    <tr>
        <td align="center"><a target="_blank" href="http://media.mouzenidis-travel.ru/Presentations/cat2014/new_grekodom2014/">
                <!--<img width="143" height="197" alt="" src="/userfiles/articles_files/cat/dom.jpg" style="border: thin solid #bbb;">-->
                <img width="143" height="197" src="<?php echo Yii::app()->request->getBaseUrl(true).'/media/publications/snapshots/'.$publication->snapshot; ?>" style="">
            </a></td>
    </tr>
    <tr>
        <td align="center"><span style="color: #c20018;"><strong><?php echo $publication->name; ?><br></strong></span></td>
    </tr>
    <tr>
        <td align="center"><span style="color: #808080;"><?php echo CHtml::link("Смотреть онлайн", Yii::app()->request->getBaseUrl(true).'/media/publications/docs/'. $publication->document); ?></td>
    </tr>
    <tr>
        <td align="center"><span style="color: #808080;">
                <?php echo CHtml::link("Скачать в PDF", Yii::app()->request->getBaseUrl(true).'/media/publications/docs/'.$publication->document, array('target'=>'_blank','download'=>$publication->document)); ?></span></td>
    </tr>
    </tbody>
</table>
<?php endforeach; ?>
</div>