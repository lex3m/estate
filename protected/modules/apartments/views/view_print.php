<div>
<?php
echo '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
echo '<td>';
echo '<img src="' . Yii::app()->getBaseUrl(true) . '/images/pages/logo-open-ore.png" />';
echo '</td>';
echo '<td align="right">';
$this->widget('application.extensions.qrcode.QRCodeGenerator', array(
	'data' => $model->URL,
	'filename' => 'listing_' . $model->id . '-' . Yii::app()->language . '.png',
	'matrixPointSize' => 3,
	'fileUrl' => Yii::app()->getBaseUrl(true) . '/uploads',
));
echo '</td>';
echo '</tr></table>';
?>

<h1>
    <?php echo CHtml::encode($model->getStrByLang('title')); ?>
</h1>

<div>
<div>
<?php
if ($model->is_special_offer) {
	?>
	<div>
		<?php
		echo '<h2>' . Yii::t('common', 'Special offer!') . '</h2>';

		if ($model->is_free_from != '0000-00-00' && $model->is_free_to != '0000-00-00') {
			echo '<div>';
			echo '<strong>' . Yii::t('common', 'Is avaliable') . '</strong>';
			if ($model->is_free_from != '0000-00-00') {
				echo ' ' . Yii::t('common', 'from');
				echo ' ' . Booking::getDate($model->is_free_from);
			}
			if ($model->is_free_to != '0000-00-00') {
				echo ' ' . Yii::t('common', 'to');
				echo ' ' . Booking::getDate($model->is_free_to);
			}
			echo '</div>';
		}
		?>
	</div>
<?php
}
?>

<div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="320px" valign="top">
				<?php

				$res = Images::getMainThumb(300, 200, $model->images);
				echo CHtml::image($res['thumbUrl'], $model->getStrByLang('title'), array(
					'title' => $model->getStrByLang('title'),
				));
				?>
			</td>
			<td valign="top">
				<?php
				echo '<div>';
				echo '<strong>' . tt('Type', 'apartments') . '</strong>: ' . Apartment::getNameByType($model->type);
				echo '</div>';
				?>
				<?php if (param('useShowUserInfo')) {
					if (isset($model->user->phone) && $model->user->phone) {
						echo '<div>';
						echo '<strong>' . tt('Owner phone', 'apartments') . '</strong>: <img src="' . Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $model->id, 'width' => 180)) . '" style="vertical-align: text-top;"/>';
						echo '</div>';
					}
					$additionalInfo = 'additional_info_' . Yii::app()->language;
					if (isset($model->user->$additionalInfo) && !empty($model->user->$additionalInfo)) {
						echo '<div>';
						echo '<strong>' . tc('Additional info') . '</strong>: ' . CHtml::encode($model->user->$additionalInfo);
						echo '</div>';
					}
				} ?>
				<?php
				echo '<div>';
				echo '<strong>' . tt('Apartment ID', 'apartments') . '</strong>: ' . $model->id;
				echo '</div>';

				echo '<div>';
				echo '<strong>';
				echo utf8_ucfirst($model->objType->name);
				if ($model->stationsTitle() && $model->num_of_rooms) {
					echo ',&nbsp;';
					echo '' . Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms near {metro} metro station', array($model->num_of_rooms, '{metro}' => $model->stationsTitle()));
				} elseif ($model->num_of_rooms) {
					echo ',&nbsp;';
					echo Yii::t('module_apartments',
						'{n} bedroom|{n} bedrooms|{n} bedrooms', array($model->num_of_rooms));
				}


				if (issetModule('location') && param('useLocation', 1)) {
					if ($model->locCountry || $model->locRegion || $model->locCity) {
						echo "<br>";
					}

					if ($model->locCountry) {
						echo $model->locCountry->getStrByLang('name');
					}
					if ($model->locRegion) {
						if ($model->locCountry) {
							echo ',&nbsp;';
						}
						echo $model->locRegion->getStrByLang('name');
					}
					if ($model->locCity) {
						if ($model->locCountry || $model->locRegion) {
							echo ',&nbsp;';
						}
						echo $model->locCity->getStrByLang('name');
					}
				} else {
					if (isset($model->city) && isset($model->city->name)) {
						echo ',&nbsp;';
						echo $model->city->name;
					}
				}

				echo '</strong>';
				echo '</div>';
				echo '<p></p>';
				echo '<div>';
				if (($model->floor && $model->floor_total) || $model->square || $model->land_square || $model->berths || ($model->windowTo && $model->windowTo->getTitle())) {
					echo '<div></div>';
					$echo = array();
					if ($model->canShowInView('floor_all') && $model->floor && $model->floor_total) {
						$echo[] = Yii::t('module_apartments', '{n} floor of {total} total', array($model->floor, '{total}' => $model->floor_total));
					}
					if ($model->canShowInView('square')) {
						$echo[] = Yii::t('module_apartments', 'total square: {n}', $model->square) . ' ' . tc('site_square');
					}
					if ($model->canShowInView('land_square')) {
						$echo[] = Yii::t('module_apartments', 'land square: {n}', $model->land_square) . ' ' . tc('site_land_square');
					}
					if ($model->canShowInView('berths')) {
						$echo[] = Yii::t('module_apartments', 'berths') . ': ' . CHtml::encode($model->berths);
					}
					if ($model->canShowInView('windowTo') && $model->windowTo->getTitle()) {
						$echo[] = tt('window to', 'apartments') . ' ' . CHtml::encode($model->windowTo->getTitle());
					}
					echo implode(', ', $echo);
					unset($echo);
				}
				echo '</div>';
				echo '<p></p>';
				echo '<div>';
				if ($model->is_price_poa) {
					echo '<strong>' . tt('is_price_poa', 'apartments') . '</strong>';
				} else {
					echo '<strong>' . tt('Price from', 'apartments') . '</strong>: ' . $model->getPrettyPrice();
				}
				echo '</div>';
				?>
			</td>
		</tr>
	</table>
