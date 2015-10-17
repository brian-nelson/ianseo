/**
 * @package Ext.util
 */
 
Ext.namespace('Ext.util');

/**
 * Renderizza una combo in un EditorGridPanel in modo
 * da visulizzare il campo displayField al posto di valueField.
 * Queste due propriet� devono essere impostate nella combo
 *
 * @param Ext.form.ComboBox combo: combo da renderizzare
 *
 * @return una funzione anonima usata per renderizzare. Il parametro value viene passato dal ColumnModel della griglia
 *   ed � il valore valueField in quel momento settato. Ritorna il valore da visualizzare 
 */
Ext.util.comboRenderer=function(combo)
{
	return function(value) 
	{
		
		//var regex=new RegExp('^'  + value + '$');
		
		var idx = combo.store.find(combo.valueField, value,0,true);
		//var idx = combo.store.find(regex, value,0,true);
		
	    var ret="";
	    if (idx!=-1)
	    {
	    	var rec = combo.store.getAt(idx);
	    	ret=rec.get(combo.displayField);
	    }
	    
	    return ret; 
	}
}



/**
 * Produce un messaggio di conferma.
 * Imposta i testi dei bottoni Yes e No in base alla lingua settata
 * 
 * @param config: configurazione di Ext.Msg.show che imposta:
 *     String config.title: titolo
 *     String config.msg: messaggio
 *	   fn config.fn: funzione di callback
 *	   String config.strYes: Stringa localizzata per il Si
 *	   String config.Stringa localizzata per il No
 *
 * @return void
 */
Ext.util.confirm=function(config)
{
	if (config)
	{
		Ext.MessageBox.buttonText.yes=config.strYes;
		Ext.MessageBox.buttonText.no=config.strNo;
		Ext.Msg.show
		(
			{
				title: config.title,
				msg: config.msg,
				buttons: Ext.Msg.YESNO,
				icon: Ext.MessageBox.QUESTION,
				fn:config.fn
			}
		);
	}
}

/**
 * Gestisce l'allocazione dell'oggetto xmlRoot di un response xml.
 * 
 * @param responseXML xmlResp: il response da cui partire
 *
 * @return xmlRoot il nodo root del documento.
 *
 * @throws eccezioni anonime in caso di errore
 */
Ext.util.xmlResponse=function(xmlResp)
{
// intercetto gli errori di IE e Opera
	if (!xmlResp || !xmlResp.documentElement)
		throw('');
		//throw(xmlResp.responseText);
		
// Intercetto gli errori di Firefox
	var xmlRoot;
	if ((xmlRoot = xmlResp.documentElement.nodeName)=="parsererror")
		throw("parser error");
		
	xmlRoot = xmlResp.documentElement;	
	
	return xmlRoot;
}

Ext.util.Buffer=function(ms)
{
	var buffer=new Array();
	var maxSize=-1
	
	if (ms)
		maxSize=ms;
	
	this.push=function(v)
	{
		if (buffer.length<maxSize || maxSize==-1)
		{
		//	console.debug('insert --> ' + v.params.country_code);
			buffer.push(v);
			
			return true;
		}	
		
		return false;
	}
	
	this.shift=function()
	{
		if (buffer.length>0)
		{
			var v=buffer.shift();
			//console.debug('extract --> ' + v.params.country_code);
			return v;
		} 
		
		return false;
	}
	
	this.getSize=function()
	{
		return buffer.length;
	}
	
	this.reset=function()
	{
		buffer=new Array();
	}
}

Ext.util.storeData2Xml= function(data,itemFn)
{
	var xml='<?xml version="1.0"?>';
	
	xml+='<response>';
	
		Ext.each(data.items,function(item)
		{
			xml+=itemFn(item);
		});
	
	xml+='</response>';
	
	return (new DOMParser()).parseFromString(xml, "text/xml");
}