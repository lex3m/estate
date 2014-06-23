<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/usercpanel.css');

$this->beginContent('//layouts/inner');
?>

    <div class="usercpanel-left floatleft">
        <div id="usermenu">
            <?php
            $this->widget('zii.widgets.CMenu', array(
                'items' => HUser::getMenu(),
                'htmlOptions' => array(
                    'id' => 'navlist',
                ),
            ));
            ?>
        </div>
    </div>

    <div class="usercpanel-right floatleft">
        <?php echo $content; ?>
    </div>

<?php $this->endContent(); ?>