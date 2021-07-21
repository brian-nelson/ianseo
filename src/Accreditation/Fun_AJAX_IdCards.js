$(function () {
	updateView();
})

function updateView() {
    ShowCountries();
    ShowCategories();
    // ShowPhases();
    ShowEntries();
}

function ShowCountries() {
	var formData= getFormData();

	$.getJSON('GetCountries.php', formData, function(data) {
		if(data.error==0) {
			var newCountries='';
			$.each(data.Countries, function() {
				newCountries+='<option value="'+this.id+'">'+this.txt+'</option>';
			});
			$('#d_Country').html(newCountries);
		}
	});
}

function ShowCategories() {
	var formData= getFormData();

	$.getJSON('GetCategories.php', formData, function(data) {
		if(data.error==0) {
			var newCats='';
			$.each(data.Divisions, function() {
				newCats+='<option value="'+this.id+'">'+this.txt+'</option>';
			});
			$('#d_Division').html(newCats);

			var newCats='';
			$.each(data.Classes, function() {
				newCats+='<option value="'+this.id+'">'+this.txt+'</option>';
			});
			$('#d_Class').html(newCats);
		}
	});
}

function getFormData() {
	var formData= {
		PrintAccredited:$('#PrintAccredited:checked').length,
		PrintPhoto:$('#PrintPhoto:checked').length,
		PrintNotPrinted:$('#PrintNotPrinted:checked').length,
		SortByTarget:$('#SortByTarget:checked').length,
		CardType:$('#BadgeType').val(),
		CardNumber:$('#BadgeNumber').length==0 ? 0 : $('#BadgeNumber').val(),
	};

	if($('#d_Phase:checked').length>0) {
		formData.Phase=$('#d_Phase').val();
	}

	if($('#HasPlastic:checked').length>0) {
		formData.HasPlastic=1;
	}

	formData.Country=[];
	$('#d_Country option:selected').each(function() {
		formData.Country.push(this.value);
	});

	formData.Division=[];
	$('#d_Division option:selected').each(function() {
		formData.Division.push(this.value);
	});

	formData.Class=[];
	$('#d_Class option:selected').each(function() {
		formData.Class.push(this.value);
	});

	formData.QSession=[];
	$('.QSession:checked').each(function() {
		formData.QSession.push(this.value);
	});

	formData.ESession=[];
	$('.ESession:checked').each(function() {
		formData.ESession.push(this.value);
	});

	formData.Event=[];
	$('.Events:checked').each(function() {
		formData.Event.push(this.value);
	});

	formData.TourId=[];
	$('.TourId:checked').each(function() {
		formData.TourId.push(this.value);
	});

	if($('#TopRanked').length>0) {
		formData.TopRanked=$('#TopRanked').val()
	}
	if($('#TopRankedFinal').length>0) {
		formData.TopRankedFinal=$('#TopRankedFinal').val()
	}

	return formData;
}

function ShowEntries() {
	var formData= getFormData();

    hide_confirm();

    $.getJSON('GetEntries.php', formData, function(data) {
    	if(data.error==0) {
		    // clean the Selectors
		    var newEntries='';
		    $.each(data.Entries, function() {
			    newEntries+='<option value="'+this.id+'" style="color:'+this.style+'">'+this.text+'</option>';
		    });
		    $('#p_Entries').html(newEntries);

		    var numbers=data.reds;
		    if($('#PrintNotPrinted:checked').length==0) {
		    	numbers=(parseInt(data.reds)+parseInt(data.greens)) + ': '+data.reds+' + '+data.greens;
		    }
		    $('#getEntriesNum').html(numbers);

		    hide_confirm();
	    }
    });
}

function ConfirmPrinted() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("POST",'ConfirmPrinted.php?CardType='+document.getElementById('BadgeType').value
						+ '&CardNumber='+document.getElementById('BadgeNumber').value, true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error=XMLRoot.getElementsByTagName('error').item(0).value;
						if(Error) {
							alert('Error');
						}

						ShowEntries();
					} catch(e) {
						//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
					}

				};
				XMLHttp.send();
			}
		} catch (e) {
			//document.getElementById('idOutput').innerHTML='Errore: ' + e.toString();
		}
	}
}

