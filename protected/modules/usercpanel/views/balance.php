<?php
$this->pageTitle .= ' - '.tc('My balance');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tc('My balance'),
);

$user = HUser::getModel();

echo '<b>' . tc('On the balance') . ': ' . $user->balance . ' ' . Currency::getDefaultCurrencyName() . '</b>';
echo '<br>';

echo CHtml::link(tt('Replenish the balance'), Yii::app()->createUrl('/paidservices/main/index', array('paid_id' => PaidServices::ID_ADD_FUNDS)), array('class' => 'fancy'));