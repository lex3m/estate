<?php $baseUrl = Yii::app()->request->baseUrl; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="language" content="en"/>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->pageDescription); ?>"/>
	<meta name="keywords" content="<?php echo CHtml::encode($this->pageKeywords); ?>"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

	<link media="screen, projection" type="text/css" href="<?php echo $baseUrl; ?>/css/admin-styles.css" rel="stylesheet"/>

	<!--[if IE]>
	<link href="<?php echo $baseUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
	<link rel="icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>

	<?php
		Yii::app()->bootstrap->registerAllCss();
		Yii::app()->bootstrap->registerCoreScripts();

		if(param('useYandexMap') == 1) {
			Yii::app()->getClientScript()->registerScriptFile(
				'http://api-maps.yandex.ru/2.0/?load=package.standard,package.clusters&coordorder=longlat&lang=' . CustomYMap::getLangForMap(),
				CClientScript::POS_END);
		}
		elseif(param('useGoogleMap') == 1) {
			//Yii::app()->getClientScript()->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
			//Yii::app()->getClientScript()->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', CClientScript::POS_END);
		}
		elseif(param('useOSMMap') == 1) {
			//Yii::app()->clientScript->registerScriptFile('http://cdn.leafletjs.com/leaflet-0.7/leaflet.js', CClientScript::POS_END);
			//Yii::app()->clientScript->registerCssFile('http://cdn.leafletjs.com/leaflet-0.7/leaflet.css');

			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/leaflet/leaflet-0.7.2/leaflet.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/js/leaflet/leaflet-0.7.2/leaflet.css');

			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/leaflet/leaflet-0.7.2/dist/leaflet.markercluster-src.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.css');
			Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.Default.css');
		}

    $this->renderPartial('//layouts/_common');
	?>
</head>

<body id="top">
<div id="fb-root"></div>

<?php

	if(isFree()) {
		$rightItems = array(
			array('label' => tc('Log out'), 'url' => $baseUrl . '/site/logout'),
		);
	} else {
		$rightItems = array(
			array('label' => tc('Language'), 'url' => '#', 'items' => Lang::getAdminMenuLangs()),
			array('label' => tc('Currency'), 'url' => '#', 'items' => Currency::getActiveCurrencyArray(4)),
			array('label' => tc('Log out'), 'url' => $baseUrl . '/site/logout'),
		);
	}

	$this->widget('bootstrap.widgets.TbNavbar', array(
		'fixed' => 'top',
		'brand' => '<img alt="' . CHtml::encode($this->pageDescription) . '" src="' . Yii::app()->request->baseUrl . '/images/pages/logo-open-re-admin.png" id="logo">',
		'brandUrl' => $baseUrl . '/',
		'collapse' => false, // requires bootstrap-responsive.css
		'items' => array(
			array(
				'class' => 'bootstrap.widgets.TbMenu',
				'items' => array(
					array('label' => tc('Control panel'), 'url' => '#', 'active' => true),
					array('label' => tc('Menu'), 'url' => '#', 'items' => $this->infoPages),
				),
				'encodeLabel' => false,
			),
			//'<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>',

			array(
				'class' => 'bootstrap.widgets.TbMenu',
				'htmlOptions' => array('class' => 'pull-right'),
				'items' => $rightItems,
			),
		),
	));

	$countApartmentModeration = Apartment::getCountModeration();
	$bageListings = ($countApartmentModeration > 0) ? "&nbsp<span class=\"badge\">{$countApartmentModeration}</span>" : '';

	$bagePayments = '';
	if(issetModule('payment')){
		$countPaymentWait = Payments::getCountWait();
		$bagePayments = ($countPaymentWait > 0) ? "&nbsp<span class=\"badge\">{$countPaymentWait}</span>" : '';
	}

	$countCommentPending = Comment::getCountPending();
	$bageComments = ($countCommentPending > 0) ? "&nbsp<span class=\"badge\">{$countCommentPending}</span>" : '';

	$countComplainPending = ApartmentsComplain::getCountPending();
	$bageComplain = ($countComplainPending > 0) ? "&nbsp<span class=\"badge\">{$countComplainPending}</span>" : '';

	$countReviewsPending = Reviews::getCountModeration();
	$bageReviews = ($countReviewsPending > 0) ? "&nbsp<span class=\"badge\">{$countReviewsPending}</span>" : '';

	$bageBooking = '';
	if (issetModule('bookingtable')) {
		$countNewPending = Bookingtable::getCountNew();
		$bageBooking = ($countNewPending > 0) ? "&nbsp<span class=\"badge\">{$countNewPending}</span>" : '';
	}
?>

