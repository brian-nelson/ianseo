function getWinHeight() {
	// tutti ma non IE
	if( typeof( window.innerHeight ) == 'number' ) return window.innerHeight;
	// IE 6+ in 'standards compliant mode'
	if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) return document.documentElement.clientHeight;
	// IE 4 (per completezza)
	if (document.body && ( document.body.clientWidth || document.body.clientHeight )) return document.body.clientHeight;
	return 0;
}

function getWinWidth() {
	// tutti ma non IE
	if( typeof( window.innerWidth ) == 'number' ) return window.innerWidth;
	// IE 6+ in 'standards compliant mode'
	if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) return document.documentElement.clientWidth;
	// IE 4 (per completezza)
	if (document.body && ( document.body.clientWidth || document.body.clientHeight )) return document.body.clientWidth;
	return 0;
}

function getObjectHeight(obj) {
	// tutti ma non IE
	if( typeof( obj.innerHeight ) == 'number' ) return obj.innerHeight;
	// IE
	return obj.clientHeight;
}

function getObjectWidth(obj) {
	// tutti ma non IE
	if( typeof( obj.innerWidth ) == 'number' ) return obj.innerWidth;
	// IE
	return obj.clientWidth;
}

function resize(obj) {
	var rap=ConHeight/obj.height;
	if(obj.width * rap > ConWidth) rap=ConWidth/obj.width;
	var w = obj.width*rap;
	var h = obj.height*rap;
	obj.width=w;
	obj.height=h;
}

function init() {
	var Content=document.getElementById('Content');
	altezza = getObjectHeight(Content);
	ConWidth=getWinWidth() - 6;
	ConHeight=getWinHeight() - 6;
	
//	document.getElementById('Content').style.height=ConHeight+"px";
//	document.getElementById('Content').style.overflow="hidden";

	scroller=window.setInterval("start_scroll()", 10);
}

function start_scroll() {
	if(TimeCounter-- > 0) return;
	StartingPoint = StartingPoint + PixelScroll;
	if(StartingPoint>altezza) {
		// reload page
		window.location.assign(NextRule);
		StartingPoint=0;
	}
	
	window.scrollBy(0, PixelScroll);
}