function selectEntries(selected) {
	var comboCountry=document.getElementById('d_Country');
	var comboDivision=document.getElementById('d_Division');
	var comboClass=document.getElementById('d_Class');

	query='?type=';

	for(n=0; n<comboCountry.length; n++) {
		if(comboCountry.options[n].selected) query+='&Country[]='+comboCountry.options[n].value;
	}
	for(n=0; n<comboDivision.length; n++) {
		if(comboDivision.options[n].selected) query+='&Division[]='+comboDivision.options[n].value;
	}
	for(n=0; n<comboClass.length; n++) {
		if(comboClass.options[n].selected) query+='&Class[]='+comboClass.options[n].value;
	}

	for(n=1; n<=SesNo; n++) {
		if(document.getElementById('d_Session_'+n).checked) query+='&Session[]='+n;
	}

	var o=
	{
		url: 'GetIdCardEntries.php'+query,
		method: 'POST',
		success: function(response)
		{
			var xmlResp={};

			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);

				//var combo=document.getElementById('d_Rule');
				var combo=Ext.get('p_Entries');

				var i=0;

			// pulisco
				for (i=combo.dom.options.length-1;i>=0;--i)
				{
					combo.dom.remove(i);
				}

				var rules=xmlRoot.getElementsByTagName('entry');

				i=0;
				Ext.each
				(
					rules,
					function(el)
					{
						var code=el.getElementsByTagName('id').item(0).firstChild.data;
						var descr=el.getElementsByTagName('option').item(0).firstChild.data;
						var style=el.getElementsByTagName('style').item(0).firstChild.data;

						combo.dom.options[i]=new Option(descr,code);
						combo.dom.options[i++].style.color = style;
					}
				);


				if (selected)
				{
					combo.dom.value=selected;
				}

			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	};

	Ext.Ajax.request(o);
}

function CreateNewBadge() {
	var XMLHttp=CreateXMLHttpRequestObject();
	if (XMLHttp) {
		try {
			if ((XMLHttp.readyState==XHS_COMPLETE || XMLHttp.readyState==XHS_UNINIT)) {
				XMLHttp.open("POST",'IdCardCreate.php?CardType='+document.getElementById('BadgeType').value+'&CardName='+document.getElementById('newBadgeName').value, true);
				XMLHttp.onreadystatechange=function() {
					if (XMLHttp.readyState!=XHS_COMPLETE) return;
					if (XMLHttp.status!=200) return;
					try {
						var XMLResp=XMLHttp.responseXML;
						// intercetto gli errori di IE e Opera
						if (!XMLResp || !XMLResp.documentElement) throw(XMLResp.responseText);

						// Intercetto gli errori di Firefox
						var XMLRoot;
						if ((XMLRoot = XMLResp.documentElement.nodeName)=="parsererror") throw("ParseError");

						XMLRoot = XMLResp.documentElement;

						var Error=XMLRoot.getAttribute('error');
						if(Error==0) {
							location.href=XMLRoot.getAttribute('page');
						} else {
							alert(Error);
						}
					} catch(e) {
					}
				};
				XMLHttp.send();
			}
		} catch (e) {
		}
	}

}

function baseForm(obj) {
    obj.form.action='IdCards.php';
    obj.form.target='';
}

function checkBibNumber(obj) {
	if ( event.keyCode == 13 ) {
		event.preventDefault();
		event.stopPropagation();
		printBibname(obj);
	}
}

function printBibname(obj) {
	if(document.getElementById('BibNumber').value!='') {
		pdfForm(obj);

		document.getElementById('print_button').click();
	}
}

function pdfForm(obj) {
    activate_confirm(obj.form);
    var Badge=obj.form.BadgeTypeSelector.value;
    if(Badge==undefined) {
        var Badges=obj.form.BadgeTypeSelector;
        for(var i=0; i<Badges.length; i++) {
            if(Badges.item(i).checked) {
                Badge=Badges.item(i).value;
            }
        }
    }
    obj.form.action=Badge;
}

function activate_confirm(form) {
    form.target='Badges';
    document.getElementById('confirm_button').style.display='inline';
}

function hide_custom() {
    var box=document.getElementsByClassName('CustomBadges');
    for(i=0;i<box.length;i++) {
        box[i].style.display='none';
    }
    hide_confirm();
}

function show_custom() {
    var box=document.getElementsByClassName('CustomBadges');
    for(i=0;i<box.length;i++) {
        box[i].style.display='block';
    }
    hide_confirm();
}

function hide_confirm() {
    document.getElementById('confirm_button').style.display='none';
}

function check_confirm(form) {
    form.target='';
    form.action=''
}
