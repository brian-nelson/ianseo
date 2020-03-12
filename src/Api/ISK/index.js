var ComboScheduleLoaded=false;
var timerReference;

function saveSequence(save) {
	var curSession = $('#x_Session').val();
	var curDistance = $('#x_Distance').val();
	var curEnd = $('#x_End').val();
	$.getJSON('Ind-UpdateSequence.php'+(save ? '?session='+curSession+'&distance='+curDistance+'&end='+curEnd : ''), function(data) {
        if (data.error==0) {
            $('#x_Session').val(data.type+data.maxdist+data.session);
            loadComboDistanceEnd();
            $('#x_Distance').val(data.distance);
            $('#x_End').val(data.end);
            loadDevices();
        }
    });
}

function loadDevicesOrdered(obj) {
    var oldOrd=$(obj).attr('ordertype');
    var newOrd='ordasc';
    $('[ordertype^=ord]').attr('ordertype','');
    if(oldOrd=='ordasc') {
        newOrd='orddesc';
    }
    $(obj).attr('ordertype', newOrd);

    loadDevices();
}

function loadComboDistanceEnd(distance) {
	var curSession = $('#x_Session').val();
	var MaxEnds=$('#x_Session option:selected')[0].maxEnds
	var Combo = $('#x_Distance')[0];
	for(i=Combo.length-1; i>=0; --i) {
		Combo.remove(i);
	}
	if(curSession.charAt(0)=='Q') {
		for(i=0; i<curSession.charAt(1); i++) {
			Combo.options[i] = new Option(i+1,i+1);
			if(distance && distance==i+1) {
				Combo.options[i].selected=true;
			}
		}
		var Ends=MaxEnds.split(',');
		var End=Ends[0]*1;
	} else {
		Combo.options[0] = new Option("--","");
		var End=MaxEnds*1;
	}
	$('#x_End')[0].max=End+(curSession.charAt(0)=='Q' ? 0 : 1);
	$('#x_End').val(0);
}

function adjustMaxEnd() {
	var curSession = document.getElementById('x_Session').value;
	var MaxEnds=document.getElementById('x_Session').options[document.getElementById('x_Session').selectedIndex].maxEnds
	var curDistance=document.getElementById('x_Distance').value;
	if(curDistance>0) {
		var Ends=MaxEnds.split(',');
		var End=Ends[curDistance-1];
	} else {
		var End=MaxEnds;
	}
	document.getElementById('x_End').max=End;
	document.getElementById('x_End').value=0;
}

function loadComboSchedule() {
	var onlyToday = (document.getElementById('x_onlyToday').checked==true ? 1:0);

	$.get('Ind-GetComboSchedule.php?onlyToday='+onlyToday, function(XMLResp) {
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
            var Combo = document.getElementById('x_Session');

            var Arr_Code = XMLRoot.getElementsByTagName('val');
            var Arr_Name = XMLRoot.getElementsByTagName('display');

            for(var i=Combo.length-1; i>=0; --i) {
                Combo.remove(i);
            }

            Combo.options[0] = new Option("--","");
            for (var i=0;i<Arr_Code.length;++i) {
                Combo.options[i+1] = new Option(Arr_Name.item(i).firstChild.data,Arr_Code.item(i).firstChild.data);

                // adding maxEnds support
                var MaxEnds=Arr_Code.item(i).getAttribute('maxends');
                Combo.options[i+1].maxEnds=MaxEnds;

                if(Arr_Code.item(i).getAttribute('selected')=='1') {
                    Combo.options[i+1].selected=true;
                    var Ends=MaxEnds.split(',');
                    var End=Ends[0];
                    if(XMLRoot.getAttribute('end')>0) {
                        End=Ends[XMLRoot.getAttribute('end')-1];
                    }
                    document.getElementById('x_End').max=End;
                }
            }
            loadComboDistanceEnd(XMLRoot.getAttribute('distance'));
            document.getElementById('x_End').value=XMLRoot.getAttribute('end');

            ComboScheduleLoaded=true;
        } else {
        }
    });
}

