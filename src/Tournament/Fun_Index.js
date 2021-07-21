function ChangeTourType(who) {
	var combo=document.getElementById('d_ToType');
	var subrule=document.getElementById('d_SubRule');

	while(combo.options.length>0) combo.remove(0);
	while(subrule.options.length>0) subrule.remove(0);
	document.getElementById('rowSubRule').style.display='none';

	if(who && ToTypes[who]) {
		var morethan1=0;
		for(n in ToTypes[who]['ordered_types']) {
			var tourType = ToTypes[who]['ordered_types'][n]
			morethan1++;
			y=document.createElement('option');
			y.value=tourType.type;
			y.text=tourType.name;
			try {
				combo.add(y,null); // standards compliant
			} catch(ex) {
				combo.add(y); // IE only
			}
		}
		if(morethan1>1) {
			y=document.createElement('option');
			y.value='';
			y.text='--';
			try {
				combo.add(y,combo.options[0]); // standards compliant
			} catch(ex) {
				combo.add(y,1); // IE only
			}
			combo.selectedIndex=0;
		}
		ChangeLocalSubRule(combo.value);
	}
}

function ChangeLocalSubRule(who) {
	var local=document.getElementById('d_Rule').value;
	var subrule=document.getElementById('d_SubRule');

	$('#d_SubRule').empty();

	document.getElementById('rowSubRule').style.display='none';

	if(ToTypes[local]['rules'][who]) {
		$.each(ToTypes[local]['rules'][who], function(idx) {
			$('#d_SubRule').append('<option value="'+idx+'">'+this+'</option>');
		});
		document.getElementById('rowSubRule').style.display='table-row';
	}
}

function ChangeIskConfig() {
    $.getJSON('index-getIskConfig.php?api='+$('#IskSelect').val(), function (data) {
        $('#IskConfig').html(data.html);
    });
}

function ChangeLookUpCombo() {
	if($('#d_ToIocCode').val()==$('#oldToIocCode').val()) {
		$('#cmdAssignLookup').show();
	} else {
        $('#cmdAssignLookup').hide();
	}
}

function assignCurrentLookUp() {
	$('#Command').val('AssignLookupEntry');
	$('#Frm').submit();
}
