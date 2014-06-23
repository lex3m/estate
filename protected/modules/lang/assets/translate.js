
function translateField(field, fromLang, isCKEditor, modelName){
	isCKEditor = isCKEditor || 0;

    var fields = new Object();
    for(var key in activeLang){
        var lang = activeLang[key];
        var fieldId = 'id_'+modelName+field+'_'+lang;

        if (isCKEditor == 1) {
			fields[lang] = CKEDITOR.instances[fieldId].getData();
		} else {
			fields[lang] = $('#'+fieldId).val();
		}
        //alert(fieldId+' = '+field[lang]);
    }

    if(!fields[fromLang]){
        error(errorNoFromLang);
        return false;
    }

    var postData = new Object();
    postData['fromLang'] = fromLang;
    postData['fields'] = fields;

    var loaderId = 'span.t_loader_' + modelName + field;
    $(loaderId).show();

    $.ajax({
        type: "POST",
        url: baseUrl+'/lang/main/ajaxTranslate',
        data: postData,
        dataType: 'json',
        success: function(msg){
            if(msg.result == 'ok'){
                message(successTranslate, 'message', 2000);
                for(var lang in msg.fields){
                    var val = msg.fields[lang];
                    fieldId = 'id_'+modelName+field+'_'+lang;
                    if (isCKEditor == 1) {
						CKEDITOR.instances[fieldId].setData(val);
					} else {
						$('#'+fieldId).val(val);
					}
                }
            }else{
                error(errorTranslate);
            }
            $(loaderId).hide();
        },
        error: function(err){
            $(loaderId).hide();
        }
    });
}

function copyField(field, fromLang, isCKEditor, modelName){
	isCKEditor = isCKEditor || 0;

    var fieldId = 'id_'+modelName+field+'_'+fromLang;

    var copyValue = (isCKEditor == 1) ? CKEDITOR.instances[fieldId].getData() : $('#'+fieldId).val() ;

    for(var key in activeLang){
        var lang = activeLang[key];
        fieldId = 'id_'+modelName+field+'_'+lang;

        if(fromLang != lang){
			if (isCKEditor == 1) {
				CKEDITOR.instances[fieldId].setData(copyValue);
			} else {
				$('#'+fieldId).val(copyValue);
			}

        }
    }
    message(successCopy);
}