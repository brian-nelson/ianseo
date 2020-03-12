var RowNodes = {
		enStatus:0,
		enSession:1,
		enTargetno:2,
		enCode:3,
		enLocalCode:4,
		enFirstname:5,
		enName:6,
		enEmail:7,
		enCaption:8,
		enDob:9,
		enSex:10,
		enCountry_code:11,
		enCountry_name:12,
		enWheelchair:13,
		enDivision:14,
		enAgeclass:15,
		enClass:16,
		enSubclass:17,
		enTargetface_name:18
	};

// var rowId=null;
// var enId=0;
// var activeCell=null;
// var activeValue='';
// var activeField='';
// var activeWhat='';


function insertInput(cell, what){
	var url='';

	// if(rowId) resetCell();

	var rowId=cell.parentNode.id;
	var tmp=rowId.split('_');
	var enId=tmp[2];

	switch(what) {
        case 'subclass':
            url='Get-Subclasses.php';
            break;
        case 'division':
            url='Get-Divisions.php';
            break;
        case 'ageclass':
            url='Get-AgeClasses.php?enid='+enId;
            break;
        case 'class':
            url='Get-Classes.php?enid='+enId;
            break;
        case 'name':
            break;
	}

	if(url>'') { // only combos have to get the correct data!
		createComboField(cell, what, url);
	} else {
		createTextField(cell, what);
	}
}

function createTextField(cell, what) {
    $(cell).attr('oldvalue', $(cell).html());
    $(cell).attr('what', what);
    $(cell).off('click');
    $(cell).attr('onclick', null);

    $(cell).html('<input type="text" onblur="updateField(this)" value="'+$(cell).attr('oldvalue')+'">');
    $(cell).find('input')[0].focus();
}

function createComboField(cell, what, url) {
    var oldValue=$(cell).html();
    $(cell).attr('oldvalue', oldValue);
    $(cell).attr('what', what);
    $(cell).off('click');
    $(cell).attr('onclick', null);

	$.get(url, function(XMLResp) {
    // intercetto gli errori di IE e Opera
        if (!XMLResp || !XMLResp.documentElement)
            throw(XMLResp.responseText);

    // Intercetto gli errori di Firefox
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
            throw("");

        XMLRoot = XMLResp.documentElement;

        var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;

        //alert(Error);
        if (Error==1) {
            $(cell).style='error';
        } else {
            $(cell).style='';

            var activeField=document.createElement('select');

            var opt = document.createElement('option');
            opt.text='--';
            opt.value='';
            try {
                activeField.add(opt,null); // standard
            } catch(ex) {
                activeField.add(opt); // IE ....
            }

            var Specs = XMLRoot.getElementsByTagName('items').item(0).firstChild.data;

            if (Specs!='')  {
                var Fields = Specs.split('---');

                for (var i=0;i<Fields.length;++i) {
                    var opt = document.createElement('option');
                    var KeyVal=Fields[i].split(':::');
                    opt.value=KeyVal[0];
                    opt.text=KeyVal[1];
                    if(oldValue==KeyVal[0] || Fields.length == 1) opt.selected=true
                    try {
                        activeField.add(opt,null); // standard
                    } catch(ex) {
                        activeField.add(opt); // IE ....
                    }
                }
            }

            $(activeField).on('blur', function() { updateField(this); });

            $(cell).empty();
            $(cell).append(activeField);
            activeField.focus();
        }
    });
}

function updateField(obj) {
    var Cell=$(obj).closest('td');
    var oldValue=Cell.attr('oldvalue');
    var what=Cell.attr('what');

	if($(obj).val() == oldValue) {
		resetCell(Cell, what, oldValue);
		return;
	}

    var rowId=$(obj).closest('tr').attr('id');
    var tmp=rowId.split('_');
    var enId=tmp[2];

    var session=(what=='session' ? oldValue : $(obj).closest('tr').find('td').eq(RowNodes.enSession).html());
    var targetno=(what=='targetno' ? oldValue : $(obj).closest('tr').find('td').eq(RowNodes.enTargetno).html());

	$.get('Set-UpdateField.php?id=' + enId
        + '&session=' + session
        + '&targetno=' + targetno
        + '&field=' + what
        + '&value=' + encodeURIComponent(obj.value), function(XMLResp) {

        // intercetto gli errori di IE e Opera
        if (!XMLResp || !XMLResp.documentElement)
            throw(XMLResp.responseText);

        // Intercetto gli errori di Firefox
        var XMLRoot;
        if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror")
            throw("");

        XMLRoot = XMLResp.documentElement;

        var Error = XMLRoot.getElementsByTagName('error').item(0).firstChild.data;
        var Value = XMLRoot.getElementsByTagName('value').item(0).firstChild.data;
        var Update = XMLRoot.getElementsByTagName('update').item(0).firstChild.data;

        if(Update==0 && Error==0) {
            var tmp=rowId.split('_');
            tmp[2]=XMLRoot.getElementsByTagName('id').item(0).firstChild.data
            obj.parentNode.id = tmp.join('_') ;
            enId=tmp[2];
        }

        obj.value=Value;

        if(Error==1) obj.style.backgroundColor='yellow';
        else obj.style.backgroundColor='';
        resetCell(Cell, what, Value);
    });
}

function setError(Cell) {
	Cell.style='error';
	resetCell();
}

function resetCell(Cell, what, Value) {
    $(Cell).html(Value);
    $(Cell).on('click', function(){ insertInput(this, what);});
    switch(what) {
        case 'division':
            insertInput($(Cell).next()[0], 'ageclass');
            break;
        case 'ageclass':
            insertInput($(Cell).next()[0], 'class');
            break;
    }
    // switch(what) {
    // case 'subclass':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'subclass');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'subclass');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'firstname':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'firstname');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'firstname');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'name':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'name');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'name');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'caption':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'caption');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'caption');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'localCode':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'localCode');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'localCode');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'email':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'email');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'email');this.detachEventListener('onclick',arguments.callee);});
		// break;
    // case 'division':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'division');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'division');this.detachEventListener('onclick',arguments.callee);});
    //     ContActiveWhat=activeWhat;
		// break;
    // case 'ageclass':
		// if (activeCell.addEventListener)
		// 	activeCell.addEventListener("click", function(){insertInput(this, 'ageclass');this.removeEventListener('click',arguments.callee,false);}, false);
		// else if (activeCell.attachEvent)
		// 	activeCell.attachEvent("onclick", function(){insertInput(this, 'ageclass');this.detachEventListener('onclick',arguments.callee);});
    //     ContActiveWhat=activeWhat;
		// break;
    // }
    //
    // activeCell.innerHTML=activeField.value;
    //
    // rowId=null;
    // enId=0;
    // activeCell=null;
    // activeValue='';
    // activeField='';
    // activeWhat='';
    // switch(ContActiveWhat) {
    //     case 'division':
    //         insertInput(ContActiveCell.nextElementSibling, 'ageclass');
    //         break;
    //     case 'ageclass':
    //         insertInput(ContActiveCell.nextElementSibling, 'class');
    //         break;
    // }
}

