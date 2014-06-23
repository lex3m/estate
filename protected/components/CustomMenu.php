<?php

	Yii::import('zii.widgets.CMenu');
	class CustomMenu extends CMenu {
		protected function renderMenuItem($item){
			if(isset($item['url'])){
				if(isset($item['linkOptions']['submit'])){
					$item['linkOptions']['csrf'] = true;
				}

				$label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
				return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
			}
			else
				return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
		}
	}