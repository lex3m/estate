<?php
if(!param('useGoogleMap')){
    Yii::app()->getClientScript()->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
}
Yii::app()->clientScript->registerScriptFile( Yii::app()->baseUrl. '/js/AMap.js' );
Yii::app()->clientScript->registerCssFile( Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css' );

Yii::app()->clientScript->registerScript('redirectType', "
    $(document).ready(function() {
        var BASE_URL = ".CJavaScript::encode(Yii::app()->baseUrl).";

        $('#obj_type, #ap_type').live('change', function() {
            $('#update_overlay').show();
            $('#is_update').val(1);
            $('#Apartment-form').submit();
            return false;
        });
	});
	",
    CClientScript::POS_END, array(), true);

?>

<div class="form">
    <div id="update_overlay"><p><?php echo tc('Loading content...');?></p></div>

	<?php
	$ajaxValidation = false;

	if(!$model->isNewRecord){
		$htmlOptions = array('enctype' => 'multipart/form-data');
	}
	else{
		$htmlOptions = array();
	}?>

	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'Apartment-form',
		'enableAjaxValidation'=>$ajaxValidation,
		'htmlOptions'=> $htmlOptions,
	));
	?>

	<?php echo $form->errorSummary($model); ?>

	<div id="tabs">
		<ul>
			<li><a href="#tab-main"><?php echo tc('General'); ?></a></li>
			<li><a href="#tab-extended"><?php echo tc('Addition'); ?></a></li>

			<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
				<li><a href="#tab-images"><?php echo tc('Photos for listing'); ?></a></li>
			<?php endif; ?>

			<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) :?>
            	<li><a href="#tab-panorama" data-toggle="tab"><?php echo tc('Panorama'); ?></a></li>
				<li><a href="#tab-videos" data-toggle="tab"><?php echo tc('Videos for listing'); ?></a></li>
			<?php endif; ?>

			<?php
			if(!$model->isNewRecord  && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))
				&& $model->type != Apartment::TYPE_BUY
				&& $model->type != Apartment::TYPE_RENTING
			){
				?>
				<li>
					<a href="#tab-map" id="map-tab-link" onclick="reInitMap();">
						<?php echo tc('Map'); ?>
					</a>
				</li>
				<?php } ?>
        </ul>

		<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
			<div class="tab-pane" id="tab-images">
				<?php
				$this->widget('application.modules.images.components.AdminImagesWidget', array(
					'objectId' => $model->id,
				));
				?>
			</div>
		<?php endif; ?>

		<?php if($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
			<div class="tab-pane" id="tab-panorama">
				<?php
					$this->renderPartial('_tab_panorama_edit', array(
						'model' => $model,
						'form' => $form,
					));
				?>
			</div>


        <div class="tab-pane" id="tab-videos">
            <div class="flash-notice"><?php echo tc('You can upload a video or code.'); ?></div>
			<?php
			if($model->video){
				$videoFileExists = false;

				$videoHtml = array();
				$count = 0;

				foreach($model->video as $video){
					if($video->isFile()){
						if($video->isFileExists()){
							$videoFileExists = true;
							echo '<div class="video-file-block">';
							echo '<a class="view-video-file" href="'.$video->getFileUrl().'" id="player-'.$video->id.'"></a>';
							echo '</div>';

							Yii::app()->clientScript->registerScript('player-'.$video->id.'', '
									flowplayer("player-'.$video->id.'", "'.Yii::app()->request->baseUrl."/js/flowplayer/flowplayer-3.2.16.swf".'",
									{
										clip: {
											autoPlay: false
										},
									});
								', CClientScript::POS_READY);
							echo '<div>'.CHtml::button(tc('Delete'), array(
								'onclick' => 'document.location.href="'.Yii::app()->controller->createUrl('deletevideo', array('id' => $video->id, 'apId' => $model->id)).'";'
							)).'</div>';
						}
					}
					if($video->isHtml()){
						echo '<div class="video-html-block" id="video-block-html-'.$count.'"></div>';
						echo '<div>'.CHtml::button(tc('Delete'), array(
							'onclick' => 'document.location.href="'.Yii::app()->controller->createUrl('deletevideo', array('id' => $video->id, 'apId' => $model->id)).'";'
						)).'</div><br/>';
						$videoHtml[$count] = CHtml::decode($video->video_html);
						$count++;
					}
				}
				if($videoFileExists){
					Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/flowplayer/flowplayer-3.2.12.min.js', CClientScript::POS_END);
				}
				$script = '';
				if($videoHtml){
					foreach($videoHtml as $key => $value){
						$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
					}
				}
				if($script){
					Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
				}
			}
			?>

			<?php
				if($model->video){
					echo '<div>'.CHtml::button(tc('Add'), array(
						'onclick' => '$(".add-video").toggle();',
					)).'</div><br/>';

					Yii::app()->clientScript->registerScript('hide-add', '
							$(".add-video").hide();
						', CClientScript::POS_READY);
				}
			?>

            <div class="add-video">
                <div class="rowold">
					<?php echo $form->labelEx($model,'video_html'); ?>
					<?php echo $form->textArea($model,'video_html',array('class'=>'width500 height100')); ?>
                    <br />
					<?php echo $form->error($model,'video_html'); ?>
                </div>

                <div class="rowold">
					<?php echo $form->labelEx($model,'video_file'); ?>
					<?php echo $form->fileField($model, 'video_file'); ?>
                    <div class="padding-bottom10">
						<span class="label label-info">
							<?php echo Yii::t('module_apartments', 'Supported file: {supportExt}.', array('{supportExt}' => $supportvideoext));?>
						</span>
                        <br />
						<span class="label label-info">
							<?php echo Yii::t('module_apartments', 'videoMaxSite: {size}.', array('{size}' => formatBytes($supportvideomaxsize)));?>
						</span>
                    </div>
					<?php echo $form->error($model,'video_file'); ?>
                </div>
            </div>
        </div>
		<?php endif; ?>

		<div id="tab-map">
			<?php
			if(!$model->isNewRecord  && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))
				&& $model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) {

				if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
					echo '<div class="flash-notice">'.tc('Click on the map to set the location of an object or move an existing marker.').'</div>';
				}

				if (param('useGoogleMap', 1)) : ?>
					<div class="clear">&nbsp;</div>
					<div id="gmap">
						<?php echo $this->actionGmap($model->id, $model); ?>
					</div>
					<div class="clear">&nbsp;</div>
				<?php elseif (param('useYandexMap', 1)) : ?>
					<!--<div id="ymap">-->
					<?php echo $this->actionYmap($model->id, $model); ?>
					<!--</div>-->
				<?php elseif (param('useOSMMap', 1)) : ?>
					<div class="clear">&nbsp;</div>
					<div id="osmap">
						<?php echo $this->actionOSmap($model->id, $model); ?>
					</div>
					<div class="clear">&nbsp;</div>
				<?php endif; ?>


				<div class="search_by_address">
					<?php
					echo CHtml::textField('address_for_map', '', array('class' => 'width300'));
					echo CHtml::button(tc('Set a marker by address'), array('onclick' => "findByAddress(); return false;"));
					?>
				</div>

			<?php } ?>
		</div>

		<?php
		$this->renderPartial('//../modules/apartments/views/backend/__form',array(
			'model' => $model,
			'form' => $form,
		));?>

	</div>

	<?php $this->endWidget(); ?>
	<!-- form -->
