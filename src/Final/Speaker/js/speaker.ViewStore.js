Ext.ns('speaker');

speaker.XmlStore=function(config)
{
	var record=Ext.data.Record.create(
	[
	 	{name: 'id', mapping: 'id'},
	 	{name: 'finished', mapping: 'f'},
	 	{name: 'read', mapping: 'r'},
	 	{name: 'target', mapping: 't'},
	 	{name: 'event', mapping: 'ev'},
	 	{name: 'eventName', mapping: 'evn'},
	 	{name: 'name1', mapping: 'n1'},
	 	{name: 'countryName1', mapping: 'cn1'},
	 	{name: 'name2', mapping: 'n2'},
	 	{name: 'countryName2', mapping: 'cn2'},
	 	{name: 'score', mapping: 's'},
	 	{name: 'setPoints1', mapping: 'sp1'},
	 	{name: 'setPoints2', mapping: 'sp2'},
	 	{name: 'lastUpdate', mapping: 'lu'}
	]);
	
	var config=config || {};
	
	Ext.applyIf(config,
	{
		reader: new Ext.data.XmlReader(
		{
			record: 'm'
			,id: 'id'
		},record)
	});
	
	speaker.XmlStore.superclass.constructor.call(this, config);
}

Ext.extend(speaker.XmlStore, Ext.data.Store);
