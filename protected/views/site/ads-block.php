<div id="ore-ads-block">
	<div style="margin: 0 auto; width: 960px;">
		<ul>
		<li>
			<?php
				echo CHtml::link(
					Yii::t('module_install', 'About product', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/about-open-real-estate' : 'http://open-real-estate.info/en/about-open-real-estate',
					array (
						'class' => 'button cyan'
					)
				);
			?>
		</li>
		<li>
			<?php
				echo CHtml::link(
					Yii::t('module_install', 'Contact us', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/contact-us' : 'http://open-real-estate.info/en/contact-us',
					array (
						'class' => 'button cyan'
					)
				);
			?>
		</li>
		<li>
			<?php
				echo CHtml::link(
					'<span class="download"></span>'.Yii::t('module_install', 'Download', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/download-open-real-estate' : 'http://open-real-estate.info/en/download-open-real-estate',
					array (
						'class' => 'button green'
					)
				);
			?>
		</li>
	</ul>
	</div>
</div>