function loadDevices() {
    clearTimeout(timerReference);

    Query='?field='+$('[ordertype^=ord]').attr('id')+'&ord='+$('[ordertype^=ord]').attr('ordertype');

    $.get('Ind-GetTabletsInfo.php'+Query, function(XMLResp) {
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
            var Arr_Tablets = XMLRoot.getElementsByTagName('tablet');
            var objTbody = document.getElementById('tablets');
            var arrTablets=new Array();
            for(var i=objTbody.rows.length-1; i>=0; --i) {
                arrTablets[i]=1;
//							objTbody.deleteRow(i);
            }
            var row;
            for(var i=0; i<Arr_Tablets.length; i++) {
                var device = Arr_Tablets.item(i).getAttribute('device');
                if(row=document.getElementById('row_'+device)) {
                    $(row).attr('order', Arr_Tablets.item(i).getAttribute('order'))
                    // TargetNo
                    var TgtRequested=Arr_Tablets.item(i).getAttribute('reqtarget');
                    var TgtAssigned=Arr_Tablets.item(i).getAttribute('target');
                    var tmp = row.cells[0];
                    tmp.innerHTML = Arr_Tablets.item(i).getAttribute('target')
                    if(Arr_Tablets.item(i).getAttribute('reqtarget') != '0'){
                        tmp.innerHTML += '&nbsp;(<span>'+Arr_Tablets.item(i).getAttribute('reqtarget')+'</span>)';
                        tmp.className = "TargetRequested";
                    } else {
                        tmp.className = "TargetAssigned";
                    }
                    row.cells[0].style.backgroundColor = Arr_Tablets.item(i).getAttribute('online');

                    // auth request
                    tmp=row.cells[1];
                    tmp.innerHTML = (Arr_Tablets.item(i).getAttribute('authrequest')==1 ? '<img src="'+imgPath+'isk-exclamation.png" title="'+msgIskRequiresApproval+'">' : '');

                    // tournament
                    tmp = row.cells[2];
                    tmp.innerHTML = '<img class="ClickableDiv" src="'+imgPath+'isk-status-'+(Arr_Tablets.item(i).getAttribute('tournament')==TourId ? 'ok':'noshoot')+'.png" onClick="setCompetitionDevice(\''+device+'\','+(Arr_Tablets.item(i).getAttribute('tournament')==TourId ? 'false':'true')+')">';

                    //Application Pro/Lite and device Code
                    row.cells[3].innerHTML = Arr_Tablets.item(i).getAttribute('code');
                    row.cells[4].innerHTML = (Arr_Tablets.item(i).getAttribute('appdevversion')==1 ? Pro : Lite);
                    row.cells[5].innerHTML = Arr_Tablets.item(i).getAttribute('appversion');

                    //Device ID
                    row.cells[6].innerHTML = device;

/* SE LA RIACCENDI ANCORA SENZA MIA AUTORIZZAZIONE TI TOLGO I PRIVILEGI DI SCRITTURA - Teo
* 								//Device Alive
                    if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
                        if(row.cells[7].childElementCount>0) {
                            row.cells[7].firstChild.src='Ind-GetTabletOnline.php?device='+Arr_Tablets.item(i).getAttribute('code');
                        } else {
                            row.cells[7].innerHTML='<img src="Ind-GetTabletOnline.php?device='+Arr_Tablets.item(i).getAttribute('code')+'" height="16" width="16">';
                        }
                    }
*/
                    row.cells[7].innerHTML=Arr_Tablets.item(i).getAttribute('seconds');

                    //Device Status
                    tmp = row.cells[8];
                    if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
                        switch(Arr_Tablets.item(i).getAttribute('state')) {
                        case "0":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-noshoot.png" title="'+msgIskStatusNoShoot+'">';
                            tmp.className = "ToBeEnabled";
                            break;
                        case "1":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-ok.png" title="'+msgIskStatusOK+'">';
                            tmp.className = "Center";
                            break;
                        case "2":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-unknown.png" title="'+msgIskStatusReloading+'">';
                            tmp.className = "BarcodeRequested";
                            break;
                        case "3":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-ok-gray.png" title="'+msgIskStatusWaitConfirm+'">';
                            tmp.className = "BarcodeRequested";
                            break;
                        }
                    }

                    // Change status bar
                    tmp = row.cells[9];
                    if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
                        tmp.innerHTML = '<img class="ClickableDiv" src="'+imgPath+'isk-status-noshoot.png" onClick="setStatusDevice(\''+device+'\',0)" title="'+msgIskDenyAccess+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-unknown.png" onClick="setStatusDevice(\''+device+'\',2)" title="'+msgIskReloadConfig+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-ok-gray.png" onClick="setStatusDevice(\''+device+'\',3)" title="'+msgIskForceConfirm+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-ok.png" onClick="setStatusDevice(\''+device+'\',1)" title="'+msgIskApproveConfig+'">';
                    }

                    //Battery
                    row.cells[10].innerHTML = Arr_Tablets.item(i).getAttribute('battery');

                    //Ip Address
                    row.cells[11].innerHTML = Arr_Tablets.item(i).getAttribute('ip');

                    //Last Seen
                    row.cells[12].innerHTML = Arr_Tablets.item(i).getAttribute('lastseen');
                    row.cells[12].style.backgroundColor = Arr_Tablets.item(i).getAttribute('online');

                    // remove is already set...
                } else {
                    var row = objTbody.insertRow(i);
                    row.id='row_'+device;
                    row.setAttribute('order', Arr_Tablets.item(i).getAttribute('order'))
                    //TargetNo
                    var cellIndex=0;
                    var tmp = row.insertCell(cellIndex++);
                    var TgtRequested=Arr_Tablets.item(i).getAttribute('reqtarget');
                    var TgtAssigned=Arr_Tablets.item(i).getAttribute('target');
                    var Group=0;
                    tmp.id = 'tgt_'+device;
                    // tmp.setAttribute('onClick', 'manageTargetNo(\''+device+'\',0'+(TgtAssigned!=TgtRequested && TgtRequested!=0 ? TgtRequested : TgtAssigned)+');');
                    tmp.setAttribute('onClick', 'manageTargetGroup(\''+device+'\','+Group+');');
                    tmp.innerHTML = TgtAssigned;
                    if(TgtRequested != '0'){
                        tmp.innerHTML += '&nbsp;(<span>'+TgtRequested+'</span>)';
                        tmp.className = "TargetRequested";
                    } else {
                        tmp.className = "TargetAssigned";
                    }
	                tmp.style.backgroundColor = Arr_Tablets.item(i).getAttribute('online');

	                // auth request
                    tmp = row.insertCell(cellIndex++);
                    tmp.innerHTML = (Arr_Tablets.item(i).getAttribute('authrequest')==1 ? '<img src="'+imgPath+'isk-exclamation.png" title="'+msgIskRequiresApproval+'">' : '');
                    tmp.className = "Center";

                    // tournament
                    tmp = row.insertCell(cellIndex++);
                    tmp.innerHTML = '<img class="ClickableDiv" src="'+imgPath+'isk-status-'+(Arr_Tablets.item(i).getAttribute('tournament')==TourId ? 'ok':'noshoot')+'.png" onClick="setCompetitionDevice(\''+device+'\','+(Arr_Tablets.item(i).getAttribute('tournament')==TourId ? 'false':'true')+')">';
                    tmp.className = "Center";
                    //Application Pro/Lite and device Code
                    (row.insertCell(cellIndex++)).innerHTML = Arr_Tablets.item(i).getAttribute('code');
                    (row.insertCell(cellIndex++)).innerHTML = (Arr_Tablets.item(i).getAttribute('appdevversion')==1 ? Pro : Lite);
                    (row.insertCell(cellIndex++)).innerHTML = Arr_Tablets.item(i).getAttribute('appversion');
                    //Device ID
                    (row.insertCell(cellIndex++)).innerHTML = device;
                    //Device Alive
                    tmp = row.insertCell(cellIndex++);
                    tmp.className = "Center";
//							if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
//								tmp.innerHTML = '<img src="Ind-GetTabletOnline.php?device='+Arr_Tablets.item(i).getAttribute('code')+'" height="16" width="16">';
//							}
                    //Device Status
                    tmp = row.insertCell(cellIndex++);
                    tmp.className = "Center";
                    if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
                        switch(Arr_Tablets.item(i).getAttribute('state')) {
                        case "0":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-noshoot.png" title="'+msgIskStatusNoShoot+'">';
                            tmp.className = "ToBeEnabled";
                            break;
                        case "1":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-ok.png" title="'+msgIskStatusOK+'">';
                            break;
                        case "2":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-unknown.png" title="'+msgIskStatusReloading+'">';
                            tmp.className = "BarcodeRequested";
                            break;
                        case "3":
                            tmp.innerHTML = '<img src="'+imgPath+'isk-status-ok-gray.png" title="'+msgIskStatusWaitConfirm+'">';
                            tmp.className = "BarcodeRequested";
                            break;
                        }
                    }
                    // Change status bar
                    tmp = row.insertCell(cellIndex++);
                    tmp.className = "Center";
                    tmp.style.whiteSpace='nowrap';
                    if(Arr_Tablets.item(i).getAttribute('tournament')==TourId) {
                        tmp.innerHTML = '<img class="ClickableDiv" src="'+imgPath+'isk-status-noshoot.png" onClick="setStatusDevice(\''+device+'\',0)" title="'+msgIskDenyAccess+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-unknown.png" onClick="setStatusDevice(\''+device+'\',2)" title="'+msgIskReloadConfig+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-ok-gray.png" onClick="setStatusDevice(\''+device+'\',3)" title="'+msgIskForceConfirm+'">';
                        tmp.innerHTML += '&nbsp;&nbsp;&nbsp;<img class="ClickableDiv" src="'+imgPath+'isk-status-ok.png" onClick="setStatusDevice(\''+device+'\',1)" title="'+msgIskApproveConfig+'">';
                    }
                    //Battery
                    (row.insertCell(cellIndex++)).innerHTML = Arr_Tablets.item(i).getAttribute('battery');
                    //Ip Address
                    tmp = row.insertCell(cellIndex++);
                    tmp.innerHTML = Arr_Tablets.item(i).getAttribute('ip');
                    tmp.className = "Right";
                    //Last Seen
                    tmp = row.insertCell(cellIndex++);
                    tmp.innerHTML = Arr_Tablets.item(i).getAttribute('lastseen');
                    tmp.className = "Right";
                    tmp.style.backgroundColor = Arr_Tablets.item(i).getAttribute('online');

                    // remove device from DB
                    tmp = row.insertCell(cellIndex++);
                    tmp.innerHTML = '<input type="button" class="ClickableDiv" value="'+msgRemove+'" onclick="if(confirm(\''+MsgConfirm+'\')) {window.location.href=\'?remove='+device+'\'}">';
                    tmp.style.textAlign = "center";
                }
            }


            var rows = $(objTbody).find('tr[order]').get();
            rows.sort(function(a, b) {
                var keyA = parseInt($(a).attr('order'));
                var keyB = parseInt($(b).attr('order'));
                if (keyA < keyB) return -1;
                if (keyA > keyB) return 1;
                return 0;
            });
            $.each(rows, function(index, row) {
                $(objTbody).append(row);
            });

        } else {
        }
        timerReference = setTimeout(function(){ loadDevices(); }, 5000);
    });
}

function manageTargetGroup(device, Group) {
    var d = document.getElementById('PopUp');

    $('#PopDevice').html(device);
    $('#PopGroup').html(String.fromCharCode(65+Group));
    var tgt=$('#tgt_'+device);
    var req=$(tgt).find('span').html();
    var tgt=tgt.html();

    if(parseInt(req)>0) {
        tgt+=' <input type="button" value="Accept" onclick="AcceptRequest('+req+')">';
    }

    $('#PopTarget').html(tgt);

    $('#NewTarget').val($('#tgt_'+device).html());
    $('#NewGroup').val(Group);
    $('#PopUp').show();

}

function AcceptRequest(tgt) {
    $('#NewTarget').val(tgt);
    AssignGroupTarget();
}

function AssignGroupTarget() {
    var device=$('#PopDevice').html();
    $.get('Ind-SetDeviceInfo.php?device='+device+'&setTarget='+$('#NewTarget').val(), function(XMLResp) {
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
        // document.getElementById('tgt_'+device).setAttribute('onClick','manageTargetNo(\''+device+'\','+(objText.value)+');');

        closePopup();
    });
}

function setStatusDevice(device, newStatus) {
	if(newStatus==0 || confirm(MsgConfirm) ) {
		clearTimeout(timerReference);
		$.get('Ind-SetDeviceInfo.php?device='+device+'&setStatus='+newStatus, function(XMLResp) {

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
                loadDevices();
            } else {
                timerReference = setTimeout(function(){ loadDevices(); }, 5000)
            }
        });
	}
}

function setCompetitionDevice(device,enable) {
	if(enable || confirm(MsgConfirm) ) {
		clearTimeout(timerReference);
		$.get('Ind-SetDeviceInfo.php?device='+device+'&setCompetition='+enable, function(XMLResp) {
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
                loadDevices();
            } else {
                timerReference = setTimeout(function(){ loadDevices(); }, 5000)
            }
        });
	}
}

function closePopup() {
    $('#PopUp').hide();
}

