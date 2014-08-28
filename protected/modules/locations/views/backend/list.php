Список регионов:
<ol>
    <?php foreach ($regions as $region) {
      $link =  Yii::app()->createUrl('locations/backend/main/edit/',array('id'=>$region->id));
    echo '<li><a href="'.$link.'">'.$region->name.'</a></li>';
     } ?>
</ol>