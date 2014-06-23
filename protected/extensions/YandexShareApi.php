<?php

/**
 *  Виджет ysc.yandex.YandexShareApi
 *
 *  Позволяет отрисовать кнопку для публикации контента в различных социальных сетях
 *  Подробности http://api.yandex.ru/share/
 *
 *  @author Opeykin A. <aopeykin@gmail.com>
 *  @link   http://allframeworks.ru/
 *  @version 0.0.1
 *  @since ysc 0.0.2
 *  @package Yii Social Components (YSC)
 *  @subpackage yandex
 *  @example В коде представления <?php $this->widget('application.components.ysc.yandex.YandexShareApi');?>
 *
 */

class YandexShareApi extends CWidget
{
    public $type;
    public $services;
	public $language;
    private $_validTypes = array('button','link');
    private $_validServices = array('yazakladki','myspace','moikrug','linkedin','juick','greader','gbuzz','delicious','evernote','digg','blogger','yaru','vkontakte','facebook','twitter','odnoklassniki','moimir','friendfeed','lj');

    public function init()
    {
         $this->type = in_array($this->type,$this->_validTypes) ?  $this->type : 'button';

         $data = array();

		 if(!$this->language){
			 $this->language = Yii::app()->language;
			 if($this->language == 'de'){
				 $this->language = 'en';
			 }
		 }

         if( !$this->services ||  $this->services === 'all' || $this->services === '*')
         {
            $this->services = $this->_validServices;
         }

         if(is_string($this->services))
         {
             $this->services = explode(',',$this->services);
         }

         if(count($this->services))
         {
            foreach($this->services as $service)
            {
                if(in_array($service,$this->_validServices))
                {
                    array_push($data,$service);
                }
            }
         }

         $this->services = implode(',',$this->services);

    }

    public function renderContent()
    {
        //echo '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>';
		Yii::app()->getClientScript()->registerScriptFile('//yandex.st/share/share.js', CClientScript::POS_END);
		echo '<div class="yashare-auto-init" data-yashareL10n="'.$this->language.'" data-yashareType="'.$this->type.'" data-yashareQuickServices="'.$this->services.'"></div>';
    }

	public function run()
    {
        $this->renderContent();
    }

}