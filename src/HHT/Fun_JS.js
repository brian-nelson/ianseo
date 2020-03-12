/* javascript */
String.prototype.get = function(p){
    return (match = this.match(new RegExp("[?|&]?" + p + "=([^&]*)"))) ? match[1] : 0;
}

function UpdateLinks(checked, Form) {
	if(Form == undefined) Form='FrmSetup';
	get='';
	if(checked) {
		for(n=0; n< document.getElementById(Form).elements.length; n++) {
			hht=document.getElementById(Form).elements[n];
			if(hht.type=='checkbox' && hht.checked) {
				get+='&'+hht.name+'='+hht.value;
			}
		}
	}
	if(document.getElementById('HhtNextPage')) {
		href=document.getElementById('HhtNextPage').href;
		tmp=href.split('?');
		page=tmp[0];
		get='?propagate='+(checked?1:0)+'&x_Hht='+href.get('x_Hht')+'&x_Session='+href.get('x_Session')+get;
		document.getElementById('HhtNextPage').href = page + get;
	}
	if(document.getElementById('HhtPrevPage')) {
		href=document.getElementById('HhtPrevPage').href;
		tmp=href.split('?');
		page=tmp[0];
		get='?propagate='+(checked?1:0)+'&x_Hht='+href.get('x_Hht')+'&x_Session='+href.get('x_Session')+get;
		document.getElementById('HhtPrevPage').href = page + get;
	}
}

function resetCmbSession()
{
	if(document.getElementById('HhtSearchSession'))
	{
		document.getElementById('HhtSearchSession').innerHTML='';
		if(document.getElementById('HhtSearchResult'))
			document.getElementById('HhtSearchResult').innerHTML='';
	}
}

function incSeq()
{
	var firstArr=parseInt(document.getElementById('firstArr').value,10);
	var lastArr=parseInt(document.getElementById('lastArr').value,10);
	var volee=parseInt(document.getElementById('volee').value,10);
	
	var gap=lastArr-firstArr;
	
	firstArr += gap+1;
	lastArr += gap+1;
	++volee;
	
// per non sforare con le lunghezze
	if (firstArr>99 || lastArr>99 || volee>99)
		return;
	
	document.getElementById('firstArr').value=firstArr;
	document.getElementById('lastArr').value=lastArr;
	document.getElementById('volee').value=volee;
	
	// resets the colors of the targets
	resetColors();
}

function decSeq()
{
	var firstArr=parseInt(document.getElementById('firstArr').value,10);
	var lastArr=parseInt(document.getElementById('lastArr').value,10);
	var volee=parseInt(document.getElementById('volee').value,10);
	
	var gap=lastArr-firstArr;
	
	firstArr-=gap+1;
	lastArr-=gap+1;
	--volee;
	
// per non avere cagate negative o a zero
	if (firstArr<1 || lastArr<1 || volee<1)
		return;
	
	document.getElementById('firstArr').value=firstArr;
	document.getElementById('lastArr').value=lastArr;
	document.getElementById('volee').value=volee;
	
	
	// resets the colors of the targets
	resetColors();
}

function resetColors() {
	var Divs=document.getElementsByTagName("div");
	for (n=0; n<Divs.length; n++) {
		if(Divs[n].className.indexOf("htt_letter")==0) {
			Divs[n].className="htt_letter";
		}
	}
	
}