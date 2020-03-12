var timeout=100;
var ResTimer;


function ResultsInit() {
	loadComboSchedule();
	LoadTablets();

//	window.onbeforeunload=function() {SetAutoImport(true);};
//	window.onunload=function() {SetAutoImport(true);};
}

function LoadTablets(obj) {
	if(ComboScheduleLoaded==false) ResTimer = setTimeout(function() {LoadTablets(this)}, timeout);

	// fills in the locked ends
	var session=document.getElementById('x_Session').value;
	var distance=document.getElementById('x_Distance').value;
	var end=document.getElementById('x_End').value;
	if(session=='' || (session[0]=='Q' && distance==0) || end==0) {
		document.getElementById('TabletInfo').innerHTML='';
		return;
	}

	// Clears timeout
	clearTimeout(ResTimer);

	$.get('Res-LoadTablets.php?ses='+session+'&dist='+distance+'&end='+end+'&maxend='+document.getElementById('x_End').max, function(XMLResp) {
					// intercetto gli errori di IE e Opera
					if (!XMLResp || !XMLResp.documentElement) {
						throw("XML not valid:\n"+XMLResp.responseText);
					}

					// Intercetto gli errori di Firefox
					var XMLRoot;
					if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
						throw("XML not valid:\n");
					}

					XMLRoot = XMLResp.documentElement;

					if (XMLRoot.getAttribute('error')==0) {
						// retrieves the html
						var html=XMLRoot.getElementsByTagName('html')[0].firstChild.data;
						document.getElementById('TabletInfo').innerHTML=html;

						// retrieves the ends to lock
						var sticky=XMLRoot.getElementsByTagName('sticky')[0].firstChild.data;
						document.getElementById('StickyEnds').innerHTML=sticky;

						// retrieves the message
						document.getElementById('ISK-sticky').innerHTML=XMLRoot.getElementsByTagName('sm')[0].firstChild.data;

						ComboScheduleLoaded=true;
						UpdateTablets();
					} else {
					}
    });
}

function UpdateTablets() {
	var session=document.getElementById('x_Session').value;
	var distance=document.getElementById('x_Distance').value;
	var end=document.getElementById('x_End').value;

	// Clears timer
	clearTimeout(ResTimer);

	if(session=='' || (session[0]=='Q' && distance==0) || end==0 || document.getElementById('TabletInfo').innerHTML=='') {
		ComboScheduleLoaded=false;
		document.getElementById('TabletInfo').innerHTML='';
		timeout=1000;
		LoadTablets();
		return;
	}

	$.get('Res-UpdateTablets.php?ses='+session+'&dist='+distance+'&end='+end+'&maxend='+document.getElementById('x_End').max, function(XMLResp) {
        // intercetto gli errori di IE e Opera
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Intercetto gli errori di Firefox
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // assigns the return values of sequence
            document.getElementById('ISK-ses').innerHTML=XMLRoot.getAttribute('ses');
            document.getElementById('ISK-dis').innerHTML=XMLRoot.getAttribute('dis');
            document.getElementById('ISK-end').innerHTML=XMLRoot.getAttribute('end');
            document.getElementById('ISK-sticky').innerHTML=XMLRoot.getElementsByTagName('sm')[0].firstChild.data;

            // assigns the sticky ends if any
            var data=XMLRoot.getElementsByTagName('st');
            for(var i=0; i<data.length; i++) {
                document.getElementById(data[i].getAttribute('id')).checked=(data[i].getAttribute('checked')=='1' ? true : false);
            }


            // assigns the letters colors
            var data=XMLRoot.getElementsByTagName('a');
            for(var i=0; i<data.length; i++) {
                var elem=document.getElementById('l-'+data[i].getAttribute('id'));
                elem.className='Let-'+data[i].getAttribute('v');
                if(data[i].getAttribute('a')==1) {
                    elem.className+=' Anomaly';
                }
            }
            // assign the target color, device id and message
            var data=XMLRoot.getElementsByTagName('t');
            var autoImport=document.getElementById('AutoImport').checked;
            for(var i=0; i<data.length; i++) {
                var lt=data[i].getAttribute('v');
                var id=data[i].getAttribute('id');
                var over=data[i].getAttribute('o');
                var anomaly=data[i].getAttribute('a');
                document.getElementById('t-'+id).className='TargetTitle Let-'+lt;
                document.getElementById('d-'+id).innerHTML=data[i].getAttribute('d');
                document.getElementById('m-'+id).innerHTML=data[i].firstChild.data;
                document.getElementById('i-'+id).disabled=(lt!='Y' && lt!='Z' && lt!='O');
                document.getElementById('i-'+id).style.display=((autoImport && !data[i].getAttribute('i')) ? 'none' : 'inline-block');
                if(over==1) {
                    document.getElementById('t-'+id).parentNode.className="TargetContainer Let-F";
                }
                if((typeof Anomalies !== 'undefined') && (lt=='B' || over==1) && anomaly==0) {
                    document.getElementById('t-'+id).parentNode.style.display='none';
                } else {
                    document.getElementById('t-'+id).parentNode.style.display='block';
                }
            }

            // assign the device payload
            var data=XMLRoot.getElementsByTagName('pl');
            for(var i=0; i<data.length; i++) {
                var id=data[i].getAttribute('id');
                document.getElementById('t-'+id).setAttribute('value', data[i].firstChild.data);
            }


            // check if there are messages
            var data=XMLRoot.getElementsByTagName('msg');
            if(data.length==1) {
                document.getElementById('Errors').innerHTML=data[0].firstChild.data;
            } else {
                document.getElementById('Errors').innerHTML='';
            }

            //updates the popup if any
            if(ActiveTarget!='') {
                seeTarget(ActiveTarget);
            }
        } else {
        }
        ResTimer=setTimeout(function() {UpdateTablets()}, 1000);
    });
}

