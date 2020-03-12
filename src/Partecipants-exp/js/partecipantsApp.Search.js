/**
 * Gestione dei partecipanti
 *
 * @package partecipantsApp
 */

Ext.ns('partecipantsApp');
	
/**
 * Griglia di ricerca
 *
 * @param Object p: Applicazione che ha istanziato la classe
 */
partecipantsApp.Search=function(p)
{
	var parent=p;
	
	var win=null;
	
	var formFilter=null;
	
	var gridResults=null;
	
	var recordResults=null;
	var storeResults=null;
	
	var records=new Array();
		records['results']=null;
		records['divisions']=null;
		records['classes']=null;
		
	var stores=new Array();
		stores['results']=null;
		stores['divisions']=null;
		stores['classes']=null;
		
	function setupRecords()
	{
		records['results']=Ext.data.Record.create
		(
			[
			// hidden
				{name: 'id', mapping: 'id'},
				{name: 'status', mapping: 'status'},
			// non-hidden	
				{name: 'code', mapping: 'code'},
				{name: 'archer',mapping:'archer'},
				{name: 'country',mapping:'country'},
				{name: 'division',mapping:'division'},
				{name: 'class',mapping:'class'}
			]
		);
		
		records['divisions']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);		
		
	// Classi anagrafiche
		records['classes']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
	}
	
	function setupStores()
	{
	// Questo non va caricato in automatico
		stores['results']=new Ext.data.Store
		(
			{
				url:'actions/xmlSearch.php',
				autoLoad:false,
				reader:new Ext.data.XmlReader
				(
					{
						record: 'ath',
						id: 'id'
					},
					records['results']
				)
			}
		);	
		
	// Gli altri si
		stores['divisions']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'division',
						id:'id'
					}, 
					records['divisions']
				)
			}
		);
		
		stores['classes']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'class',
						id:'id'
					}, 
					records['classes']
				)
			}
		);
		
		var o =
		{
			url: 'actions/xmlGetSearchCombos.php',
			method:'POST',
			scope:this,
			success: function(response)
			{
				var o = {};
				
	            try 
	            {
	                o = response.responseXML;
	                
	            }
	            catch(e) 
	            {
	                return;
	            }
	        
	      		stores['divisions'].loadData(o);
	      		stores['classes'].loadData(o);
			}
		};
		
		Ext.Ajax.request(o);
	}
	
	function loadStoreResults()
	{
		var params=
		{
			code: Ext.get('gridResults-filter-editor-colCode').getValue(),
			archer: Ext.get('gridResults-filter-editor-colArcher').getValue(),
			country: Ext.get('gridResults-filter-editor-colCountry').getValue(),
			division: Ext.get('gridResults-filter-editor-colDiv').getValue(),
			'class': Ext.get('gridResults-filter-editor-colClass').getValue()
		}
		
		var o=
		{
			params:params
		}
		
		stores['results'].load(o);
	}
	
	function setupFilter()
	{
		var textFilterCode=new Ext.form.TextField
		(
			{
				id: 'gridResults-filter-editor-colCode',
				enableKeyEvents: true,
				listeners:
				{
					keypress: onKeyPressFilter
				}
			}
		);
		var textFilterArcher=new Ext.form.TextField
		(
			{
				id: 'gridResults-filter-editor-colArcher',
				enableKeyEvents: true,
				listeners:
				{
					keypress: onKeyPressFilter
				}
			}
		);
		
		var textFilterCountry=new Ext.form.TextField
		(
			{
				id: 'gridResults-filter-editor-colCountry',
				enableKeyEvents: true,
				listeners:
				{
					keypress: onKeyPressFilter
				}
			}
		);
		
		var comboFilterDiv=new Ext.form.ComboBox
		(
			{
				id:'gridResults-filter-editor-colDiv',
				store: stores['divisions'],
				valueField: 'id',
                displayField: 'val',
                iconClsField: 'icon',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);
		
		comboFilterDiv.setValue('--');
		
		var comboFilterCl=new Ext.form.ComboBox
		(
			{
				id:'gridResults-filter-editor-colClass',
				store: stores['classes'],
				valueField: 'id',
                displayField: 'val',
                iconClsField: 'icon',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);
		
		comboFilterCl.setValue('--');
		
	/* 
	 * il pannello mi serve per wrappare il bottone all'interno
	 * di qualche cosa con le propriet�� di size
	 */
		var panel=new Ext.Panel
		(
			{
				id: 'gridResults-filter-editor-colTools',
				border: false,
				frame: false,
				items:
				[
					new Ext.Button
					(
						{
							text: StrOk,
							handler: loadStoreResults
							
						}
					)
				]
			}
		);
	}
	
	function setupGrid()
	{
		/*
	 * ColumnModel per la griglia. 
	 * Lo definisco qui per comodit�
	 */
		var cm=new Ext.grid.ColumnModel
		(
			{
				columns:
				[
					{
						header:StrStatus, 
						id:'colStatus',
						width: 30,
						dataIndex:'status',
						sortable: true,
						resizable: false,
						hideable:false,
						renderer: statusRenderer
					},
					{
						header:StrCode, 
						id:'colCode',
						//width: 80,
						dataIndex:'code',
						sortable: true
						//renderer: standardRenderer
					},
					{
						header:StrArcher, 
						id:'colArcher',
						//width: 120,
						dataIndex:'archer',
						sortable: true
						//renderer: standardRenderer
					},
					{
						header:StrCountry, 
						id:'colCountry',
						//width: 120,
						dataIndex:'country',
						sortable: true
						//renderer: standardRenderer
					},
					{
						header:StrDiv, 
						id:'colDiv',
						//width: 50,
						dataIndex:'division',
						sortable: true
					},
					{
						header:StrAgeCl, 
						id:'colClass',
						//width: 50,
						dataIndex:'class',
						sortable: true
					},
					{
						header:'', 
						id:'colTools',
						width: 37,
						sortable: false,
						resizable: false,
						hideable:false,
						renderer: function(value)
						{
							return '';
						}
					}
				]
			}
		);
		
		gridResults=new Ext.grid.GridPanel
		(
			{
				id: 'gridResults',
				store: stores['results'],
				cm:cm,
				sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
				viewConfig:
				{
					forceFit: true
				},
				//width: 450,
				height: 500,
				stripeRows:true,	// righe a colori alterni
				plugins: [new Ext.ux.grid.FilterRow()],
				tbar:
				[
					{
						text: StrCmdAdd,
						handler: parent.addArcher
					}
				],
			// eventi
				listeners:
				{
					rowdblclick: onRowDblClickGridResults
				}
			}
		);
		
		setupFilter();
	}
	
	function setupWin()
	{
		win=new Ext.Window
		(
			{
				title: '<div align="center">' + StrSearch + '<div>',
				constrainHeader:true,
				width:800,
				height:600,
				closable:true,
				resizable: true,
				border:true,
				layout: 'fit',
				items: [gridResults],
				listeners:
				{
					close:function()
					{
						parent.setWinSearchNull();
					}
				}
			}
		);
	}
	
/****** Renderizzatori ******/
	
/**
 * Renderizza la colonna status.
 * In base allo stato della persona viene visualizzata l'icona corretta.
 *
 * @param String value: valore della cella
 *
 * @return void
 *
 * @access private
 */
	function statusRenderer(value)
	{
		var img='';
		var title='';
		
		switch (value)
		{
			case '0':
				img='status-ok.gif';
				title=StrOk;
				break;
			case '1':
				img='status-canshoot.gif'; 
				title=Arr_StrStatus[1];
				break;
			case '5':
				img='status-unknown.gif'; 
				title=Arr_StrStatus[5];
				break;
			case '8':
				img='status-couldshoot.gif';
				title=Arr_StrStatus[8];
				break;
			case '9':
				img='status-noshoot.gif';
				title=Arr_StrStatus[9];
				break;
		}
		
		var html
			= '<div class="status">'
				+ '<img src="'+WebDir+'Common/Images/' + img + '" title="' + title + '" width="16" height="16">'
			+ '</div>';
			
		return html;
	}
	
/****** End Renderizzatori ******/
	
/****** Event handlers ******/

	function onKeyPressFilter(field,e)
	{
		if (e.getKey()==e.ENTER)
		{
			loadStoreResults();
		}
	}
	
/**
 * Gestisce il doppio click su una riga.
 * Invia il codice della persona alla griglia principale.
 *
 * @param Ext.grid.GridPanel grid: griglia
 * @param Int row: indice di riga
 * @param Ext.EventObject eventObject: event object
 * @
 * @ return void
 *
 * @access private
 */
	function onRowDblClickGridResults(grid, row, eventObject)
	{
		var code=grid.getStore().getAt(row).get('code');
		
		parent.setCode(code);
	}
	
/****** End Event handlers ******/

/****** Metodi pubblici ******/
	
	this.bootstrap=function()
	{
		setupRecords();
		setupStores();
		setupGrid();
		
		setupWin();
		win.show();
			
	}
	
/****** End Metodi pubblici ******/
}