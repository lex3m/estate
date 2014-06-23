jQuery(function ($) {
    $("#loading").bind("ajaxSend",function () {
        $(this).show();
    }).bind("ajaxComplete", function () {
            $(this).hide();
        });
    /*
     $("a.fancy").fancybox({
     "ajax":{
     "data": {"isFancy":"true"}
     },
     });
     */
    $('.searchField').live('change', function (event) {
        changeSearch();
    });
});

function focusSubmit(elem) {
    elem.keypress(function (e) {
        if (e.which == 13) {
            $(this).blur();
            $("#btnleft").focus().click();
        }
    });
}

function reloadApartmentList(url) {
    $.ajax({
        type: 'GET',
        url: url,
        data: {is_ajax: 1},
        ajaxStart: UpdatingProcess(resultBlock, updateText),
        success: function (msg) {
            $('div.main-content-wrapper').html(msg);
            $('div.ratingview > span > input').rating({'readOnly': true});

            list.apply();

		// smooth scroll to
		var dest=0;
		if($("#appartment_box").offset().top > $(document).height()-$(window).height()){
			dest=$(document).height()-$(window).height();
		}else{
			dest=$("#appartment_box").offset().top;
		}
		$("html,body").animate({scrollTop:dest}, 500,"swing");


            $('#update_div').remove();
            $('#update_text').remove();
            $('#update_img').remove();
        }
    });
}

function UpdatingProcess(resultBlock, updateText) {
    $('#update_div').remove();
    $('#update_text').remove();
    $('#update_img').remove();

    var opacityBlock = $('#' + resultBlock);

    if (opacityBlock.width() != null) {
        var width = opacityBlock.width();
        var height = opacityBlock.height();
        var left_pos = opacityBlock.offset().left;
        var top_pos = opacityBlock.offset().top;
        $('body').append('<div id=\"update_div\"></div>');

        var cssValues = {
            'z-index': '1005',
            'position': 'absolute',
            'left': left_pos,
            'top': top_pos,
            'width': width,
            'height': height,
            'border': '0px solid #FFFFFF',
            'background-image': 'url(' + bg_img + ')'
        }

        $('#update_div').css(cssValues);

        var left_img = left_pos + width / 2 - 16;
        var left_text = left_pos + width / 2 + 24;
        var top_img = top_pos + height / 2 - 16;
        var top_text = top_img + 8;

        $('body').append("<img id='update_img' src='" + indicator + "' style='position:absolute;z-index:1006; left: " + left_img + "px;top: " + top_img + "px;'>");
        $('body').append("<div id='update_text' style='position:absolute;z-index:6; left: " + left_text + "px;top: " + top_text + "px;'>" + updateText + "</div>");
    }
}

var searchLock = false;

function changeSearch() {
    if (params.change_search_ajax != 1) {
        return false;
    }

    if (!searchLock) {
        searchLock = true;

        $.ajax({
            url: BASE_URL + '/quicksearch/main/mainsearch/countAjax/1',
            data: $('#search-form').serialize(),
            dataType: 'json',
            type: 'get',
            success: function (data) {
                $('#btnleft').html(data.string);
                searchLock = false;
            },
            error: function () {
                searchLock = false;
            }
        })
    }
}

var placemarksYmap = [];

var list = {
    lat: 0,
    lng: 0,

    apply: function () {
        $('div.appartment_item').each(function () {

            var existListMap = $('#list_map_block').attr('exist') == 1;
            if(!existListMap){
                return;
            }

            var item = $(this);

            item.mouseover(function () {

                var ad = $(this);
                var lat = ad.attr('lat') + 0;
                var lng = ad.attr('lng') + 0;
                var id = ad.attr('ap_id');

                if ((list.lat != lat || list.lng != lng) && lat > 0 && lng > 0) {
                    list.lat = lat;
                    list.lng = lng;

                    if (useGoogleMap) {
                        if (typeof infoWindowsGMap !== 'undefined' && typeof infoWindowsGMap[id] !== 'undefined') {
                            for (var key in infoWindowsGMap) {
                                if (key == id) {
                                    infoWindowsGMap[key].open();
                                } else {
                                    infoWindowsGMap[key].close();
                                }
                            }
                            var latLng = new google.maps.LatLng(lat, lng);

                            mapGMap.panTo(latLng);
                            infoWindowsGMap[id].open(mapGMap, markersGMap[id]);
                        }
                    }

                    if (useYandexMap) {
                        if (typeof placemarksYMap[id] !== 'undefined') {
                            placemarksYMap[id].balloon.open();
                        }
                    }

                    if (useOSMap) {
                        if (typeof markersOSMap[id] !== 'undefined') {
                            markersOSMap[id].openPopup();
                            mapOSMap.panTo(new L.LatLng(lat, lng));
                        }
                    }
                }

            });

        });
    }
}

var scriptLoaded = [];

function loadScript(url, reload) {
    reload = reload || true;

    //if(typeof scriptLoaded[url] == 'undefined' || reload){
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = url;
        document.body.appendChild(script);

        scriptLoaded[url] = 1;
    //}
}