function SetAutoImport(action) {
	if(!action && !confirm(msgAreYouSure)) {
		document.getElementById('AutoImport').checked=(!document.getElementById('AutoImport').checked);
		return;
	}
	document.getElementById('cmdImport').className = (document.getElementById('AutoImport').checked ? 'hidden':'');
	var targets=document.querySelectorAll('input.TgtImport');
	for(i=0; i<targets.length; i++) {
		targets[i].style.display=(document.getElementById('AutoImport').checked ? 'none' : 'inline-block');
	}

	$.get('Res-AutoImport.php?stop='+(action ? '0' : '1'), function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
        } else {
            // error condition
        }
    });
}

function toggleSticky(obj) {
	var session=document.getElementById('x_Session').value;
	var distance=document.getElementById('x_Distance').value;
	$.get('Res-ToggleSticky.php?ses='+session+'&dist='+distance+'&'+obj.id+'='+(obj.checked ? '1' : '0'), function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
        } else {
            // error condition
            document.getElementById('ISK-sticky').innerHTML=XMLRoot.getElementsByTagName('sm')[0].firstChild.data;
        }
    });
}

function dataImport(Tgt) {
	var session=document.getElementById('x_Session').value;
	var distance=document.getElementById('x_Distance').value;
	var end=document.getElementById('x_End').value;
	var qry='ses='+session+'&dist='+distance+'&end='+end;
	if(Tgt!=null) {
		qry=document.getElementById('t-'+Tgt.id.substr(2)).getAttribute('value');
	}

	$.get('Res-DataImport.php?'+qry, function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
        } else {
            // error condition
        }
    });
}

var ActiveTarget='';

function seeTarget(obj) {
	var y=Math.max(obj.offsetTop-20, 50);
	var d = document.getElementById('PopUp');
//	d.style.top=y+'px';
	ActiveTarget = obj;
	var tit=document.getElementById('PopUpTitle')
	tit.innerHTML=obj.getAttribute('title');
	tit.className.parentNode=obj.className;

	d.style.display="block";

	// let's get the content...
    $.get('Res-SeeTarget.php?'+obj.getAttribute('value'), function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
            document.getElementById('PopUpContent').innerHTML=XMLRoot.getElementsByTagName('html')[0].firstChild.data;
            var Arrows=document.getElementById('PopUpContent').querySelectorAll('div.Let-Z');
            document.getElementById('PopupRemove').disabled=(Arrows.length==0);
            document.getElementById('PopupImport').disabled=(Arrows.length==0);
        } else {
            // error condition
        }
    });
}

function closeTarget() {
	document.getElementById('PopUpContent').innerHTML='';
	document.getElementById('PopUp').style.display='none';
	ActiveTarget='';
}

function popupRemove(obj) {
	if(!confirm(msgAreYouSure)) return;

    var payloads=document.querySelectorAll('#PopUpContent .PopUpSpot');
    var qry='';
    for(var i=0; i<payloads.length; i++) {
        if(payloads[i].getAttribute('value')!='') qry+='&'+payloads[i].getAttribute('value');
    }
	$.get('Res-RemoveEnd.php?'+qry.substring(1), function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
            closeTarget();
        } else {
            // error condition
        }
    });
}

function popupImport(obj) {
	if(!confirm(msgAreYouSure)) return;

    var payloads=document.querySelectorAll('#PopUpContent .PopUpSpot');
    var qry='';
    for(var i=0; i<payloads.length; i++) {
        if(payloads[i].getAttribute('value')!='') qry+='&'+payloads[i].getAttribute('value');
    }

    $.get('Res-ImportEnd.php?'+qry.substring(1), function(XMLResp) {
        // IE & Opera error
        if (!XMLResp || !XMLResp.documentElement) {
            throw("XML not valid:\n"+XMLResp.responseText);
        }

        // Firefox Errors
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") {
            throw("XML not valid:\n");
        }

        XMLRoot = XMLResp.documentElement;

        if (XMLRoot.getAttribute('error')==0) {
            // modify the pannel
            closeTarget();
        } else {
            // error condition
        }
    });
}

