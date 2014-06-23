<?php Yii::import('application.modules.formdesigner.models.FormDesigner'); ?>
<?php if ($apartments): ?>
	<section style="width:100%; padding-bottom:20px">
		<div style="width:960px; margin:0; padding:10px 0 10px 0px">
			<h2><?php echo tt('Comparison list', 'comparisonList');?></h2>
		</div>

		<table class="table compare">
			<thead class="goods">
				<tr>
					<td>&nbsp;</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<div class="compare-title">
								<?php
								$title = truncateText($item->getStrByLang('title'), 10);

								echo CHtml::link($title, $item->getUrl());
								?>
							</div>

							<div class="compare-photo">
								<div style="position: relative;">
									<div class="compare-delete-icon">
										<?php
											echo CHtml::link(
												'<img src="'.Yii::app()->baseUrl.'/images/delete_22x22.png">',
												Yii::app()->createUrl('/comparisonList/main/del', array('apId' => $item->id)),
												array('title' => tc('Delete'))
											);
										?>
									</div>
									<?php
										$res = Images::getMainThumb(150,100, $item->images);
										$img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
											'title' => $item->getStrByLang('title'),
											'class' => 'apartment_type_img'
										));
										echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title')));
									?>
								</div>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
                <?php if(FormDesigner::isShowForAnything('address')){ ?>
				<tr>
					<td>
						<strong><?php echo tt('Address', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							$adressFull = '';

							if ($item->canShowInView('address')) {
								if (issetModule('location') && param('useLocation', 1)) {
									if($item->locCountry || $item->locRegion || $item->locCity)
										$adressFull = ' ';

									if($item->locCountry){
										$adressFull .= $item->locCountry->getStrByLang('name');
									}
									if($item->locRegion){
										if($item->locCountry)
											$adressFull .=  ',&nbsp;';
										$adressFull .=  $item->locRegion->getStrByLang('name');
									}
									if($item->locCity){
										if($item->locCountry || $item->locRegion)
											$adressFull .=  ',&nbsp;';
										$adressFull .=  $item->locCity->getStrByLang('name');
									}
								} else {
									if(isset($item->city) && isset($item->city->name)){
										$cityName = $item->city->name;
										if($cityName) {
											$adressFull = ' '.$cityName;
										}
									}
								}
								$adress = CHtml::encode($item->getStrByLang('address'));
								if($adress){
									$adressFull .= ', '.$adress;
								}
								echo $adressFull;
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

				<tr>
					<td>
						<strong><?php echo tt('Object type', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php echo utf8_ucfirst($item->objType->name); ?>
						</td>
					<?php endforeach; ?>
				</tr>

				<tr>
					<td>
						<strong><?php echo tt('Price from', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php if ($item->is_price_poa)
								echo tt('is_price_poa', 'apartments');
							else
								echo $item->getPrettyPrice();
							?>
						</td>
					<?php endforeach; ?>
				</tr>

                <?php if(FormDesigner::isShowForAnything('floor_all')){ ?>
                <tr>
					<td>
						<strong><?php echo tc('Floor');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('floor_all')){
								echo $item->floor;
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>

				<tr>
					<td>
						<strong><?php echo tt('Total number of floors', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('floor_all')){
								echo $item->floor_total;
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

                <?php if(FormDesigner::isShowForAnything('site_square')){ ?>
                <tr>
					<td>
						<strong><?php echo tt('Total square', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('square')){
								echo $item->square.' '.tc('site_square');
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

                <?php if(FormDesigner::isShowForAnything('site_land_square')){ ?>
				<tr>
					<td>
						<strong><?php echo tt('Land square', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('land_square')){
								echo $item->land_square.' '.tc('site_land_square');
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

                <?php if(FormDesigner::isShowForAnything('berths')){ ?>
				<tr>
					<td>
						<strong><?php echo tt('Number of berths', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('berths')){
								echo CHtml::encode($item->berths);
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

                <?php if(FormDesigner::isShowForAnything('window_to')){ ?>
				<tr>
					<td>
						<strong><?php echo tt('window to', 'apartments');?>:</strong>
					</td>
					<?php foreach ($apartments as $item) :?>
						<td>
							<?php
							if($item->canShowInView('window_to') && $item->windowTo->getTitle()){
								echo CHtml::encode($item->windowTo->getTitle());
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
                <?php } ?>

				<?php
                    if(FormDesigner::isShowForAnything('references')){
                        $categories = ComparisonList::getRefCategories();
                        if ($categories) {
                            foreach($categories as $category) {
                                echo '<tr>';
                                echo '<td>';
                                echo '<strong>'.CHtml::encode($category->getStrByLang('title')).':</strong>';
                                echo '</td>';
                                foreach ($apartments as $item) {
                                    echo '<td>';
                                    $item->references = $item->getFullInformation($item->id, $item->type, $category->id);
                                    foreach($item->references as $ref) {
                                        echo '<ul class="compare-description-ul">';
                                        if($ref['title']){
                                            foreach($ref['values'] as $key => $value){
                                                if($value){
                                                    echo '<li><span>'.CHtml::encode($value).'</span></li>';
                                                }
                                            }
                                        }
                                        echo '</ul>';
                                    }
                                    echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    }

                    if(issetModule('formeditor')){
                        $rows = FormDesigner::getNewFields();

                        foreach($rows as $row){
                            if(!FormDesigner::isShowForAnything($row['field'])){
                                continue;
                            }
                            echo '<tr>';
                            echo '<td>';
                            echo '<strong>'.CHtml::encode($row['label_'.Yii::app()->language]).':</strong>';
                            echo '</td>';
                            foreach ($apartments as $item) {
                                if($row->type == FormDesigner::TYPE_REFERENCE){
                                    $sql = "SELECT title_".Yii::app()->language." FROM {{apartment_reference_values}} WHERE id=".$item->$row['field'];
                                    $value = Yii::app()->db->createCommand($sql)->queryScalar();
                                } else{
                                    $value = CHtml::encode($item->$row['field']);
                                }

                                if($row->type == FormDesigner::TYPE_INT && $row->measure_unit){
                                    $value .= '&nbsp;' . CHtml::encode($row->measure_unit);
                                }

                                echo '<td>';
                                if($value){
                                    if($item->canShowInView($row['field'])){
                                        echo $value;
                                    }
                                }
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                    }

				?>
			</tbody>
		</table>
	</section>
	<div class="clear"></div>
<?php else:?>

<?php endif;?>

<?php
Yii::app()->clientScript->registerScript('compare-zebra', "
	$('.table tr').not('.head').removeClass('odd').removeClass('int');
	$('.table tr:odd').not('.head').addClass('odd');
	$('.table tr:even').not('.head').addClass('int');
", CClientScript::POS_READY);
?>