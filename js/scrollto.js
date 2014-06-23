function getOffset(elem) {
	if (elem.getBoundingClientRect) {
		// "правильный" вариант
		return getOffsetRect(elem)
	} else {
		// пусть работает хоть как-то
		return getOffsetSum(elem)
	}
}

function getOffsetSum(elem) {
	var top=0, left=0
	while(elem) {
		top = top + parseInt(elem.offsetTop)
		left = left + parseInt(elem.offsetLeft)
		elem = elem.offsetParent
	}

	return {top: top, left: left}
}

function getOffsetRect(elem) {
	// (1)
	var box = elem.getBoundingClientRect()

	// (2)
	var body = document.body
	var docElem = document.documentElement

	// (3)
	var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
	var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft

	// (4)
	var clientTop = docElem.clientTop || body.clientTop || 0
	var clientLeft = docElem.clientLeft || body.clientLeft || 0

	// (5)
	var top  = box.top +  scrollTop - clientTop
	var left = box.left + scrollLeft - clientLeft

	return { top: Math.round(top), left: Math.round(left) }
}

function scrollto(id){
	var pos = getOffset(document.getElementById(id));
	window.scrollTo(0, pos.top);
}