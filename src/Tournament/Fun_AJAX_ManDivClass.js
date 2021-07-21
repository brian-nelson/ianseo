
/*
													- Fun_AJAX_ManDivClass.js -
	Contiene le funzioni ajax usate da ManDivClass.php
*/

/*
	- UpdateField(Tab, Field)
	sends data to UpdateManDivClassField.php.
	Tab è la tabella usata per l'update
	Field è l'id del campo da usare
*/
function UpdateField(Tab, Field) {
	$.getJSON('UpdateManDivClassField.php?Tab=' + Tab + '&Field=' + Field + '&Value=' + encodeURIComponent($('#'+Field).val()), function(data) {
		if(data.error==1) {
			if(data.which!='#') {
				SetStyle(data.which,'error');
			}
		} else {
			if(data.which!='#') {
				SetStyle(data.which,'');
			}
		}
	});
}

/*
	- DeleteRow(Tab,Id,Msg)
	esegue la post a DeleteManDivClassField.php.
	Tab è la tabella usata per l'update
	Id è il valore da eliminare
	Msg è il messaggio di conferma
*/
function DeleteRow(Tab, Id) {
	if (confirm(MsgAreYouSure)) {
		$.getJSON('DeleteManDivClassField.php?Tab=' + Tab + '&Id=' + Id, function(data) {
			if(data.error==0) {
				if (data.which!='#') {
					$('#'+data.which).closest('tr').remove();
				}
			}
		});
	}
}


