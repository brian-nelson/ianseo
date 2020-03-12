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
	var altezza = getWinHeight();
	
	// table
	obj.parentElement.parentElement.parentElement.parentElement.style.height = altezza+"px";
	
	// div
	obj.parentElement.parentElement.parentElement.parentElement.parentElement.style.height = altezza+"px";
}

function init() {
	var Content=document.getElementById('Content');
	ConWidth=getWinWidth() - 6;
	ConHeight=getWinHeight() - 6;

	document.getElementById('Content').style.height=ConHeight+"px";
	document.getElementById('Content').style.overflow="hidden";
	altezza = getWinHeight();

	post_init();
	
	for(z=0;z<Quadro;z++) {
		var tmpQuadro=document.getElementById('scrolltop'+z);
		var tmpHead=document.getElementById('scrollhead'+z);
		var tmpCont=document.getElementById('scrolldiv'+z);
		altezza=Math.min(ConHeight, getObjectHeight(tmpCont)+(tmpHead == undefined ? 0 : getObjectHeight(tmpHead)));
//		tmpQuadro.style.height=altezza+"px";
		
		//altezza = document.getElementById('scrolltop'+z).offsetTop + ConHeight - document.getElementById('scrolldiv'+z).offsetTop;
		tmp=document.getElementById('scrolldiv'+z);
		if(tmp.title=='MM'+z) {
			tmp.style.height = altezza+"px";
			tmp2=document.getElementById('scrolltab'+z);
			tmp2.style.height = altezza+"px";
		} else {
			//tmp.style.height = tmp.clientHeight+"px";
			tmp.style.height = tmp.offsetHeight+"px";
		}
		tmp.style.overflow="hidden";

		if(z==0) {
			tmp=document.getElementById('scrolldivEnd');
			if(tmp.title=='MMEnd') {
				tmp.style.height = altezza+"px";
				tmp2=document.getElementById('scrolltabEnd');
				tmp2.style.height = altezza+"px";
			} else {
				//tmp.style.height = tmp.clientHeight+"px";
				tmp.style.height = tmp.offsetHeight+"px";
			}
			tmp.style.overflow="hidden";
		}
	}

	elemento = document.getElementById('scrolldiv'+i);
	next_element=document.getElementById('scrolldiv'+j);
	if(next_element==undefined) next_element=document.getElementById('scrolldivEnd');
	//altezza  = parseInt(elemento.style.height);
	altezza  = ConHeight;
	tabella = document.getElementById('scrolltab'+i);

	// scrolla il div interno
	aspetta=timeStop[i];

	scroller_interno=window.setInterval("start_scroll_interno()", timeScroll[i]);
	
	if(RotMatches) Matches=setTimeout('GetMatchContent()', 2000);
}

function start_scroll_interno() {
	var alt=parseInt(elemento.offsetHeight) - PixelScroll;
	if(alt<0) alt=0;

	// check the reload of next frame
	if(!loaded && alt<=altezza && next_element!=undefined && next_element.title=='DB'+j && FreshDBContent[j]!='') {
		pageRequest = new XMLHttpRequest()

		if (pageRequest){ //if pageRequest is not false
			pageRequest.open('GET', FreshDBContent[j], false) //get page synchronously
			pageRequest.send(null)
			document.getElementById('scrolltop'+j).innerHTML = pageRequest.responseText;
			tmp=document.getElementById('scrolldiv'+j);
			loaded=true;

			tmp=document.getElementById('scrolldiv'+j);
			tmp.style.height = tmp.offsetHeight+"px";
			tmp.style.overflow="hidden";
		}
	}

	if(aspetta) {
		aspetta--;
		return;
	}

	elemento.style.height = alt + 'px';

	if(alt < 1 || elemento.title=='MM'+i) {
		loaded=false;
		// aspetta=tempo_attesa;

		// fai salire la pagina fino al prossimo elemento
		// ferma lo scroller interno
		scroller_interno=window.clearInterval(scroller_interno);

		elemento = document.getElementById('Content');

		i++; // passa al prossimo quadro
		j=i+1;
		tabella = document.getElementById('scrolltop'+i);
		if(tabella == undefined || tabella=='undefined' || !tabella ) tabella = document.getElementById('scrolltopEnd');

		// era l'ultimo quadro?
		var AA_TotHeight = elemento.scrollHeight;
		var AA_BottomSpace = elemento.scrollHeight - elemento.scrollTop - elemento.clientHeight;
		if(tabella == undefined || tabella=='undefined' || !tabella || AA_BottomSpace <= 0) {
			reload=true;
		}

		// attiva lo scroller esterno
		scroller_esterno=window.setInterval("start_scroll_esterno()", timeScroll[i-1]);

	} else {
		if(cicli==0) {
			d = new Date();
			d1=d.getTime();
		}

		if(cicli++ == 5) {
			// tempo per fare 5 cicli:
			d = new Date();
			d2=d.getTime() - d1;
			// tempo impostato per fare 5 cicli
			PixelScroll = Math.round(altezza*d2/(5 * timeScroll[i] * 1000));
		}

	}

	elemento.scrollTop=elemento.scrollTop+PixelScroll;
}

