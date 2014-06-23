
(function($){

    function compareObject(ob1,ob2){
        if((ob1 && !ob2) || (!ob1 && ob2)) return false;
        for(var i in ob1){
            if(typeof(ob2[i])=='undefined' || ob1[i]!=ob2[i]) return false;
        }
        return true;
    }

    $.fn.fpopover=function(options){
        var $this=$(this);
        var rnd=Math.random()*10e+16;

        var settings={
            id: 'fpopover',
            width: 'auto',
            title: '',
            content: ''
        };

        if(options)
            $.extend(settings,options);

        var titleId=settings.id+'_title',closeId=settings.id+'_close',bodyId=settings.id+'_body',footerId=settings.id+'_footer';
        var popover=$("#"+settings.id);
        var $title=$("#"+titleId);
        var $close=$("#"+closeId);
        var $body=$("#"+bodyId);
        var $footer=$("#"+footerId);

        var init=function(){
            popover=$('\
                <div class="popover fade top in" id="'+settings.id+'">\
                    <div class="arrow"></div>\
                    <div class="popover-inner" style="width: '+(settings.width==='auto'?'auto':(settings.width+'px'))+'; min-width: 300px">\
                        <h3 class="popover-title"><a class="close">×</a></h3>\
                        <div class="popover-content"></div>\
                    </div>\
                </div>\
            ');
            var popoverContent=popover.find('.popover-content');
            $close=popover.find('a.close').attr('id',closeId);
            $title=$('<span></span>').html(settings.title).attr('id',titleId).appendTo(popover.find('.popover-title'));
            $body=$('<div></div>').html(settings.content).attr('id',bodyId).appendTo(popoverContent);
            $footer=$('<div class="t-right" style="margin-top: 10px;"></div>').attr('id',footerId).appendTo(popoverContent);

            $close.on('click',close);

            popover.insertBefore($('body'));
            $.data(popover[0],'popover',0);
        }

        var open=function(){
            var pHeight=popover.outerHeight();
            var pWidth=popover.outerWidth();
            var eHeight=$this.outerHeight();
            var eWidth=$this.outerWidth();
            var pos=$this.offset();

            popover.css({
                top: pos.top - pHeight,
                left: pos.left + eWidth / 2 - pWidth / 2
            }).show();

            $.data($this[0],'popover',rnd);
            $.data(popover[0],'popover',rnd);
        }
        var close=function(){
            popover.hide();
            if($this.length)
                $.removeData($this[0],'popover');
        }
        var toggle=function(){
            if($.data($this[0],'popover')===$.data(popover[0],'popover') && !popover.is(':hidden'))
                close();
            else
                open();
        }

        var setTitle=function(title){
            $title.html(title);
        }

        var setContent=function(content){
            $body.html(content);
            return this;
        }

        var notice=function(contentText,title,cbackOk){
            if(typeof contentText!='undefined') setContent(contentText);
            if(typeof title!='undefined') setTitle(title);
            if(typeof cbackOk!='function') cbackOk=function(){};

            $footer.empty().append($('<button class="btn">Ок</button>').on('click',cbackOk));
            return this;
        }

        var confirm=function(contentText,title,cbackYes,closeYes,cbackNo,closeNo,yesText,noText){
            if(typeof contentText!='undefined') setContent(contentText);
            if(typeof title!='undefined') setTitle(title);
            if(typeof closeYes=='undefined') closeYes=true;
            if(typeof closeNo=='undefined') closeNo=true;
            if(typeof cbackYes!='function') cbackYes=function(){};
            if(typeof cbackNo!='function') cbackNo=function(){};

            var yesBtn=$('<button class="btn" style="margin-right: 10px">'+(yesText?yesText:"Да")+'</button>').on('click',cbackYes);
            if(closeYes) yesBtn.on('click',close);

            var noBtn=$('<button class="btn">'+(noText?noText:"Нет")+'</button>').on('click',cbackNo);
            if(closeNo) noBtn.on('click',close);

            $close.on('click',cbackNo);

            $footer
                .empty()
                .append(yesBtn)
                .append(noBtn);

            return this;
        }

        if(popover.length===0) init();

        return{
            "open": open,
            "close": close,
            "toggle": toggle,
            "setTitle": setTitle,
            "setContent": setContent,
            "notice": notice,
            "confirm": confirm
        };
    }

    $.fn.dialogPlugin=function(options){
        var dialog=null;
        var id='dialogPlugin'+Math.floor(Math.random()*100000000);
        var ajaxURL=null,ajaxParams=null;
        var additionalParams={};
        var $title, $body, $footer, $fakeFooter;

        var initialized=false;

        var settings={
            keyboard: false,
            backdrop: 'static',
            content: '',
            autowidth: false,
            css: {}
        };

        if(options)
            $.extend(settings,options);

        dialog=$('\
                <div class="modal" id="'+id+'"> \
                    <div class="modal-header"> \
                        <button class="close" data-dismiss="modal">×</button> \
                    </div> \
                </div> \
            ')
            .css(settings.css)
            .css('display','none')
            .insertBefore($('body'));

        $title=$('<h3 id="'+id+'_title">&nbsp;</h3>').appendTo($("#"+id).children('.modal-header'));
        $body=$('<div class="modal-body" id="'+id+'_body">'+settings.content+'</div>').appendTo($("#"+id));
        $footer=$('<div class="modal-footer" id="'+id+'_footer"></div>').appendTo($("#"+id));
        $fakeFooter=$('<div class="modal-footer" id="'+id+'_fakefooter" style="display: none"><button class="btn" data-loading-text="загрузка..."><span class="ico16 progress-bg"></span></button></div>').appendTo($("#"+id));

        var getId=function(){return id;}

        var setParam=function(paramName,val){
            additionalParams[paramName]=val;
        }
        var setParams=function(params){
            $.extend(true,additionalParams,params);
        }
        var getParam=function(paramName){
            return additionalParams[paramName];
        }
        var getParams=function(){
            return additionalParams;
        }
        var delParam=function(paramName){
            delete additionalParams[paramName];
        }

        var open=function(){
            dialog.modal(settings);
            if(settings.autowidth)
                autowidth();
            initialized=true;
            return this;
        }

        var isInitialized=function(){
            return initialized;
        }

        var centering=function(){
            $("#"+id).parent().position({
                my: "center",
                at: "center",
                of: window
            });
        }

        var autowidth=function(){
            dialog.css({
                width: 'auto',
                'margin-left': function () {
                    return -($(this).width() / 2);
                }
            });
        }

        var close=function(){
            dialog.modal('hide');
            return this;
        }

        var block=function(){
            $footer.hide();
            $fakeFooter.show();

            dialog.find('.modal-header>.close').hide();
            return this;
        }

        var unblock=function(){
            $fakeFooter.hide();
            $footer.show();

            dialog.find('.modal-header>.close').show();
            return this;
        }

        var setContent=function(content){
            $body.html(content);
            return this;
        }

        var setContentAjax=function(url,params,openIfLoad,cache,loadEvent){
            var self=this;
            if(cache && ajaxURL==url && compareObject(params,ajaxParams)){
                if(loadEvent){loadEvent()}
                if(openIfLoad) self.open();
            }else{
                ajaxURL=url;
                ajaxParams=$.extend(true, {}, params);
                self.block();
                $.post(
                    url,
                    params,
                    function (r) {
                        self.setContent(r);
                        if(loadEvent){loadEvent()}
                        if(openIfLoad) self.open();
                    }
                )
                .complete(function(){self.unblock();});
            }

            return self;
        }

        var setTitle=function(title){
            $title.html(title);
            return this;
        }

        var notice=function(contentText,title,cbackOk,cbackClose){
            if(contentText!=undefined) setContent(contentText);
            if(title!=undefined) setTitle(title);
            if(typeof cbackOk!='function') cbackOk=function(){};
            if(typeof cbackClose!='function') cbackClose=function(){};
            
            dialog.on('hidden',cbackClose);

            $footer.empty().append($('<button data-dismiss="modal" class="btn">Ок</button>').bind('click',cbackOk));
            return this;
        }

        var confirm=function(contentText,title,cbackYes,closeYes,cbackNo,closeNo,yesText,noText){
            if(contentText!=undefined) setContent(contentText);
            if(title!=undefined) setTitle(title);
            if(closeYes==undefined) closeYes=true;
            if(closeNo==undefined) closeNo=true;
            if(typeof cbackYes!='function') cbackYes=function(){};
            if(typeof cbackNo!='function') cbackNo=function(){};

            var yesBtn=$('<button class="btn">'+(yesText?yesText:"Да")+'</button>').bind('click',cbackYes);
            if(closeYes) yesBtn.attr('data-dismiss','modal');

            var noBtn=$('<button class="btn">'+(noText?noText:"Нет")+'</button>');
            if(closeNo) noBtn.attr('data-dismiss','modal');

            dialog.on('hidden',cbackNo);

            $footer
                .empty()
                .append(yesBtn)
                .append(noBtn);

            return this;
        }

        return{
            "getId": getId,
            "isInitialized": isInitialized,
            "setParam": setParam,
            "setParams": setParams,
            "getParam": getParam,
            "getParams": getParams,
            "delParam": delParam,
            "open": open,
            "centering": centering,
            "autowidth": autowidth,
            "close": close,
            "block": block,
            "unblock": unblock,
            "setTitle": setTitle,
            "setContent": setContent,
            "setContentAjax": setContentAjax,
            "notice": notice,
            "confirm": confirm
        };
    }

})(jQuery);