</div>

<?php
if(issetModule('paidservices')){
	echo '<div class="current_paid">';
	echo '<h6>'.tc('Paid services').'</h6>';
	echo $model->getPaidHtml(true);
	echo '</div>';
}
?>

<?php
if (issetModule('seo') && !$model->isNewRecord && $model->active != Apartment::STATUS_DRAFT) {
	echo '<br>';
	$this->widget('application.modules.seo.components.SeoWidget', array(
		'model' => $model,
	));
} ?>

<?php

	//Yii::app()->clientScript->registerCssFile( Yii::app()->request->baseUrl.'/css/ui/jquery-ui-1.8.16.custom.css', 'screen' );
	Yii::app()->clientScript->registerScript('init-tabs', '$( "#tabs" ).tabs();', CClientScript::POS_READY);

// reInit google map (for preventing incorrect work in hidden tab)
Yii::app()->clientScript->registerScript('gmap-init', '
		var useYandexMap = '.param('useYandexMap', 1).';
		var useGoogleMap = '.param('useGoogleMap', 1).';
		var useOSMap = '.param('useOSMMap', 1).';

		var lang = "'.Yii::app()->language.'";

		function addAddressString(string){
            if(typeof string == "undefined" || string.length == 0){
                return "";
            }

            var sep = address.length > 0 ? ", " : "";

            return sep + string;
        }

		function reInitMap(){
			address = "";

		    if($("#UserAds_city_id").length){
		        address += addAddressString($("#UserAds_city_id option:selected").html());
		    } else {
		        address += addAddressString($("#ap_country option:selected").html());
		        address += addAddressString($("#ap_city option:selected").html());
		    }

			address += addAddressString($("#id_UserAdsaddress_"+lang).val());

			$("#address_for_map").val(address);

			// place code to end of queue
			if(useGoogleMap){
				setTimeout(function(){
					var tmpGmapCenter = mapGMap.getCenter();

					google.maps.event.trigger($("#googleMap")[0], "resize");
					mapGMap.setCenter(tmpGmapCenter);
				}, 0);
			}

			if(useYandexMap){
				setTimeout(function(){
					ymaps.ready(function () {
						globalYMap.container.fitToViewport();
						globalYMap.setCenter(globalYMap.getCenter());
					});
				}, 0);
			}

			if(useOSMap){
				setTimeout(function(){
					L.Util.requestAnimFrame(mapOSMap.invalidateSize,mapOSMap,!1,mapOSMap._container);
				}, 0);
			}
		}

		function findByAddress(){
			var address = $("#address_for_map").val();
			if(!address){
				error("'.tc('Please enter address').'");
				return false;
			}
			AGeoCoder.getDataByAddress(address, {
				success: function(){
					if(useGoogleMap && typeof markersGMap['.$model->id.'] !== "undefined" && typeof mapGMap !== "undefined"){
						var latLng = new google.maps.LatLng(AGeoCoder.geoData.lat, AGeoCoder.geoData.lng);
						markersGMap['.$model->id.'].setPosition(latLng);
						mapGMap.setCenter(latLng);
					}
					if(useYandexMap && typeof placemark !== "undefined" && typeof globalYMap !== "undefined"){
						placemark.geometry.setCoordinates([AGeoCoder.geoData.lng, AGeoCoder.geoData.lat]);
						globalYMap.setCenter([AGeoCoder.geoData.lng, AGeoCoder.geoData.lat]);
					}
					if(useOSMap && typeof markersOSMap['.$model->id.'] !== "undefined" && typeof mapOSMap !== "undefined"){
						var newLatLng = new L.LatLng(AGeoCoder.geoData.lat, AGeoCoder.geoData.lng);
						markersOSMap['.$model->id.'].setLatLng(newLatLng);
						mapOSMap.setView(newLatLng);
					}
					setMarker(AGeoCoder.geoData.lat, AGeoCoder.geoData.lng);
				}
			});
		}

		function setMarker(lat, lng){
			$.ajax({
				type:"POST",
				url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
				data:({lat: lat, lng: lng}),
				cache:false
			})
		}
	', CClientScript::POS_END);

	Yii::app()->clientScript->registerScript('show-special', '
		// price poa
		if($("#'.$this->modelName.'_is_price_poa").is(":checked")){
			$("#price_fields").hide();
		}
		$("#'.$this->modelName.'_is_price_poa").bind("change", function(){
			if($(this).is(":checked")){
				$("#price_fields").hide();
			} else {
				$("#price_fields").show();
			}
		});
	', CClientScript::POS_READY);
