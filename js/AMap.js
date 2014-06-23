// requrie https://maps.google.com/maps/api/js?sensor=false

/** геокодер
 * https://developers.google.com/maps/documentation/javascript/geocoding
 * https://developers.google.com/maps/documentation/geocoding/?hl=ru#Results
 */
var AGeoCoder = {

    error: {
        notFind: 'Not find address'
    },

    geoData : {
        location: '',
        country_code : '',
        country_name : '',
        city_name : '',
        street_address: '',
        street_number : '',
        lat: '',
        lng: '',
        csrf: ''
    },

    findLocation : {},

    geoCoder : null,

    findData : {},

    options : {},

    getDataByAddress : function(address, options){
        var options = options || { success: function() {}, error: function() {} };
        AGeoCoder.findData = { 'address': address };
        return AGeoCoder.process(options);
    },

    getDataByLatLng : function(latLng, options){
        var options = options || { success: function() {}, error: function() {} };
        AGeoCoder.findData = { 'latLng': latLng };
        return AGeoCoder.process(options);
    },

    process : function(options){

        if(AGeoCoder.geoCoder == null){
            AGeoCoder.geoCoder = new google.maps.Geocoder();
        }

        AGeoCoder.geoCoder.geocode( AGeoCoder.findData, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                AGeoCoder.findLocation = results[0].geometry.location;
                AGeoCoder.setGeoData(results[0]);

                AGeoCoder.process.err = false;

                options.success();
            } else {
                error(AGeoCoder.error.notFind);

                //options.error();

                AGeoCoder.process.err = true;
            }
        });
    },

    setGeoData : function(result){
        //отчистим geoData
        AGeoCoder.geoData.country_code = '';
        AGeoCoder.geoData.country_name = '';
        AGeoCoder.geoData.city_name = '';
        AGeoCoder.geoData.street_address = '';
        AGeoCoder.geoData.street_number = '';

        for(var key in result.address_components){
            var types = result.address_components[key].types;

            if(AGeoCoder.checkType('country', types)){
                AGeoCoder.geoData.country_code = result.address_components[key].short_name;
                AGeoCoder.geoData.country_name = result.address_components[key].long_name;
            }

            if(AGeoCoder.checkType('locality', types)){
                AGeoCoder.geoData.city_name = result.address_components[key].long_name;
            }

            if(AGeoCoder.checkType('route', types)){
                AGeoCoder.geoData.street_address = result.address_components[key].long_name;
            }

            if(AGeoCoder.checkType('street_number', types)){
                AGeoCoder.geoData.street_number = result.address_components[key].long_name;
            }
        }

        AGeoCoder.geoData.lat = result.geometry.location.lat();
        AGeoCoder.geoData.lng = result.geometry.location.lng();

    },

    checkType : function(needle, types){
        return needle == types || ($.isArray(types) && $.inArray(needle, types) != -1);
    },

    getFormattedAddress : function(){
        var addr = '';

        addr += AGeoCoder.geoData.city_name ? AGeoCoder.geoData.city_name : '';
        addr += AGeoCoder.geoData.street_address ? ', ' + AGeoCoder.geoData.street_address : '';
        addr += AGeoCoder.geoData.street_number ? ' ' + AGeoCoder.geoData.street_number : '';
        return addr;
    }
};