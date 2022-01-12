function CheckOrisTeam(obj) {
	var Events=document.getElementById('TeamEvents[]');
	var href='?';
	for(var n=0; n<Events.options.length; n++) {
		if(Events.options.item(n).selected) {
			href+=Events.id+"="+Events.options.item(n).value+'&';
		}
	}
	if(href.length>1) {
		obj.href+=href;
	}
}

function printCountries() {
	var href='OrisCountry.php?SinglePage=1';
	if($('#CoDoB:checked').length>0) {
		href+='&dob=1';
	}
	if($('#CoContacts:checked').length>0) {
		href+='&contacts=1';
	}
	if($('#CoMissing:checked').length>0) {
		href+='&missing=1';
	}
	if($('#CoPictures:checked').length>0) {
		href+='&retake=1';
	}
	window.open(href);
}
