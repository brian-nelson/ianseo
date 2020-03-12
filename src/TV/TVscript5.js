var canvas;
var ctx;
var data='';
var DOMURL = window.URL || window.webkitURL || window;
var XMLMatches = CreateXMLHttpRequestObject();
var img;
var vx=0;
var W;
var H;

function startShow() {

	canvas = document.getElementById('canvas');
	canvas.width=document.documentElement.clientWidth;
	canvas.height=document.documentElement.clientHeight;
	W = canvas.width;
	H = canvas.height;
	ctx    = canvas.getContext('2d');
//	data   = '<svg xmlns="http://www.w3.org/2000/svg" width="'+document.documentElement.clientWidth+'px" height="'+document.documentElement.clientHeight+'px">' +
//	'<foreignObject width="100%" height="100%">' +
//	'<div xmlns="http://www.w3.org/1999/xhtml" style="font-size:40px;background-color:gray;width:'+document.documentElement.clientWidth+'px">' +
//	'<em>I</em> like <span style="color:white; text-shadow:0 0 2px blue;">cheese</span>' +
//	'</div>' +
//	'</foreignObject>' +
//	'</svg>';
//	
//	var img = new Image();
//	var svg = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
//	var url = DOMURL.createObjectURL(svg);
//	
//	img.onload = function () {
//		ctx.drawImage(img, 0, 0);
//		DOMURL.revokeObjectURL(url);
//	};
//	
//	img.src = url;
	
	GetNewContent();
}

function GetNewContent() {
	if (!XMLMatches) return;
	try {
		XMLMatches.open("POST","GetNewContent.php?Quadro=2&Rule=" + RuleId + "&Tour=" + TourId + "&Segment=2&Event=RW&output=svg", true);
		XMLMatches.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		XMLMatches.onreadystatechange=function() {
			if (XMLMatches.readyState!=XHS_COMPLETE) return;
			if (XMLMatches.status!=200) return;
			try {
				data=XMLMatches.responseText ;
				var doc = document.implementation.createHTMLDocument("");
				doc.write(data);
				var table=doc.querySelector('table');
				var tab2=doc.querySelector('div');

				// You must manually set the xmlns if you intend to immediately serialize the HTML
				// document to a string as opposed to appending it to a <foreignObject> in the DOM
//				doc.documentElement.setAttribute("xmlns", doc.documentElement.namespaceURI);

				// Get well-formed markup
				data = (new XMLSerializer).serializeToString(doc.querySelector('body'));
				
				data = '<svg xmlns="http://www.w3.org/2000/svg" width="'+document.documentElement.clientWidth+'px" >' +
				'<foreignObject width="100%" height="100%">' + data + '</foreignObject></svg>';
				
				document.getElementById('DivDebug').innerHTML=data;
				
//				img = new Image();
//				img.onload = function () {
//					ctx.drawImage(img, 0, 0);
//					DOMURL.revokeObjectURL(url);
//				};
//
//				var svg = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
//				var url = DOMURL.createObjectURL(svg);
//				
//				canvas.width=canvas.width;
//				img.src = url;
				
				
			} catch(e) {}
		};
		XMLMatches.send(null);
	} catch (e) { }
}

//window.requestAnimationFrame = function() {
//	return window.requestAnimationFrame ||
//		window.webkitRequestAnimationFrame ||
//		window.mozRequestAnimationFrame ||
//		window.msRequestAnimationFrame ||
//		window.oRequestAnimationFrame ||
//		function(f) {
//			window.setTimeout(f,1000/60);
//		};
//}();
//
//(function renderGame() {
//	window.requestAnimationFrame(renderGame);
//	
//	ctx.clearRect(0, 0, W, H);
//	
//	ctx.fillStyle = '#333';
//	ctx.fillRect(0, 0, W, H);
//	
//	ctx.drawImage(img,0, vx);
//	ctx.drawImage(img,0, img.height-Math.abs(vx));
//	
//	if (Math.abs(vx) > img.height) {
//		vx = 0;
//	}
//	
//	vx -= document.documentElement.clientHeight/350;
//}());