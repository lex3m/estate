<?php
$userTypes = User::getTypeList('withAll');
$typeName = isset($userTypes[$type]) ? $userTypes[$type] : '?';

$this->pageTitle .= ' - '.tc('Users').': '.$typeName;
$this->breadcrumbs=array(
    tc('Users'),
);
?>

<h1><?php echo tc('Users'); ?></h1>

<?php
$links = array();
$links[] = array('label' => tc('All'), 'url' => Yii::app()->createUrl('/users/main/search', array('type' => 'all')), 'active' => $type == 'all');
//$links[] = array('label' => tc('Private persons'), 'url' => Yii::app()->createUrl('/users/main/search', array('type' => User::TYPE_PRIVATE_PERSON)), 'active' => $type == User::TYPE_PRIVATE_PERSON);
//$links[] = array('label' => tc('Agents'), 'url' => Yii::app()->createUrl('/users/main/search', array('type' => User::TYPE_AGENT)), 'active' => $type == User::TYPE_AGENT);
//$links[] = array('label' => tc('Agency'), 'url' => Yii::app()->createUrl('/users/main/search', array('type' => User::TYPE_AGENCY)), 'active' => $type == User::TYPE_AGENCY);
?>

<div id="userfilter">
<?php
/*$this->widget('zii.widgets.CMenu',array(
    'items'=>$links
));*/
?>
</div>

<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_search_user_item', // представление для одной записи
    'ajaxUpdate'=>false, // отключаем ajax поведение
    'emptyText'=>tc('No user'),
    'summaryText'=>"{start}&mdash;{end} ".tc('of')." {count}",
    'template'=>'{summary} {sorter} {items} {pager}',

    'sortableAttributes'=>array('username', 'date_created'),
    'pager'=>array(
        'class'=>'CLinkPager',
        'header'=>false,
        'htmlOptions'=>array('class'=>'pager'),
    ),
));

	Yii::app()->clientScript->registerScript('generate-phone', '
		function getPhoneNum(elem, id){
			$(elem).closest("span").html(\'<img src="'.Yii::app()->controller->createUrl('/users/main/generatephone').'?id=\' + id + \'" />\');
		}
	', CClientScript::POS_END);


