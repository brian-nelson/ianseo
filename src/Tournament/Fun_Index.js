function ChangeTourType(who) {
	var combo=document.getElementById('d_ToType');
	var subrule=document.getElementById('d_SubRule');
	
	while(combo.options.length>0) combo.remove(0);
	while(subrule.options.length>0) subrule.remove(0);
	document.getElementById('rowSubRule').style.display='none';
	
	if(who && ToTypes[who]) {
		var morethan1=0;
		for(n in ToTypes[who]['types']) {
			morethan1++;
			y=document.createElement('option');
			y.value=n;
			y.text=ToTypes[who]['types'][n];
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
	
	while(subrule.options.length>0) subrule.remove(0);
	
	document.getElementById('rowSubRule').style.display='none';
	
	if(ToTypes[local]['rules'][who]) {
		for(n in ToTypes[local]['rules'][who]) {
			y=document.createElement('option');
			y.value=n;
			y.text=ToTypes[local]['rules'][who][n];
			try {
				subrule.add(y,null); // standards compliant
			} catch(ex) {
				subrule.add(y); // IE only
			}
		}
		document.getElementById('rowSubRule').style.display='table-row';
	}
}