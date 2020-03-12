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