<div class="bootnavbar-delimiter"></div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">

				<?php
				if(isFree()){
					$this->widget('bootstrap.widgets.TbMenu', array(
						'type' => 'list',
						'encodeLabel' => false,
						'items' => array(
							array('label' => tc('Listings')),
							array('label' => tc('Listings') . $bageListings, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartments/backend/main/admin', 'active' => isActive('apartments')),
							array('label' => tc('List your property'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/apartments/backend/main/create', 'active' => isActive('apartments.create')),
							array('label' => tc('Comments') . $bageComments, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/comments/backend/main/admin', 'active' => isActive('comments')),
							array('label' => tc('Complains') . $bageComplain, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartmentsComplain/backend/main/admin', 'active' => isActive('apartmentsComplain'), 'visible' => issetModule('apartmentsComplain')),
							array('label' => tt('Booking apartment', 'booking') . $bageBooking, 'icon' => 'icon-file', 'url' => $baseUrl . '/bookingtable/backend/main/admin', 'active' => isActive('bookingtable'), 'visible' => issetModule('bookingtable')),

							array('label' => tc('Users')),
							array('label' => tc('Users'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/users/backend/main/admin', 'active' => isActive('users')),
							//array('label' => tt('Add user', 'users'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/users/backend/main/create', 'active' => isActive('users.create')),

							array('label' => tt('Reviews', 'reviews')),
							array('label' => tt('Reviews_management', 'reviews') . $bageReviews, 'icon' => 'icon-file', 'url' => $baseUrl . '/reviews/backend/main/admin', 'active' => isActive('reviews')),
							array('label' => tt('Add_feedback', 'reviews'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/reviews/backend/main/create', 'active' => isActive('reviews.create')),

							array('label' => tc('Content')),
							array('label' => tc('News'), 'icon' => 'icon-file', 'url' => $baseUrl . '/news/backend/main/admin', 'active' => isActive('news')),
							array('label' => tc('Q&As'), 'icon' => 'icon-file', 'url' => $baseUrl . '/articles/backend/main/admin', 'active' => isActive('articles')),
							array('label' => tc('Top menu items'), 'icon' => 'icon-file', 'url' => $baseUrl . '/menumanager/backend/main/admin', 'active' => isActive('menumanager')),
							array('label' => tc('Info pages'), 'icon' => 'icon-file', 'url' => $baseUrl . '/infopages/backend/main/admin', 'active' => isActive('infopages')),
                            array('label' => tc('Publications'), 'icon' => 'icon-file', 'url' => $baseUrl . '/publications/backend/main/admin', 'active' => isActive('publications')),
                            array('label' => tc('Locations'), 'icon' => 'icon-file', 'url' => $baseUrl . '/locations/backend/main/list', 'active' => isActive('locations')),

							array('label' => tc('References')),
							array('label' => tc('Categories of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencecategories/backend/main/admin', 'active' => isActive('referencecategories')),
							array('label' => tc('Values of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencevalues/backend/main/admin', 'active' => isActive('referencevalues')),
							array('label' => tc('Reference "View:"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/windowto/backend/main/admin', 'active' => isActive('windowto')),
							array('label' => tc('Reference "Check-in"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesin/backend/main/admin', 'active' => isActive('timesin')),
							array('label' => tc('Reference "Check-out"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesout/backend/main/admin', 'active' => isActive('timesout')),
							array('label' => tc('Reference "Property types"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentObjType/backend/main/admin', 'active' => isActive('apartmentObjType')),
							array('label' => tc('Reference "City/Cities"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentCity/backend/main/admin', 'active' => isActive('apartmentCity')),

							array('label' => tc('Settings')),
							array('label' => tc('Settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/configuration/backend/main/admin', 'active' => isActive('configuration')),
							array('label' => tc('Manage modules'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/modules/backend/main/admin', 'active' => isActive('modules')),
							array('label' => tc('Images'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/images/backend/main/index', 'active' => isActive('images')),
							array('label' => tc('Change admin password'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/adminpass/backend/main/index', 'active' => isActive('adminpass')),
							array('label' => tc('Site service '), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/service/backend/main/admin', 'active' => isActive('service'), 'visible' => issetModule('service')),
							array('label' => tc('Authentication services'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/socialauth/backend/main/admin', 'active' => isActive('socialauth'), 'visible' => issetModule('socialauth')),
							array(
								'label' => tc('The forms designer'),
								'icon' => 'icon-wrench',
								'url' => $baseUrl . '/formdesigner/backend/main/admin',
								'active' => isActive('formdesigner'),
								'visible' => issetModule('formdesigner')
							),

							array('label' => tc('Languages and currency')),
							array('label' => tc('Translations'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/translateMessage/backend/main/admin', 'active' => isActive('translateMessage')),

							array('label' => tc('Other')),
							array('label' => tc('News about Open Real Estate CMS'), 'icon' => 'icon-home', 'url' => $baseUrl . '/news/backend/main/product', 'active' => isActive('news.product')),
						),
					));
				} else {
						$this->widget('bootstrap.widgets.TbMenu', array(
						'type' => 'list',
						'encodeLabel' => false,
						'items' => array(
							array('label' => tc('Listings')),
							array('label' => tc('Listings') . $bageListings, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartments/backend/main/admin', 'active' => isActive('apartments')),
							array('label' => tc('List your property'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/apartments/backend/main/create', 'active' => isActive('apartments.create')),
							array('label' => tc('Comments') . $bageComments, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/comments/backend/main/admin', 'active' => isActive('comments')),
							array('label' => tc('Complains') . $bageComplain, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartmentsComplain/backend/main/admin', 'active' => isActive('apartmentsComplain'), 'visible' => issetModule('apartmentsComplain')),
                            array('label' => tt('Booking apartment', 'booking') . $bageBooking, 'icon' => 'icon-file', 'url' => $baseUrl . '/bookingtable/backend/main/admin', 'active' => isActive('bookingtable'), 'visible' => issetModule('bookingtable')),

							array('label' => tc('Users')),
							array('label' => tc('Users'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/users/backend/main/admin', 'active' => isActive('users')),
							//array('label' => tt('Add user', 'users'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/users/backend/main/create', 'active' => isActive('users.create')),

							array('label' => tt('Reviews', 'reviews')),
							array('label' => tt('Reviews_management', 'reviews') . $bageReviews, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/reviews/backend/main/admin', 'active' => isActive('reviews')),
							array('label' => tt('Add_feedback', 'reviews'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/reviews/backend/main/create', 'active' => isActive('reviews.create')),

							array('label' => tc('Content')),
							array('label' => tc('News'), 'icon' => 'icon-file', 'url' => $baseUrl . '/news/backend/main/admin', 'active' => isActive('news')),
							array('label' => tc('Q&As'), 'icon' => 'icon-file', 'url' => $baseUrl . '/articles/backend/main/admin', 'active' => isActive('articles')),
							array('label' => tc('Top menu items'), 'icon' => 'icon-file', 'url' => $baseUrl . '/menumanager/backend/main/admin', 'active' => isActive('menumanager')),
							array('label' => tc('Info pages'), 'icon' => 'icon-file', 'url' => $baseUrl . '/infopages/backend/main/admin', 'active' => isActive('infopages')),

							array('label' => tc('Payments'), 'visible' => issetModule('payment')),
							array('label' => tc('Paid services'), 'icon' => 'icon-shopping-cart', 'url' => $baseUrl . '/paidservices/backend/main/admin', 'active' => isActive('paidservices'), 'visible' => issetModule('payment')),
							array('label' => tc('Manage payments') . $bagePayments, 'icon' => 'icon-shopping-cart', 'url' => $baseUrl . '/payment/backend/main/admin', 'active' => isActive('payment'), 'visible' => issetModule('payment')),
							array('label' => tc('Payment systems'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/payment/backend/paysystem/admin', 'active' => isActive('payment.paysystem'), 'visible' => issetModule('payment')),

							array('label' => tc('References')),
							array('label' => tc('Categories of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencecategories/backend/main/admin', 'active' => isActive('referencecategories')),
							array('label' => tc('Values of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencevalues/backend/main/admin', 'active' => isActive('referencevalues')),
							array('label' => tc('Reference "View:"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/windowto/backend/main/admin', 'active' => isActive('windowto')),
							array('label' => tc('Reference "Check-in"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesin/backend/main/admin', 'active' => isActive('timesin')),
							array('label' => tc('Reference "Check-out"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesout/backend/main/admin', 'active' => isActive('timesout')),
							array('label' => tc('Reference "Property types"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentObjType/backend/main/admin', 'active' => isActive('apartmentObjType')),
							array('label' => tc('Reference "City/Cities"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentCity/backend/main/admin', 'active' => isActive('apartmentCity'), 'visible' => !(issetModule('location') && param('useLocation', 1))),

							array('label' => tc('Settings')),
							array('label' => tc('Settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/configuration/backend/main/admin', 'active' => isActive('configuration')),
							array('label' => tc('Manage modules'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/modules/backend/main/admin', 'active' => isActive('modules')),
							array('label' => tc('Images'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/images/backend/main/index', 'active' => isActive('images')),
							array('label' => tc('Change admin password'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/adminpass/backend/main/index', 'active' => isActive('adminpass')),
							array('label' => tc('Seo settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/seo/backend/main/admin', 'active' => isActive('seo'), 'visible' => issetModule('seo')),
							array('label' => tc('Site service '), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/service/backend/main/admin', 'active' => isActive('service'), 'visible' => issetModule('service')),
							array('label' => tc('Authentication services'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/socialauth/backend/main/admin', 'active' => isActive('socialauth'), 'visible' => issetModule('socialauth')),

							array('label' => tc('Languages and currency')),
							array('label' => tc('Languages'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/lang/backend/main/admin', 'active' => isActive('lang')),
							array('label' => tc('Translations'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/translateMessage/backend/main/admin', 'active' => isActive('translateMessage')),
							array('label' => tc('Currencies'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/currency/backend/main/admin', 'active' => isActive('currency')),

							array('label' => tc('Modules'), 'visible' => (issetModule('slider')) || issetModule('advertising') || issetModule('iecsv') || issetModule('formdesigner') || issetModule('socialposting')),
							array('label' => tc('Mail editor'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/notifier/backend/main/admin', 'active' => isActive('notifier'), 'visible' => issetModule('notifier')),
							array('label' => tc('Slide-show on the Home page'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/slider/backend/main/admin', 'active' => isActive('slider'), 'visible' => issetModule('slider')),
							array('label' => tc('Import / Export'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/iecsv/backend/main/admin', 'active' => isActive('iecsv')),
							array('label' => tc('Advertising banners'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/advertising/backend/advert/admin', 'active' => isActive('advertising'), 'visible' => issetModule('advertising')),
							array('label' => tc('The forms designer'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/formdesigner/backend/main/admin', 'active' => isActive('formdesigner'), 'visible' => issetModule('formdesigner')),
							array('label' => tt('Services of automatic posting', 'socialposting'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/socialposting/backend/main/admin', 'active' => isActive('socialposting'), 'visible' => issetModule('socialposting')),

							array('label' => tc('Location module'), 'visible' => (issetModule('location') && param('useLocation', 1))),
							array('label' => tc('Countries'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/country/admin', 'visible' => (issetModule('location') && param('useLocation', 1)), 'active' => isActive('location.country')),
							array('label' => tc('Regions'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/region/admin', 'visible' => (issetModule('location') && param('useLocation', 1)), 'active' => isActive('location.region')),
							array('label' => tc('Cities'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/city/admin', 'visible' => (issetModule('location') && param('useLocation', 1)), 'active' => isActive('location.city')),

							array('label' => tc('Other')),
							array('label' => tc('News about Open Real Estate CMS'), 'icon' => 'icon-home', 'url' => $baseUrl . '/news/backend/main/product', 'active' => isActive('news.product')),
						),
					));
				}

				?>
            </div>
            <!--/.well -->
        </div>
        <!--/span-->
        <div class="span9">
			<?php echo $content; ?>
        </div>
        <!--/span-->
    </div>
    <!--/row-->

    <hr>



    <div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
	<?php
	Yii::app()->clientScript->registerCoreScript('jquery');
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/adminCommon.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/habra_alert.js', CClientScript::POS_END);

	$this->widget('application.modules.fancybox.EFancyBox', array(
			'target' => 'a.fancy',
			'config' => array(
				'ajax' => array('data' => "isFancy=true"),
				'titlePosition' => 'inside',
			),
		)
	);
	?>
</div>
<!--/.fluid-container-->

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'temp_modal')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3 id="temp_modal_title">&nbsp;&nbsp;</h3>
</div>
<div class="modal-body">
	<div id="temp_modal_content"><?php echo tc('Loading content...');?></div>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
	var tempModal = {
		setContent: function(content){
			$('#temp_modal_content').html(content);
		},
		open: function(){
            $("#temp_modal").modal("show");
		},
		close: function(){
            tempModal.setTitle('');
            tempModal.setContent('<?php echo tc('Loading content...');?>');
            $("#temp_modal").modal("hide");
		},
		init: function(){
            $('a.tempModal').each(function(el){
                var objUrl = $(this).attr('href');
                if(objUrl != ''){
                    $(this).on('click', function(event){
                        $('#temp_modal_content').load(objUrl);
                        var title = $(this).attr('title');

						if(title){
							tempModal.setTitle(title);
						}
                        tempModal.open();
						event.preventDefault();
                    })
                }
            });
        },
		setTitle: function(title){
            $('#temp_modal_title').html(title);
		}
	}

	$(function(){
		tempModal.init();
	});
</script>

</body>
</html>
