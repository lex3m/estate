/**
	* HabraAlert 0.2
	* author DeerUA
	* version 0.2.0 01.12.2009
	* license as-is PL
	* include <script type="text/javascript" scr="alert.js"></script> after <body> or before, as u wish
	
    message("all good"); (5 сек.)
    error("attention"); (7 сек.)
    warning("something wrong"); (10 сек.)

	*/

initHA = function() {

	var is_ie6 = (window.external && typeof window.XMLHttpRequest == "undefined");
	var styles = "div#messages{position:fixed;top:50px;right:0px;width:250px;margin:0px;padding:7px;background:transparent;z-index:2}"+
	"div#messages div{cursor: pointer;color:#fff;padding:7px;margin-bottom:7px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;opacity:0.65;background:#888;font: normal 12px 'Georgia'}"+
	"div#messages div.error{background:#98001b}	div#messages div.message{background:#0d8529}div#messages div.warning{background:#dd6; color:#333}";
	var iestyles = "body{position:relative}div#messages{position:absolute; -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=65)'; filter: alpha(opacity=65)}div#messages div{cursor: hand}";

	addLoadEvent = function(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != 'function') { window.onload = func;} 
	  else {window.onload = function() { if (oldonload) {oldonload();}func();}}
	}
	
	import_style = function(src){ 
		if ((src == null || src == undefined)) return;
		var imprt = document.createElement('style');
		imprt.setAttribute("type", "text/css");
		if (imprt.styleSheet) imprt.styleSheet.cssText = src;
		else imprt.appendChild(document.createTextNode(src));
		document.getElementsByTagName('head')[0].appendChild(imprt);
	}
	
	addAll = function() {
		var messageBox = document.createElement ('div');
		messageBox.id = "messages";
		if (document.body.firstChild) document.body.insertBefore(messageBox, document.body.firstChild);
		else document.body.appendChild(messageBox);
		import_style(styles);
		if (is_ie6) import_style(iestyles);
	}	
	
	
	if (document.body == null) return addLoadEvent(function() {addAll();}); 
	addAll();	
}

initHA();
message = function (mtext, mtype, howlong) {

	var mtype = mtype || 'message';
	var howlong = howlong || 5000;

	if (document.getElementById('messages') == null) {
		setTimeout(function(){message (mtext, mtype, howlong)}, 100);
		return;
	}

	var alarm = document.createElement ('div');
	alarm.className = mtype;
	alarm.innerHTML = mtext;
	
	alarm.onclick = function () {
		alarm.style.display = "none";
	};

	alarm.del = function () {
		document.getElementById('messages').removeChild (alarm);
	};
	
	document.getElementById('messages').appendChild (alarm);
	setTimeout (alarm.del, howlong);
}

error = function (mtext, howlong) {
	var howlong = howlong || 10000;
	message(mtext,"error",howlong);
}

warning = function (mtext, howlong) {
	var howlong = howlong || 7000;
	message(mtext,"warning",howlong);
}