/*
													- Fun_AJAX.js -
	Contiene le funzioni ajax che riguardano la speaker view 
*/
var t;
var OldFlags='';
var timers = new Array();
var oldPhoto='';
var oldRaise='';
var oldBgCol='';

function GetFlags(obj) {
	clearTimeout(t);
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("GET","IanseoAwards.php?Tour="+TourCode,true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var tbody=document.getElementById('PopupContent');
						var XMLResp=XMLHttp.responseXML;
						if (!XMLResp || !XMLResp.documentElement)
							throw(XMLResp.responseText);
						
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") 
						{
							throw("");
						}

						XMLRoot = XMLResp.documentElement;

						var event = XMLRoot.getElementsByTagName('header').item(0).firstChild.data;
						
						var gFlag = XMLRoot.getElementsByTagName('gsvg').item(0).firstChild.data;
						var sFlag = XMLRoot.getElementsByTagName('ssvg').item(0).firstChild.data;
						var bFlag = XMLRoot.getElementsByTagName('bsvg').item(0).firstChild.data;
						if(gFlag=='') {
							gFlag = XMLRoot.getElementsByTagName('gflag').item(0).firstChild.data;
						}
						if(sFlag=='') {
							sFlag = XMLRoot.getElementsByTagName('sflag').item(0).firstChild.data;
						}
						if(bFlag=='') {
							bFlag = XMLRoot.getElementsByTagName('bflag').item(0).firstChild.data;
						}
						
						var Photo = XMLRoot.getElementsByTagName('backphoto').item(0).firstChild.data;
						var Raise = XMLRoot.getElementsByTagName('raiseflags').item(0).firstChild.data;
						var BgCol = XMLRoot.getElementsByTagName('backcolor').item(0).firstChild.data;
						
						if(oldPhoto!=Photo) {
							document.getElementById('FlagContent').style.backgroundImage="url('"+Photo+"')";
							document.getElementById('FlagContent').style.backgroundPosition="center center";
							document.getElementById('FlagContent').style.backgroundRepeat="no-repeat";
							document.getElementById('FlagContent').style.backgroundSize="cover";
							oldPhoto=Photo;
							oldBgCol='';
						}
						if(oldRaise!=Raise) {
							if(Raise!='0') {
								raiseFlag();
							} else if(oldRaise=='1') {
								location.reload();
								return;
							}
							oldRaise=Raise;
						}
						if(Photo=='' && oldBgCol!=BgCol) {
							document.getElementById('FlagContent').style.backgroundColor=BgCol;
							oldBgCol=BgCol;
						}
						
						if (OldFlags != event+gFlag+sFlag+bFlag) {
							if(OldFlags) {
								location.reload();
								return;
							}
							document.getElementById('flagG').style.visibility = "visible";
							document.getElementById('flagS').style.visibility = "visible";
							document.getElementById('flagB').style.visibility = "visible";
							
							for(i=0;i<timers.length;i++) {
								var remT = timers.shift();
								clearTimeout(remT);
							}
							flG.src = gFlag;
					 		flS.src = sFlag;
							flB.src = bFlag;

							setFlagBehaviour(flG, 'flagG');
							setFlagBehaviour(flB, 'flagB');
							setFlagBehaviour(flS, 'flagS');
							
							OldFlags = event+gFlag+sFlag+bFlag;
						}
						t = setTimeout("GetFlags()",1000);
	
					} catch(e) {
						document.getElementById('flagG').style.visibility = "hidden";
						document.getElementById('flagS').style.visibility = "hidden";
						document.getElementById('flagB').style.visibility = "hidden";
						OldFlags='';
						t = setTimeout("GetFlags()",1000);
					}
				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}


var flS = new Image;
var flG = new Image;
var flB = new Image;


function setFlagBehaviour(flG, flCanvas) {
	var flag = document.getElementById(flCanvas);
	var amp = 15;

	flag.width  = document.documentElement.clientWidth/6;
	flag.height = (flag.width* flG.height / flG.width) + amp*2;

//	flag.clearRect(0, 0, flag.width, flag.height);
	flag.getContext('2d').drawImage(flG,0,amp, flag.width, flag.width * flG.height / flG.width);
	timers.push(waveFlag( flag, flag.width/5, amp ));
}

function waveFlag(canvas, wavelength, amplitude, period, shading, squeeze ){
	if (!squeeze)    squeeze    = 0;
	if (!shading)    shading    = 100;
	if (!period)     period     = 200;
	if (!amplitude)  amplitude  = 10;
	if (!wavelength) wavelength = canvas.width/10;

	var fps = 30;
	var ctx = canvas.getContext('2d');
	var   w = canvas.width, h = canvas.height;
	var  od = ctx.getImageData(0,0,w,h).data;
	// var ct = 0, st=new Date;
	return setInterval(function(){
		var id = ctx.getImageData(0,0,w,h);
		var  d = id.data;
		var now = (new Date)/period;
		for (var y=0;y<h;++y){
			var lastO=0,shade=0;
			var sq = (y-h/2)*squeeze;
			for (var x=0;x<w;++x){
				var px  = (y*w + x)*4;
				var pct = x/w;
				var o   = Math.sin(x/wavelength-now)*amplitude*pct;
				var y2  = y + (o+sq*pct)<<0;
				var opx = (y2*w + x)*4;
				shade = (o-lastO)*shading;
				d[px  ] = od[opx  ]+shade;
				d[px+1] = od[opx+1]+shade;
				d[px+2] = od[opx+2]+shade;
				d[px+3] = od[opx+3];
				lastO = o;
			}
		}
		ctx.putImageData(id,0,0);		
		// if ((++ct)%100 == 0) console.log( 1000 * ct / (new Date - st));
	},1000/fps);
}

function raiseFlag() {
	document.getElementById('flagS').style.top='0%';
	document.getElementById('flagG').style.top='0%';
	document.getElementById('flagB').style.top='0%';
}