</div>

<?php
$images = Images::getObjectThumbs(150, 100, $model->images);

if ($images) {
	$countArr = count($images);
	$i = 1;

	if ($countArr) {
		echo '<div>';
			echo '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
			foreach ($images as $value) {
				$index = $i % 7;
				$k = $i + 1;
				$indexNext = ($i + 1) % 7;

				if ($index == 0 || $i == 1) {
					echo '<tr>';
				}
				echo '<td style="height: 105px;">';
					echo CHtml::image($value['thumbUrl'], '', array('style' => 'width: 150px; height: 100px;'));
				echo '</td>';
				if ($indexNext == 0 || $countArr == $i) {
					echo '</tr>';
				}

				$i++;
			}
			echo '</table>';
		echo '</div>';
	}
}

?>
<div>
	<?php
	if ($model->canShowInView('description')) {
		echo '<div>';
		echo '<strong>' . tt('Description', 'apartments') . '</strong>: ' . CHtml::encode($model->getStrByLang('description'));
		echo '</div>';
	}

	if ($model->canShowInView('description_near')) {
		echo '<div>';
		echo '<strong>' . tt('Near', 'apartments') . '</strong>: ' . CHtml::encode($model->getStrByLang('description_near'));
		echo '</div>';
	}

	if ($model->getStrByLang('address')) {

		$adressFull = '';

		if (issetModule('location') && param('useLocation', 1)) {
			if ($model->locCountry || $model->locRegion || $model->locCity) {
				$adressFull = ' ';
			}

			if ($model->locCountry) {
				$adressFull .= $model->locCountry->getStrByLang('name');
			}
			if ($model->locRegion) {
				if ($model->locCountry) {
					$adressFull .= ',&nbsp;';
				}
				$adressFull .= $model->locRegion->getStrByLang('name');
			}
			if ($model->locCity) {
				if ($model->locCountry || $model->locRegion) {
					$adressFull .= ',&nbsp;';
				}
				$adressFull .= $model->locCity->getStrByLang('name');
			}
		} else {
			if (isset($model->city) && isset($model->city->name)) {
				$cityName = $model->city->name;
				if ($cityName) {
					$adressFull = ' ' . $cityName;
				}
			}
		}

		if ($model->canShowInView('address')) {
			$adressFull .= ', ' . CHtml::encode($model->getStrByLang('address'));
		}

		if ($adressFull) {
			echo '<div><strong>' . tt('Address', 'apartments') . ':</strong> ' . $adressFull . '</div>';
		}
	}
	?>
</div>
<br>

<div>
	<?php
	$model->references = $model->getFullInformation($model->id, $model->type);

	if ($model->canShowInView('references')) {

		$prev = '';
		$column1 = 0;
		$column2 = 0;
		$column3 = 0;
		$i = 0;
		$count = count($model->references);

		$width = array('column1' => '100%', 'column2' => '50%', 'column3' => '33%');

		echo '<table cellpadding="0" cellspacing="0" width="100%"><tr>';

		foreach ($model->references as $item) {
			if ($item['title']) {
				if ($prev != $item['style'] && $prev != '') {
					$column2 = 0;
					$column3 = 0;
					echo '</tr></table><br><table cellpadding="0" cellspacing="0" width="100%"><tr>';
				}
				$$item['style']++;
				$prev = $item['style'];
				echo '<td valign="top" width="' . $width[$item['style']] . '">';
				echo '<span><strong>' . CHtml::encode($item['title']) . '</strong></span>';
				echo '<ul>';
				foreach ($item['values'] as $key => $value) {
					if ($value) {
						echo '<li><span>' . CHtml::encode($value) . '</span></li>';
					}
				}
				echo '</ul>';
				echo '</td>';
				$i++;
				if (($item['style'] == 'column2' && $column2 == 2) || ($item['style'] == 'column3' && $column3 == 3) || $item['style'] == 'column1') {
					echo '</tr></table><br>';
					if ($i != $count) {
						echo '<table cellpadding="0" cellspacing="0" width="100%"><tr>';
					}
				} else {
					if ($i == $count) {
						echo '</tr></table><br>';
					}
				}

			}
		}
	}
	?>
</div>
</div>
</div>
<div>
	<p>&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name) . ', ' . date('Y'); ?></p>
</div>
</div>