/*
	- AddDiv(ErrMsg)
	esegue la post a AddDiv.php per aggiungere una divisione.
	ErrMsg è il messaggio di errore
*/
function AddDiv() {
	if($('#New_DivId').val=='' || $('#New_DivDescription').val=='' || $('#New_DivViewOrder').val=='') {
		alert(MsgRowMustBeComplete);
	} else {
		var QueryString
			= 'New_DivId=' + encodeURIComponent(document.getElementById('New_DivId').value)
			+ '&New_DivDescription=' + encodeURIComponent(document.getElementById('New_DivDescription').value)
			+ '&New_DivIsPara=' + encodeURIComponent(document.getElementById('New_DivIsPara').value)
			+ '&New_DivAthlete=' + encodeURIComponent(document.getElementById('New_DivAthlete').value)
			+ '&New_DivViewOrder=' + encodeURIComponent(document.getElementById('New_DivViewOrder').value);
		$.getJSON('AddDiv.php?'+QueryString, function(data) {
			if (data.error==0) {
				$('#tbody_div').append('<tr id="Div_'+data.divid+'">' +
					'<td class="Bold Center">'+data.divid+'</td>' +
					'<td><input type="text" name="d_DivDescription_'+data.divid+'" id="d_DivDescription_'+data.divid+'" size="56" maxlength="32" value="'+data.divdescr+'" onBlur="UpdateField(\'D\',\'d_DivDescription_'+data.divid+'\')"></td>' +
					'<td class="Center"><select name="d_DivIsPara_'+data.divid+'" id="d_DivIsPara_'+data.divid+'" onBlur="UpdateField(\'D\',\'d_DivIsPara_'+data.divid+'\')">' +
					'<option value="0">'+data.no+'</option>' +
					'<option value="1"'+(data.divpara==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
					'</select></td>' +
					'<td class="Center"><select name="d_DivAthlete_'+data.divid+'" id="d_DivAthlete_'+data.divid+'"  onBlur="UpdateField(\'D\',\'d_DivAthlete_'+data.divid+'\')">' +
					'<option value="0">'+data.no+'</option>' +
					'<option value="1"'+(data.divathlete==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
					'</select></td>' +
					'<td class="Center"><input type="text" name="d_DivViewOrder_'+data.divid+'" id="d_DivViewOrder_'+data.divid+'" size="3" maxlength="3" value="'+data.divprogr+'" onBlur="UpdateField(\'D\',\'d_DivViewOrder_'+data.divid+'\')"></td>' +
					'<td class="Center">' +
					'<img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'D\',\''+data.divid+'\',\''+data.confirm_msg+'\')">' +
					'</td>' +
					'</tr>');

				// resetto i dati nella riga di inserimento
				$('#New_DivId').val('');
				$('#New_DivIsPara').val('0');
				$('#New_DivDescription').val('');
				$('#New_DivAthlete').val('0');
				$('#New_DivViewOrder').val('');
			}
		});
	}
}

/*
	- UpdateClassAge(Id,FromTo)
	Esegue la post a UpdateClassAge.php.
	Se FromTo vale 'From' significa che si sta aggiornando un AgeFrom.
	Se FromTo vale 'to'  significa che si sta aggiornando un AgeTo
*/

function UpdateClassAge(Id,FromTo) {
	var QueryString
		= 'ClId=' + Id
		+ '&FromTo=' + FromTo
		+ '&Age=' + encodeURIComponent($('#d_ClAge' + FromTo + '_' + Id).val())
		+ '&AlDivs=' + encodeURIComponent($('#d_ClValidDivision_' + Id).val());
	$.getJSON('UpdateClassAge.php?'+QueryString, function(data) {
		var ObjId = 'd_ClAge' + data.fromto + '_' + data.clid;

		if (data.error==1) {
			SetStyle(ObjId,'error');
		} else {
			SetStyle(ObjId,'');
		}
	});
}

/*
	- UpdateValidClass(Id)
*/
function UpdateValidClass(Id) {
	var QueryString
		= 'ClId=' + Id
		+ '&ClList=' + encodeURIComponent(document.getElementById('d_ClValidClass_' + Id).value);
	$.getJSON('UpdateValidClass.php?'+QueryString, function(data) {
		var ObjId = 'd_ClValidClass_' + data.clid;

		if (data.error==1) {
			SetStyle(ObjId,'error');
		} else {
			SetStyle(ObjId,'');
			$('#'+ObjId).val(data.valid);
		}
	});
}

/*
- UpdateValidDivision(Id)
*/
function UpdateValidDivision(Id) {
	var QueryString
		= 'ClId=' + Id
		+ '&ClList=' + encodeURIComponent(document.getElementById('d_ClValidDivision_' + Id).value);
	$.getJSON('UpdateValidDivision.php?'+QueryString, function(data) {
		var ObjId = 'd_ClValidDivision_' + data.clid;

		if (data.error==1) {
			SetStyle(ObjId,'error');
		} else {
			SetStyle(ObjId,'');
			$('#'+ObjId).val(data.valid);
		}
	});
}

/*
	- AddCl(ErrMsg)
	esegue la post a AddCl.php per aggiungere una classe
	ErrMsg è il messaggio di errore
*/
function AddCl() {
	if ($('#New_ClId').val()=='' ||
			$('#New_ClDescription').val()=='' ||
			$('#New_ClViewOrder').val()=='' ||
			$('#New_ClAgeFrom').val()=='' ||
			$('#New_ClAgeTo').val()=='' ) {
		alert(MsgRowMustBeComplete);
	} else {
		var QueryString
			= 'New_ClId=' + encodeURIComponent($('#New_ClId').val())
			+ '&New_ClDescription=' + encodeURIComponent($('#New_ClDescription').val())
			+ '&New_ClIsPara=' + encodeURIComponent($('#New_ClIsPara').val())
			+ '&New_ClAthlete=' + encodeURIComponent($('#New_ClAthlete').val())
			+ '&New_ClViewOrder=' + encodeURIComponent($('#New_ClViewOrder').val())
			+ '&New_ClAgeFrom=' + encodeURIComponent($('#New_ClAgeFrom').val())
			+ '&New_ClAgeTo=' + encodeURIComponent($('#New_ClAgeTo').val())
			+ '&New_ClValidClass=' + encodeURIComponent($('#New_ClValidClass').val())
			+ '&New_ClSex=' + encodeURIComponent($('#New_ClSex').val())
			+ '&New_ClValidDivision=' + encodeURIComponent($('#New_ClValidDivision').val());
		$.getJSON('AddCl.php?'+QueryString, function(data) {
			if (data.error==0) {
				$('#tbody_cl').append('<tr id="Cl_'+data.clid+'">' +
					'<td class="Bold Center">'+data.clid+'</td>' +
					'<td><select name="d_ClSex_'+data.clid+'" id="d_ClSex_'+data.clid+'" onChange="UpdateField(\'C\',\'d_ClSex_'+data.clid+'\');">' +
						'<option value="0"'+(data.clsex==0 ? ' selected="selected"' : '')+'>'+data.male+'</option>' +
						'<option value="1"'+(data.clsex==1 ? ' selected="selected"' : '')+'>'+data.female+'</option>' +
						'<option value="-1"'+(data.clsex==-1 ? ' selected="selected"' : '')+'>'+data.unisex+'</option>' +
						'</select></td>' +
					'<td><input  type="text" name="d_ClDescription_'+data.clid+'" id="d_ClDescription_'+data.clid+'" size="56" maxlength="32" value="'+data.cldescr+'" onBlur="UpdateField(\'C\',\'d_ClDescription_'+data.clid+'\')"></td>' +
					'<td class="Center"><select  name="d_ClIsPara_'+data.clid+'" id="d_ClIsPara_'+data.clid+'"  onClick="UpdateField(\'C\',\'d_ClIsPara_'+data.clid+'\')">' +
						'<option value="0">'+data.no+'</option>' +
						'<option value="1"'+(data.clpara==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
						'</select></td>' +
					'<td class="Center"><select  name="d_ClAthlete_'+data.clid+'" id="d_ClAthlete_'+data.clid+'"  onClick="UpdateField(\'C\',\'d_ClAthlete_'+data.clid+'\')">' +
						'<option value="0">'+data.no+'</option>' +
						'<option value="1"'+(data.clathlete==1 ? ' selected="selected"' : '')+'>'+data.yes+'</option>' +
						'</select></td>' +
					'<td class="Center"><input  type="text" name="d_ClViewOrder_'+data.clid+'" id="d_ClViewOrder_'+data.clid+'" size="3" maxlength="3" value="'+data.clprogr+'" onBlur="UpdateField(\'C\',\'d_ClViewOrder_'+data.clid+'\')"></td>' +
					'<td class="Center"><input  type="text" name="d_ClAgeFrom_'+data.clid+'" id="d_ClAgeFrom_'+data.clid+'" size="3" maxlength="3" value="'+data.clagefrom+'" onBlur="UpdateClassAge(\''+data.clid+'\',\'From\')"></td>' +
					'<td class="Center"><input  type="text" name="d_ClAgeTo_'+data.clid+'" id="d_ClAgeTo_'+data.clid+'" size="3" maxlength="3" value="'+data.clageto+'" onBlur="UpdateClassAge(\''+data.clid+'\',\'To\')"></td>' +
					'<td class="Center"><input  type="text" name="d_ClValidClass_'+data.clid+'" id="d_ClValidClass_'+data.clid+'" size="8" maxlength="24" value="'+data.clvalid+'" onBlur="UpdateValidClass(\''+data.clid+'\')"></td>' +
					'<td class="Center"><input  type="text" name="d_ClValidDivision_'+data.clid+'" id="d_ClValidDivision_'+data.clid+'" size="8" maxlength="255" value="'+data.clvaliddiv+'" onBlur="UpdateValidDivision(\''+data.clid+'\')"></td>' +
					'<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'C\',\''+data.clid+'\')"></td>' +
					'</tr>');

				// resetto i dati nella riga di inserimento
				$('#New_ClId').val('');
				$('#New_ClSex').val(0);
				$('#New_ClDescription').val('');
				$('#New_ClAthlete').val(0);
				$('#New_ClIsPara').val(0);
				$('#New_ClViewOrder').val('');
				$('#New_ClAgeFrom').val('');
				$('#New_ClAgeTo').val('');
				$('#New_ClValidClass').val('');
				$('#New_ClValidDivision').val('');
			} else if(data.error==2) {
				alert(data.errormsg);
			}
		});
	}
}

function AddSubClass() {
	if ($('#New_ScId').val()=='' || $('#New_ScDescription').val()=='' || $('#New_ScViewOrder').val()=='') {
		alert(MsgRowMustBeComplete);
	} else {
		var QueryString
			= 'New_ScId=' + encodeURIComponent($('#New_ScId').val())
			+ '&New_ScDescription=' + encodeURIComponent($('#New_ScDescription').val())
			+ '&New_ScViewOrder=' + encodeURIComponent($('#New_ScViewOrder').val());
		$.getJSON('AddSubCl.php?'+QueryString, function(data) {
			if(data.error==0) {
				$('#tbody_subclass').append('<tr id="SubClass_'+data.scid+'">' +
					'<td class="Bold Center">'+data.scid+'</td>' +
					'<td><input type="text" name="d_ScDescription_'+data.scid+'" id="d_ScDescription_'+data.scid+'" size="56" maxlength="32" value="'+data.scdescr+'" onBlur="UpdateField(\'SC\',\'d_ScDescription_'+data.scid+'\')"></td>' +
					'<td class="Center"><input type="text" name="d_ScViewOrder_'+data.scid+'" id="d_ScViewOrder_'+data.scid+'" size="3" maxlength="3" value="'+data.scprogr+'" onBlur="UpdateField(\'SC\',\'d_ScViewOrder_'+data.scid+'\')"></td>' +
					'<td class="Center"><img src="../Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(\'SC\',\''+data.scid+'\')"></td>' +
					'</tr>');
			} else if (data.error==2) {
				alert(data.errormsg);
			}

			$('#New_ScId').val('');
			$('#New_ScDescription').val('');
			$('#New_ScViewOrder').val('');
		});
	}
}
