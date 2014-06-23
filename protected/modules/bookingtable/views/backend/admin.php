<?php
$this->pageTitle=Yii::app()->name . ' - ' . tt('Booking apartment', 'booking');


$this->menu = array(
	array(),
);
$this->adminTitle = tt('Booking apartment', 'booking');
?>

<?php
if (issetModule('bookingcalendar')) {
	echo "<div class='flash-notice'>".tt('booking_table_to_calendar', 'booking')."</div>";
}
?>

<?php $this->widget('CustomGridView', array(
	'id'=>'admin-booking-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns' => array(
		array(
			'name' => 'id',
			'htmlOptions' => array(
				'class' => 'id_column',
			),
		),
		array(
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnBookingTableStatusHtml($data, "users-booking-grid", 1)',
			'htmlOptions' => array(
				'style' => 'width: 150px;',
				//'class'=>'apartments_status_column',
			),
			'sortable' => false,
			'filter' => Bookingtable::getAllStatuses(),
		),
		array(
			'name' => 'apartment_id',
			'type' => 'raw',
			'value' => '(isset($data->apartment) && $data->apartment->id) ? CHtml::link($data->apartment->id, $data->apartment->getUrl()) : tc("No")',
			'filter' => false,
			'sortable' => false,
		),
		array(
			'name' => 'username',
			'value' => '$data->username',
			'sortable' => false,
		),
		array(
			'name' => 'email',
			'value' => '$data->email',
			'sortable' => false,
		),
		array(
			'name' => 'phone',
			'value' => '$data->phone',
			'sortable' => false,
		),
		array(
			'name' => 'comment',
			'value' => '$data->comment',
			'sortable' => false,
		),
		array(
			'name' => 'date_start',
			'value' => '(isset($data->timein) && $data->time_in) ? $data->date_start . " (". $data->timein->getStrByLang("title").")" : "" ',
			'filter' => false,
			'sortable' => false,
			'htmlOptions' => array('style' => 'width:150px;'),
		),
		array(
			'name' => 'date_end',
			'value' => '(isset($data->timeout) && $data->time_out) ? $data->date_end . " (". $data->timeout->getStrByLang("title").")" : "" ',
			'filter' => false,
			'sortable' => false,
			'htmlOptions' => array('style' => 'width:150px;'),
		),
		array(
			'header' => tt('Creation date', 'booking'),
			'value' => '$data->date_created',
			'type' => 'raw',
			'sortable' => false,
			//'htmlOptions' => array('style' => 'width:130px;'),
		),
	),
));

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('editable_select_booking_table_admin', "
		function ajaxSetBookingTableStatus(elem, id, id_elem, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("bookingtableactivate")."', {
				data   : items,
				type   : 'select',
				cancel : '".tc('Cancel')."',
				submit : '".tc('Ok')."',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
	CClientScript::POS_HEAD);
?>