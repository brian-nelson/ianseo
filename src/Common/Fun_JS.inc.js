/*
														- Fun_JS.inc.js -
Contiene le funzioni javascript globali al progetto					
*/

/*
	- SetStyle(Field,NewStyle)
	Modifica lo stile di Field con NewStyle
*/
function SetStyle(Field,NewStyle)
{
	document.getElementById(Field).className=NewStyle;
}

/*
	- OpenPopup(URL,Title,w,h) 
	Apre in un popup l'indirizzo URL con titolo Title e misure w x h
*/
function OpenPopup(URL,Title,w,h) 
{
	alfa=window.open(URL, Title,"scrollbars=yes,toolbar=no,directories=no,status=no,menubar=no,width=" +w +",height="+h);
//	setTimeout(function() {
//		alfa.resizeTo(w, h);
//		alfa.moveTo(5, 5);
//		}, 4/*ms*/);
	alfa.focus();
}

/*
	- SelectAllOpt(Sel)
	Seleziona tutti gli elementi della select multipla Sel
*/
function SelectAllOpt(Sel)
{
	var ss = document.getElementById(Sel);
	
	if (ss)
	{
		var Opt = ss.options;
		for (i=0;i<Opt.length;++i)
			Opt[i].selected=true;
	}
}

/*
	- InsertAfter(newElement,targetElement) 
	Tramite il DOM inserisce NewElement dopo targetElement
*/
function InsertAfter(newElement,targetElement) 
{
	var parent = targetElement.parentNode;
	if (parent.lastChild == targetElement) 
	{
		parent.appendChild(newElement);
	} 
	else 
	{
		parent.insertBefore(newElement,targetElement.nextSibling);
	}
}

/*
	- CheckMail(Mail)
	Verifica che Mail sia un indirizzo email valido
*/
function CheckMail(Mail)
{
	var MyPattern = '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$';
	var Reg = new RegExp(MyPattern);
	
	if (Reg.test(Mail))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/*
	- trim (str)
	Implementa la funzione trim che elimina gli spazi bianchi all'inizio e alla fine della stringa str
*/
function trim (str) 
{
	str = this != window? this : str;
	return str.replace(/^\s+/, '').replace(/\s+$/, '');
}

/*
	- Go2(ref)
	Cambia la pagina con ref
*/
function Go2(ref)
{
	window.location.href=ref;
}

/*
	- SelectAllChecks(frm,cls)
	Seleziona tutti gli elementi checkbox di classe cls della form frm
	@param string frm: id della form
	@param string cls: classe delle checkbox da selezionare
*/
function SelectAllChecks(frm,cls)
{
	var form=document.getElementById(frm);
	
	for (var i=0;i<form.elements.length;++i)
	{
		var el=form.elements[i];
		
		if (el.className==cls)
			if(!el.disabled) el.checked=true;
	}
}

/*
	- UnselectAllChecks(frm,cls)
	Deseleziona tutti gli elementi checkbox di classe cls della form frm
	@param string frm: id della form
	@param string cls: classe delle checkbox da selezionare
*/
function UnselectAllChecks(frm,cls)
{
	var form=document.getElementById(frm);
	
	for (var i=0;i<form.elements.length;++i)
	{
		var el=form.elements[i];
		
		if (el.className==cls)
			el.checked=false;
	}
}

/*
 *  riscrittura di getElementByClassName x usarla anche in IE!!!!
 *  
 *  http://robertnyman.com/2008/05/27/the-ultimate-getelementsbyclassname-anno-2008/
 */

var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};