function start_scroll_esterno() {
//	if(aspetta) {
//		aspetta--;
//		return;
//	}
	var AA_BottomSpace = elemento.scrollHeight - elemento.scrollTop - getObjectHeight(elemento);
	if(reload || AA_BottomSpace<=0) {
		reload=false;
		scroller_esterno=window.clearInterval(scroller_esterno);
		window.location.assign(window.location.href);
		return;
	} else {
		alt_elem=elemento.scrollTop;
		alt_tab=tabella.offsetTop;
		elemento.scrollTop=elemento.scrollTop+Math.min(PixelScroll,tabella.offsetTop-elemento.scrollTop);
		if(elemento.scrollTop >= tabella.offsetTop || elemento.scrollHeight - elemento.scrollTop - getObjectHeight(elemento) <= 0) {
			scroller_esterno=window.clearInterval(scroller_esterno);
			if(typeof(timeStop[i])=='number') {
				aspetta=timeStop[i];
			} else {
				aspetta=tempo_attesa;
			}

			elemento = document.getElementById('scrolldiv'+i);
			next_element=document.getElementById('scrolldiv'+j);
			if(next_element==undefined) {
				j='End';
				next_element=document.getElementById('scrolldiv'+j);
			}
			if(elemento==undefined) {
				reload=false;
				window.location.assign(window.location.href);
				return;
			}
			//elemento.style.height=altezza+"px";
			//elemento.style.overflow='hidden';
			tabella = document.getElementById('scrolltab'+i);

			var SecScrol=(timeScroll[i]==undefined ? 10 : timeScroll[i])
			scroller_interno = window.setInterval("start_scroll_interno()", SecScrol);
		}
	}
}

var XMLMatches = CreateXMLHttpRequestObject();

function GetMatchContent() {
	if (!XMLMatches) return;
	try {
		XMLMatches.open("POST","GetMatchContent.php?TourId=" + TourId + '&RuleId=' + RuleId, true);
		XMLMatches.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		XMLMatches.onreadystatechange=GetMatchContent_StateChange;
		XMLMatches.send(null);
	} catch (e) { }
}

function GetMatchContent_StateChange() {
	if (XMLMatches.readyState==XHS_COMPLETE && XMLMatches.status==200) {
		try {
			GetMatchContent_Response();
		} catch(e) { }
	}
}

function GetMatchContent_Response() {
	var XMLResp=XMLMatches.responseXML;
	if (!XMLResp || !XMLResp.documentElement)
		throw(XMLResp.responseText);
	
	var XMLRoot;
	if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
		throw("");

	XMLRoot = XMLResp.documentElement;
	
	var MatchId = XMLRoot.getElementsByTagName('matchid');
	var Score = XMLRoot.getElementsByTagName('score');
	var tmp;
	
	if (MatchId) {
		for (mc=0; mc < MatchId.length; mc++) {
			var tmp=document.getElementById(MatchId.item(mc).firstChild.data);
			if(tmp)  tmp.innerHTML=Score.item(mc).firstChild.data;
		} 
	}

//	XMLHttp = CreateXMLHttpRequestObject();
	
	Matches=setTimeout('GetMatchContent()', 2000);
}
