/**
 * Gestione dei partecipanti
 *
 * @package partecipantsApp
 */
Ext.ns('partecipantsApp');
	
partecipantsApp.ComboDivCl=Ext.extend(Ext.form.ComboBox,
{
	initComponent: function()
	{
		partecipantsApp.ComboDivCl.superclass.initComponent.call(this);
	}	// end initComponent
	,getValue: function()
	{
		var v=partecipantsApp.ComboDivCl.superclass.getValue.call(this);
		if (v.length<2)
			v=' ' + v;
		
		return v;
	}
});

/**
 * Griglia dei partecipanti al torneo
 *
 * @param String el: div in cui renderizzare la griglia
 */
partecipantsApp.Partecipants=function(el)
{
/**
 * Istanza dell'app.
 * Dato che this prende lo scope dell'oggetto in cui si trova, salvando qui
 * sono sicuro al 100% di avere un riferimento all'istanza
 *
 * @access private
 */
	var me=this;
/**
 * Griglia dei partecipanti
 *
 * @access private
 */
	var gridPartecipants=null;	 	
	
/**
 * Div html in cui rederizzare la griglia
 *
 * @access private
 */
	var renderTo=el;
	
	
/**
 * Finestra di ricerca.
 * Verrà riportata a null al close della stessa.
 * 
 * @access private
 */
	var winSearch=null;
	
/**
 * Finestra della gestione foto
 * Verrà riportata a null al close della stessa.
 * 
 * @access private
 */
	var winPhoto=null;
	
/**
 * Finestra per la fgestione dell'accesso agli eventi
 * Verrà riportata a null al close della stessa.
 * 
 * @access private
 */
	var winEventsAccess=null;
		
/**
 * Records usati
 *
 * @access private
 */
	var records=new Array();
		records['partecipant']=null;		// partecipanti
		records['comboSession']=null;		// sessioni 
		records['comboDiv']=null;			// divisioni
		records['comboAgeClass']=null;		// classi anagrafiche
		records['comboClass']=null;			// classi gara
		records['comboSubClass']=null;		// categorie
		records['comboArcher']=null;		// arciere sì/no
		records['comboGender']=null;		// sesso m/f
		
	var recordStatus=null;
		
/**
 * Numero totale di records.
 * Mi serve perch� javascript non ritorna la lunghezza dei vettori associativi
 * Parte da -1 perch� dopo l'ultimo elemento mi ritrovo un 'remove' che non devo contare
 *
 * @access private
 */
	var totalRecords=(Ext.isIE ? -2 :-1);
	for (var r in records)
		++totalRecords;
	
	
/** 
 * Vettore degli stores.
 * Viene caricato man mano che vengono caricati gli stores usati
 *
 * @access private
 */
	var stores=new Array();
	
	var storeStatus=null;
	
/**
 * Contatore degli stores caricati
 * Vale totalRecords se tutti gli stores sono stati  caricati
 *
 * @access private
 */
	var loadedStores=0;
	
/**
 * Buffer per memorizzare l'oggetto request per le chiamate a xmlFindCode.php
 *
 * @access private
 */
	var findCodeBuffer=new Ext.util.Buffer();
	
/**
 * Buffer per memorizzare l'oggetto request per le chiamate a xmlFindCountryCode.php
 *
 * @access private
 */
	var findCountryCodeBuffer=new Ext.util.Buffer();
	
/**
 * Buffer per memorizzare l'oggetto request per le chiamate a xmlFindCountryCode.php (2)
 *
 * @access private
 */
	var findCountryCode2Buffer=new Ext.util.Buffer();
	
/**
 * Buffer per memorizzare l'oggetto request per le chiamate a xmlFindCountryCode.php (3)
 *
 * @access private
 */
	var findCountryCode3Buffer=new Ext.util.Buffer();
	
/**
 * Men� contestuale per il cambio di stato della persona
 *
 * @access private
 */
	var contextMenuStatus=new Ext.menu.Menu
	(
		{
			id: 'contextMenuStatus',
			shadow: false,
			items:
			[
				{
					id: 'menuStatusItem_1',
					text: Arr_StrStatus[1],
					//iconCls: 'context-menu-canshoot',
					icon: WebDir+'Common/Images/status-canshoot.gif',
					handler: onClickContextMenuStatus
				},	
				{
					id: 'menuStatusItem_8',
					text: Arr_StrStatus[8],
					//iconCls: 'context-menu-couldshoot',
					icon: WebDir+'Common/Images/status-couldshoot.gif',
					handler: onClickContextMenuStatus
				}
			]
		}
	);
	
	var filters=new Array();
	
	var lastSession='0';
	
/**
 * Imposta i records
 *
 * @return void
 *
 * @access private
 */
	function setupRecords()
	{
	// Griglia
		records['partecipant']=Ext.data.Record.create
		(
			[
			// hidden
				{name: 'errors', mapping: 'errors'},	// fittizio
				{name: 'id', mapping: 'id'},
				{name: 'sex', mapping: 'sex'},
				{name: 'country_id',mapping:'country_id'},
				{name: 'country_id2',mapping:'country_id2'},
				{name: 'country_id3',mapping:'country_id3'},
				{name: 'age_class_editable',mapping:'age_class_editable'},
			// non-hidden	
				{name: 'status', mapping: 'status'},
				{name: 'has_photo', mapping: 'has_photo'},
				{name: 'athlete', mapping: 'athlete'},
				{name: 'session', mapping: 'session'},
				{name: 'target_no', mapping: 'target_no'},
				{name: 'code', mapping: 'code'},
				{name: 'first_name',mapping:'first_name'},
				{name: 'name',mapping:'name'},
				//{name: 'ctrl_code',mapping:'ctrl_code'},
				{name: 'ctrl_code',mapping:'dob'},
				{name: 'sex',mapping:'sex'},
				{name: 'country_code',mapping:'country_code'},
				{name: 'country_name',mapping:'country_name'},
				{name: 'country_code2',mapping:'country_code2'},
				{name: 'country_name2',mapping:'country_name2'},
				{name: 'country_code3',mapping:'country_code3'},
				{name: 'country_name3',mapping:'country_name3'},
				{name: 'sub_team',mapping:'sub_team'},
				{name: 'division',mapping:'division'},
				{name: 'age_class',mapping:'age_class'},
				{name: 'class',mapping:'class'},
				{name: 'sub_class',mapping:'sub_class'}
			]
		);
		
	// Sessioni
		records['comboSession']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
		
	// Divisioni
		records['comboDiv']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);		
		
	// Classi anagrafiche
		records['comboAgeClass']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'},
				{name: 'valid',mapping:'valid'}
			]
		);
	
	// Classi gara
		records['comboClass']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
	
	// Categorie
		records['comboSubClass']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
		
	// arciere si/no
		records['comboArcher']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
		
	// sesso m/f
		records['comboGender']=Ext.data.Record.create
		(
			[
				{name: 'id',mapping:'id'},
				{name: 'val',mapping:'val'}
			]
		);
		
	// status per il filtro
		recordStatus=Ext.data.Record.create
		(
			[
				{name: 'id', mapping: 'id'},
				{name: 'val', mapping: 'val'},
				{name: 'icon', mapping: 'icon'}
			]
		);
	}
	
/**
 * Imposta gli stores
 *
 * @return void
 *
 * @access private
 */
	function setupStores()
	{
	// griglia
		stores['partecipant']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record: 'ath',
						id: 'id'
					},
					records['partecipant']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
	// Sessioni
		stores['comboSession']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'session',
						id:'id'
					}, 
					records['comboSession']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
	// Divisioni
		stores['comboDiv']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'division',
						id:'id'
					}, 
					records['comboDiv']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
	// Classi anagrafiche
		stores['comboAgeClass']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'class',
						id:'id'
					}, 
					records['comboAgeClass']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
		
	// Classi gara
		stores['comboClass']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'class',
						id:'id'
					}, 
					records['comboClass']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
		
	// Categorie
		stores['comboSubClass']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'sub_class',
						id:'id'
					}, 
					records['comboSubClass']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
		
	// arcieri si/no
		stores['comboArcher']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'archer',
						id:'id'
					}, 
					records['comboArcher']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
		
	// sesso
		stores['comboGender']=new Ext.data.Store
		(
			{
				reader:new Ext.data.XmlReader
				(
					{
						record:'gender',
						id:'id'
					}, 
					records['comboGender']
				),
				listeners:
				{
					load: onLoadStores
				}
			}
		);
		
	// status per il filtro
		storeStatus=new Ext.data.Store
		(
			{
				
				reader:new Ext.data.XmlReader
				(
					{
						record:'flag',
						id:'id'
					}, 
					recordStatus
				)
			}
		);
	}
	
	function createContextMenuFreeTargets(targets,rec)
	{
		var menu=new Ext.menu.Menu
		(
			{
				id: 'contextMenuFreeTargets'
			}
		);
	
	// aggiungo gli item al men�
		for (var i=0;i<targets.length;++i)
		{
			var item=new Ext.menu.Item
			(
				{
					id: 'menu_' + targets.item(i).firstChild.data,
					text: targets.item(i).firstChild.data,
					icon: WebDir+'Common/Images/target.jpeg',
					handler: function(item,eventObject)
					{
						var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
						var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
						var target_no=item.id.split('_')[1]
						                                 
						rec.set('target_no',target_no);	
						unsetError(rec,col);
						
						var params=
						{
							id: rec.get('id'),
							session: rec.get('session'),
							target: target_no,
							//row: gridPartecipants.getSelectionModel().getSelectedCell()[0],
							//col: gridPartecipants.getSelectionModel().getSelectedCell()[1]
							row: row,
							col: col
						};
						
						var o=
						{
							url: 'actions/xmlUpdateTargetNo.php',
							method: 'POST',
							params: params
						}
						
						Ext.Ajax.request(o);
					}
				}
			);
			
			menu.add(item);
		}
		
		return menu;			
	}
	
/**
 * Carica i dati negli stores
 *
 * @return void
 *
 * @access private
 */	
	function loadStores()
	{
		var o =
		{
			url: 'actions/xmlGetRows.php',
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
	        
	      		stores['partecipant'].loadData(o);
	      		stores['comboSession'].loadData(o);
	      		stores['comboDiv'].loadData(o);
	      		stores['comboAgeClass'].loadData(o);
	      		stores['comboClass'].loadData(o);
	      		stores['comboSubClass'].loadData(o);
	      		stores['comboArcher'].loadData(o);
	      		stores['comboGender'].loadData(o);
	      		
	      		//console.debug(stores['comboGender'])
	      		
	      		storeStatus.loadData(o);
			}
		};
		
		Ext.Ajax.request(o);
	}
	
	function setupGridFilter()
	{
		// num di colonne della griglia
		var cm=gridPartecipants.getColumnModel();
		
		var cc=cm.getColumnCount();
		
	// inizializzo il vettore di supporto ai filtri
		for (i=0;i<cm.getColumnCount();++i)
		{
			filters[i]='';
		}
		
	// filtri
		/*var comboFilterStatus=new Ext.ux.IconCombo
		(
			{
				id:'grid-filter-editor-colStatus',
				cls: 'filter-status',
				store: storeStatus,
				valueField: 'id',
                displayField: 'val',
                iconClsField: 'icon',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);*/
	/*	comboFilterStatus.addListener
		(
			'select',
			function(combo,record)
			{
				makeFilter(combo.getValue(),cm.findColumnIndex('status'));
			},
			this
		);*/
		
		var comboFilterArcher=new Ext.form.ComboBox
		(
			{
				id:'grid-filter-editor-colAthlete',
				cls: 'filter-athlete',
				store: stores['comboArcher'],
				valueField: 'id',
                displayField: 'val',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);
		/*comboFilterArcher.addListener
		(
			'select',
			function(combo,record)
			{
				makeFilter(combo.getValue(),cm.findColumnIndex('athlete'));
			},
			this
		);*/
		
		var tmpStore=stores['comboGender'];
		var r=new records['comboGender']({id: '',val: ''});
		tmpStore.insert(0,r);
		
		var comboFilterGender=new Ext.form.ComboBox
		(
			{
				id:'grid-filter-editor-colGender',
				cls: 'filter-sex',
				store: tmpStore,
				valueField: 'id',
                displayField: 'val',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);
		

		/*var r=new records['comboGender']({id: '',val: ''});
		comboFilterGender.getStore().insert(0,r);*/
		
	
		
		/*var comboFilterPhoto=new Ext.ux.IconCombo
		(
			{
				id:'grid-filter-editor-colPhoto',
				cls: 'filter-has_photo',
				store: new Ext.data.SimpleStore
				(
					{
						fields: ['id','val','icon'],
						data:
						[
							['','',''],
							['0','','x-icon-photo-no'],
							['1','','x-icon-photo-yes']
						]
					}
				),
				valueField: 'id',
                displayField: 'val',
                iconClsField: 'icon',
                triggerAction: 'all',
                mode: 'local',
                editable: false
			}
		);*/
		/*comboFilterPhoto.addListener
		(
			'select',
			function(combo,record)
			{
				makeFilter(combo.getValue(),cm.findColumnIndex('has_photo'));
			},
			this
		);*/
							
		var textFilterSession=new Ext.form.TextField({id: 'grid-filter-editor-colSession',cls: 'filter-session',enableKeyEvents: true});
			//textFilterSession.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('session'));},this,{buffer: 300});
	
		var textFilterTarget=new Ext.form.TextField({id: 'grid-filter-editor-colTarget',cls: 'filter-target_no',enableKeyEvents: true});
			//textFilterTarget.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('target_no'));},this,{buffer: 300});
			
		var textFilterCode=new Ext.form.TextField({id: 'grid-filter-editor-colCode',cls: 'filter-code',enableKeyEvents: true});
			//textFilterCode.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('code'));},this,{buffer: 300});
			
		var textFilterFamilyName=new Ext.form.TextField({id: 'grid-filter-editor-colFamilyName',cls: 'filter-first_name',enableKeyEvents: true});
			//textFilterFamilyName.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('first_name'));},this,{buffer: 300});
			
		var textFilterName=new Ext.form.TextField({id: 'grid-filter-editor-colName',cls: 'filter-name',enableKeyEvents: true});
			//textFilterName.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('name'));},this,{buffer: 300});
			
		var textFilterCtrlCode=new Ext.form.TextField({id: 'grid-filter-editor-colCtrlCode',cls: 'filter-ctrl_code',enableKeyEvents: true});
			//textFilterCtrlCode.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('ctrl_code'));},this,{buffer: 300});
			
		var textFilterCountryCode=new Ext.form.TextField({id: 'grid-filter-editor-colCountryCode',cls: 'filter-country_code',enableKeyEvents: true});
			//textFilterCountryCode.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('country_code'));},this,{buffer: 300});
			
		var textFilterCountryName=new Ext.form.TextField({id: 'grid-filter-editor-colCountryName',cls: 'filter-country_name',enableKeyEvents: true});
			//textFilterCountryName.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('country_name'));},this,{buffer: 300});
	
		var textFilterSubTeam=new Ext.form.TextField({id: 'grid-filter-editor-colSubTeam',cls: 'filter-sub_team',enableKeyEvents: true});
		var textFilterCountryCode2=new Ext.form.TextField({id: 'grid-filter-editor-colCountryCode2',cls: 'filter-country_code2',enableKeyEvents: true});
		var textFilterCountryName2=new Ext.form.TextField({id: 'grid-filter-editor-colCountryName2',cls: 'filter-country_name2',enableKeyEvents: true});
		
		var textFilterDiv=new Ext.form.TextField({id: 'grid-filter-editor-colDiv',cls: 'filter-division',enableKeyEvents: true});
			//textFilterDiv.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('division'));},this,{buffer: 300});		
	
		var textFilterAgeClass=new Ext.form.TextField({id: 'grid-filter-editor-colAgeClass',cls: 'filter-age_class',enableKeyEvents: true});
			//textFilterAgeClass.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('age_class'));},this,{buffer: 300});
		
		var textFilterClass=new Ext.form.TextField({id: 'grid-filter-editor-colClass',cls: 'filter-class',enableKeyEvents: true});
			//textFilterClass.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('class'));},this,{buffer: 300});
			
		var textFilterSubClass=new Ext.form.TextField({id: 'grid-filter-editor-colSubClass',cls: 'filter-sub_class',enableKeyEvents: true});
			//textFilterSubClass.addListener('keyup',function(field,eventObject){makeFilter(field.getValue(),cm.findColumnIndex('sub_class'));},this,{buffer: 300});
			
	/* 
	 * il pannello mi serve per wrappare il bottone all'interno
	 * di qualche cosa con le proprietà di size
	 */
		var panel=new Ext.Panel
		(
			{
				id: 'grid-filter-editor-colTools',
				border: false,
				frame: false,
				items:
				[
					new Ext.Button
					(
						{
							text: '<b>*</b>',
							handler: function()
							{
								Ext.each
								(
									//Ext.query('.filter'),
									Ext.query('*[class*=filter]'),
									function(el)
									{
										//console.debug(el);
										var col=Ext.getCmp(el.id).initialConfig.cls.split('-')[1];
										//console.debug(Ext.get(el.id).getValue());
										makeFilter(Ext.getCmp(el.id).getValue(),cm.findColumnIndex(col));
										
									}
								);
								execFilter();
							//	gridPartecipants.getStore().clearFilter();
								
							}
						}
					)
				]
			}
		);
	}
	
/**
 * Imposta la griglia
 *
 * @return void
 *
 * @access private
 */
	function setupGrid()
	{
	// Preparo le combo
		var comboSession=new Ext.form.ComboBox
		(
			{
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				store: stores['comboSession'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}	
		);
		
		var comboDiv=new Ext.form.ComboBox
		(
			{
				cls: 'comboClass',
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				store: stores['comboDiv'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}
		);
		
		var comboAgeClass=new Ext.form.ComboBox
		(
			{
				cls: 'comboAgeClass',
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				store: stores['comboAgeClass'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}
		);
		
	/*
	 * Al rendering della riga la combo ha lo store identico a quello della comboAgeClass.
	 * Lo store viene filtrato quando si edita la cella, in base a quello che contiene age_class
	 */
		var comboClass=new Ext.form.ComboBox
		(
			{
				cls: 'comboClass',
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				lastQuery: '',
				store: stores['comboClass'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}
		);
		
		var comboSubClass=new Ext.form.ComboBox
		(
			{
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				store: stores['comboSubClass'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}
		);
		
		var comboArcher=new Ext.form.ComboBox(
		{
			typeAhead: false,
			lazyRender: true,
			triggerAction:"all",
			store: stores['comboArcher'],
			mode:'local',			// importante!!
			valueField: 'id',  
			displayField: 'val',
			editable:false,
			forceSelection: true
		});
		
		var comboGender=new Ext.form.ComboBox(
		{
			typeAhead: false,
			lazyRender: true,
			triggerAction:"all",
			store: stores['comboGender'],
			mode:'local',			// importante!!
			valueField: 'id',  
			displayField: 'val',
			editable:false,
			forceSelection: true
		});
		
	
		
	/*
	 * L'editor della matricola e quello del codice società li definisco qui
	 * in modo da poter usare addListener per agganciare 
	 * gli eventi ed in particolare per poter usare l'opzione buffer
	 * per il delay dell'invocazione degli stessi.
	 */
	 // Matricola
		var textCode=new Ext.form.TextField
		(
			{
				allowBlank:true,
				enableKeyEvents:true
			}
		);
		textCode.addListener('keyup',onKeyUpTextCode,this,{buffer:800});

	// Codice società
		var textCountryCode=new Ext.form.TextField
		(
			{
				allowBlank:true,
				enableKeyEvents:true
			}
		);
		textCountryCode.addListener('keyup',onKeyUpTextCountryCode,this,{buffer:800});	
		
		var textCountryCode2=new Ext.form.TextField
		(
			{
				allowBlank:true,
				enableKeyEvents:true
			}
		);
		textCountryCode2.addListener('keyup',onKeyUpTextCountryCode2,this,{buffer:800});	
		
		var textCountryCode3=new Ext.form.TextField
		(
			{
				allowBlank:true,
				enableKeyEvents:true
			}
		);
		textCountryCode3.addListener('keyup',onKeyUpTextCountryCode3,this,{buffer:800});	
		
	/*
	 * ColumnModel per la griglia. 
	 * Lo definisco qui per comodità
	 */
		var cm=new Ext.grid.ColumnModel
		(
			{
				columns:
				[
					{
						header:StrStatus, 
						id:'colStatus',
						width: 50,
						dataIndex:'status',
						sortable: true,
						hideable:false,
						renderer: statusRenderer
					},
					{
						header:StrArcher, 
						id:'colAthlete',
						width: 50,
						dataIndex:'athlete',
						sortable: true,
						hideable:false,
						editor:comboArcher,
						renderer: comboRenderer(comboArcher)
					},
					{
						header:StrPhoto, 
						id:'colPhoto',
						width: 50,
						dataIndex:'has_photo',
						sortable: true,
						hideable:false,
						renderer: photoRenderer
					},
					{
						header:StrEvents, 
						id:'colEvents',
						width: 50,
						dataIndex: 'events',
						sortable: false,
						hideable:false,
						renderer: eventsRenderer
					},
					{
						header:StrSession, 
						id:'colSession',
						width: 50,
						dataIndex:'session',
						sortable: true,
						editor:comboSession,
						renderer:comboRenderer(comboSession)
					},
					{
						header:StrTarget, 
						id:'colTarget',
						width: 40,
						dataIndex:'target_no',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrCode, 
						id:'colCode',
						//width: 80,
						dataIndex:'code',
						sortable: true,
						editor:textCode,
						renderer: standardRenderer
					},
					{
						header:StrFamilyName, 
						id:'colFamilyName',
						//width: 120,
						dataIndex:'first_name',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrName, 
						id:'colName',
						//width: 120,
						dataIndex:'name',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrDOB, 
						id:'colCtrlCode',
						//width: 120,
						dataIndex:'ctrl_code',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrGender, 
						id:'colGender',
						//width: 50,
						dataIndex:'sex',
						sortable: true,
						editor:comboGender,
						renderer:comboRenderer(comboGender)
					},
					{
						header:StrCountry, 
						id:'colCountryCode',
						//width: 80,
						dataIndex:'country_code',
						sortable: true,
						editor: textCountryCode,
						renderer: standardRenderer
					},
					{
						header:StrNationShort, 
						id:'colCountryName',
						//width: 120,
						dataIndex:'country_name',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrSubTeam, 
						id:'colSubTeam',
						//width: 80,
						dataIndex:'sub_team',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					{
						header:StrCountry + ' (2)', 
						id:'colCountryCode2',
						//width: 80,
						dataIndex:'country_code2',
						sortable: true,
						editor: textCountryCode2,
						renderer: standardRenderer
					},
					{
						header:StrNationShort + ' (2)', 
						id:'colCountryName2',
						//width: 120,
						dataIndex:'country_name2',
						sortable: true,
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					
					{
						header:StrCountry + ' (3)', 
						id:'colCountryCode3',
						//width: 80,
						dataIndex:'country_code3',
						sortable: true,
						hidden: true,	// parte nascosta
						editor: textCountryCode3,
						renderer: standardRenderer
					},
					{
						header:StrNationShort + ' (3)', 
						id:'colCountryName3',
						//width: 120,
						dataIndex:'country_name3',
						sortable: true,
						hidden: true,	// parte nascosta
						editor: new Ext.form.TextField
						(
							{
								allowBlank:true,
								enableKeyEvents:true
							}
						),
						renderer: standardRenderer
					},
					
					{
						header:StrDiv, 
						id:'colDiv',
						//width: 50,
						dataIndex:'division',
						sortable: true,
						editor:comboDiv,
						renderer:comboRenderer(comboDiv)
					},
					{
						header:StrAgeCl, 
						id:'colAgeClass',
						//width: 50,
						dataIndex:'age_class',
						sortable: true,
						editor:comboAgeClass,
						renderer:comboRenderer(comboAgeClass)
					},
					{
						header:StrCl, 
						id:'colClass',
						//width: 50,
						dataIndex:'class',
						sortable: true,
						editor:comboClass,
						renderer:comboRenderer(comboClass)
					},
					{
						header:StrSubCl, 
						id:'colSubClass',
						width: 60,
						dataIndex:'sub_class',
						sortable: true,
						editor:comboSubClass,
						//resizable: false,
						renderer:comboRenderer(comboSubClass)
					},
					{
						header: '',
						width: 33,
						id: 'colTools',
						dataIndex: 'tools',
						sortable: false,
						resizable: false,
						hideable:false,
						renderer: toolsRenderer
					}
				],
			/**
			 * Override di isCellEditable
			 * @param Int col: indice di colonna della cella
			 * @param Int row: indice di riga della cella
			 */
				isCellEditable:function(col,row)
				{
					var dataIndex=this.getDataIndex(col);
						
				// A seconda della colonna decido se la cella è editabile oppure no
					switch (dataIndex)
					{
						case 'age_class':
							return (stores['partecipant'].getAt(row).get('age_class_editable')==1);
							break;
						
						case 'target_no':
							return (stores['partecipant'].getAt(row).get('session')!=0)
							//return false;
							break;
						
						case 'status':
							return false;
							break;
							
						case 'has_photo':
							return false;
							break;
							
						case 'events':
							return false;
							break;
						
						case 'tools':
							return false;
						
						default:
							return true;
					}
				}
			}
		);
			
	// Preparo la griglia	
		gridPartecipants=new Ext.grid.EditorGridPanel
		(
			{
				id: 'grid',
				store: stores['partecipant'],
				cm:cm,
				title: '<div align="center">' + StrTourPartecipants + '</div>',
				viewConfig:
				{
					forceFit: true
					,getRowClass: function(record, rowIndex, rp, ds){ // rp = rowParams
				        return 'row-speaker';
				    }
				},
				//width: 450,
				height: 648,
				clicksToEdit:1,
				stripeRows:true,	// righe a colori alterni
				plugins: [new Ext.ux.grid.FilterRow()],
			// toolbar
				tbar:
				[
					{
						text: StrCmdAdd,
						cls: 'btn-tbar',
						handler: onClickBtnAdd
					},
					{
						text: StrSearch,
						cls: 'btn-tbar',
						handler: onClickBtnSearch
					},
					{
						id: 'btnBlockFill',
						cls: 'btn-tbar',
						text: StrBlockFill,
						enableToggle: true
					},
					{
						id: 'btnHideColsGroup_0',
						cls: 'btn-tbar hideGroup0',
						text: StrHideGroup,
						enableToggle: true,
						handler: onClickHideBtns(0)
					},
					{
						id: 'btnHideColsGroup_1',
						cls: 'btn-tbar',
						text: StrHideGroup,
						enableToggle: true,
						handler: onClickHideBtns(1)
					},
					{
						id: 'btnHideColsGroup_2',
						cls: 'btn-tbar',
						text: StrHideGroup,
						enableToggle: true,
						handler: onClickHideBtns(2)
					},
					{
						id: 'btnHideColsGroup_3',
						cls: 'btn-tbar x-btn-pressed',
						text: StrHideGroup,
						enableToggle: true,
						handler: onClickHideBtns(3)
					}

				],
			// eventi
				listeners:
				{
					click: onClickGridPartecipants,
					celldblclick: onCellDblClickGridPartecipants,
					cellcontextmenu: onCellContextMenuGridPartecipants,
					beforeedit: onBeforeEditGridPartecipants,
					afteredit: onAfterEditGridPartecipants
				}
			}
		);
		
		var comboStatus=new Ext.form.ComboBox
		(
			{
				typeAhead: true,		
				lazyRender:   true,		
				triggerAction:"all",
				lastQuery: '',
				store: stores['comboClass'],
				mode:'local',			// importante!!
				valueField: 'id',  
				displayField: 'val',
				editable:false,
				forceSelection: true
        	}
		);
		
		setupGridFilter();
	}
	
	function makeFilter(value,col)
	{
		var cm=gridPartecipants.getColumnModel();
		filters[col]=value;	// aggiungo il criterio al vettore
	}
	
	function execFilter()
	{		
		var cm=gridPartecipants.getColumnModel();
		//filters[col]=value;	// aggiungo il criterio al vettore
		//console.debug(filters);
	// resetto i criteri precedenti
		gridPartecipants.getStore().clearFilter();
		
	// se ci sono criteri nuovi riapplico il filtro
		if (filters.toString()!='')
		{
			//console.debug('qui');
			gridPartecipants	// estraggo dalla griglia
				.getStore()		// lo store
					.filterBy	// e lo filtro
					(
						function(rec,id)
						{
							var re=null;
							var ret=new Array();	// se contiene almeno uno 0 significa che almeno un criterio non � rispettato 
							
						// cerco tra i criteri
							for (var i=0;i<filters.length;++i)
							{
							// se trovo qualche cosa
								if (filters[i]!='')
								{
									var re=new RegExp(filters[i],'i');
									ret.push(re.test(rec.get(cm.getDataIndex(i))) ? 1 : 0);
								}
							}
							
						// converto il vettore in stringa per convenienza 
							ret=ret.toString();
							
							if (ret.search('0')!=-1)
								return false;
							else
								return true;
							
						},
						this
					);
		}
	}
	
/**
 * Renderizza la griglia.
 * Viene richiamato dall'ultimo store caricato quando scatta l'evento load
 *
 * @return void
 *
 * @access private
 */
	function renderGrid()
	{
		setupGrid();
		gridPartecipants.render(el);		
		
	/*
	 * Devo impostare lo stato del bottone che fa sparire le colonne della terza società "a premuto" ma la proprietà
	 * del bottone è in sola lettura.
	 * Allora aggiungo la classe del bottone che otterrebbe se fosse cliccato.
	 */
		//console.debug(Ext.get('btnHideColsGroup_3'));
	}
	
	function setError(record,col)
	{
		if (checkError(record,col)==-1)
		{
			var errors=record.get('errors').split('|');
			errors.push(col);
			record.set('errors',errors.join('|'));
			return true;
		}
		
		return false;
	}
	
	function unsetError(record,col)
	{
		var index=checkError(record,col);
		if (index!=-1)
		{
			var errors=record.get('errors').split('|');
			errors.splice(index,1);
			record.set('errors',errors.join('|'));
			
			return true;
		}
		
		return false;
	}
	
	function checkError(record,col)
	{
		var errors=record.get('errors').split('|');
		for (var e in errors)
		{
			if (errors[e]==col)
				return e;
		}
		
		return -1;
	}
	
/****** Renderizzatori ******/

	function standardRenderer(value,meta,record,row,col,store)
	{
		if (checkError(record,col)!=-1)
		{
			meta.css='x-grid3-invalid-cell';
		}
		return value;
	}
	
	function comboRenderer(combo)
	{
		return function(value,meta,record,row,col,store) 
		{
			var regex=new RegExp('^' + value + '$');
			var idx = combo.store.find(combo.valueField, regex,0,true);
		  
		    var ret="";
		    if (idx!=-1)
		    {
		    	var rec = combo.store.getAt(idx);
		    	ret=rec.get(combo.displayField);
		    }
		    if (checkError(record,col)!=-1)
			{
				meta.css='x-grid3-invalid-cell';
			}
		
		    return ret; 
		}
	}
	
/**
 * Renderizza la colonna tools della griglia.
 *
 * @return String: la stringa html usata per generare il contenuto della cella
 */
	function toolsRenderer()
	{
		var html
			= '<div class="control-btn">'
				+ '<img src="'+WebDir+'Common/Images/drop.png" width="16" height="16" class="tool_drop">'
			+ '</div>';
		return html;
	}
	
/**
 * Renderizza la colonna per il tooltip dell'accesso agli eventi
 */
	function eventsRenderer()
	{
		var html
			= '<div class="events-access-icon">'
				+ '<img src="'+WebDir+'Common/Images/events_access.gif" width="16" height="16" class="events_access">'
			+ '</div>';
		
		return html;
	}
	
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
			case '6':
				img='status-gohome.gif';
				title=Arr_StrStatus[6];
				break;
			case '7':
				img='status-notaccredited.gif';
				title=Arr_StrStatus[7];
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
	
	function photoRenderer(value,meta,record,row,col,store) 
	{
		var img='';
		var title='';
		
		switch (value)
		{
			case '0':
				img='photo-no.gif';
				title=StrNo;
				break;
			case '1':
				img='photo-yes.gif'; 
				title=StrYes
				break;
			
		}
		
		var html
			= '<div class="has-photo" id="ph_' + record.get('id') + '">'
				+ '<img src="'+WebDir+'Common/Images/' + img + '" title="' + title + '" width="16" height="16">'
			+ '</div>';
			
		return html;
	}
	
/****** End Renderizzatori ******/
	
/****** Event handlers ******/
/**
 * Gestisce l'evento load degli stores.
 *
 * @param Ext.data.Store store: store che ha completato il load
 * @param  Ext.data.Record[] records: records caricati
 *
 * @return void
 */
	function onLoadStores(store,records)
	{
		if (++loadedStores==totalRecords)
		{
			renderGrid();
		}
	}
	
/**
 * Gestisce l'evento click sulla griglia
 * 
 * @param Ext.EventObject eventObject
 *
 * @return void
 *
 * @access private
 */	
	function onClickGridPartecipants(eventObject)
	{
	// Prendo il target sul div di classe .control-btn
		 var btn = eventObject.getTarget('.control-btn');
		 
		 if (btn)
		 {
			var t = eventObject.getTarget();		// questo è il bottone cliccato
			var row=this.getSelectionModel().getSelectedCell()[0];
			var col=this.getSelectionModel().getSelectedCell()[1];
			//var rec=this.getStore().getAt(row);
			
			var tool=t.className.split('_')[1];
			
			switch (tool)
			{
				case 'drop':
					Ext.util.confirm
					(
						{
							title: StrWarning,
							msg: StrMsgAreYouSure,
							fn: function(btn)
							{
								if (btn=='yes')
								{
									dropArcher(row,col);
								}
							},
							strYes:StrYes,
							strNo:StrNo
						}
					);
					break;
			}	
		 }        
	}
	
	function onCellDblClickGridPartecipants(grid,row, col,  e)
	{
		var dataIndex=grid.getColumnModel().getDataIndex(col);
		
		switch (dataIndex)
		{
			case 'has_photo':
				var rec=grid.getStore().getAt(row);
				
				if (!winPhoto)
				{
					winPhoto=new partecipantsApp.Photo(me);
					winPhoto.bootstrap(rec.get('id'),row);
				}
				else
				{
					winPhoto.beforeChangeContent();
					winPhoto.loadIFrame(rec.get('id'),row);
				}
				break;
				
			case 'events':
				var rec=grid.getStore().getAt(row);
				
				if (!winEventsAccess)
				{
					winEventsAccess=new partecipantsApp.EventsAccess(me);
					winEventsAccess.bootstrap(rec.get('id'),row);
				}
				else
				{
					winEventsAccess.loadIFrame(rec.get('id'),row);
				}
				break;
				break;
		}
	}
	
	function onCellContextMenuGridPartecipants(grid, row, col,eventObject)
	{
		var dataIndex=grid.getColumnModel().getDataIndex(col);
	
		eventObject.stopEvent();
		
	// seleziono la cella in modo da avere il riferimento alla riga  per l'handler del click
		grid.getSelectionModel().select(row,col);
		
		var rec=grid.getStore().getAt(row);
		
		var xy=eventObject.getXY();
		
		switch (dataIndex)
		{
			case 'target_no':
				if (grid.getColumnModel().isCellEditable(col,row))
				{
				
					var params=
					{
						x: xy[0],
						y: xy[1],
						row: row,
						col: col,
						session: rec.get('session')
					}
					
					var o=
					{
						url: 'actions/xmlGetFreeTargets.php',
						method: 'POST',
						params: params,
						success: xmlGetFreeTargets
					}
					
					Ext.Ajax.request(o);
				}
				
				break;
			case 'status':
				if (rec.get('status')==1 || rec.get('status')==5 || rec.get('status')==8)
				{
					contextMenuStatus.showAt(xy);
				}
		
				break;
				
			default:
				return;
		}	
	}
	
	function onClickContextMenuStatus(item,eventObject)
	{
		var newStatus=item.id.split('_')[1];
		
		var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
		var rec=gridPartecipants.getStore().getAt(row);
		
		if (rec.get('status')!=newStatus)
		{
			updateStatus(newStatus);
		}
			
	}
	
	function updateStatus(newStatus)
	{
		var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
		var rec=gridPartecipants.getStore().getAt(row);
		
		var params=
		{
			id: rec.get('id'),
			status:newStatus
		}
		
		var o=
		{
			url: 'actions/xmlUpdateStatus.php',
			method: 'POST',
			params: params,
			success: xmlUpdateStatus
		}
		
		Ext.Ajax.request(o);
	}
	
/**
 * Manda la request a xmlDropArcher.php che elimina l'arciere id
 *
 * @param Int row: indice di riga
 * @param Int col: indice di colonna
 *
 * @return void
 *
 * @access private
 */
	function dropArcher(row,col)
	{
		var rec=gridPartecipants.getStore().getAt(row);
		var params=
		{
			id: rec.get('id'),
			row:row,
			col:col
		}
		
		var o=
		{
			url: 'actions/xmlDropArcher.php',
			method: 'POST',
			params: params,
			success: xmlDropArcher
		}
		
		Ext.Ajax.request(o);
	}
		
/**
 * Gestisce l'evento beforeedit della griglia.
 *
 * @param Object editEvent: evento con le seguenti propriet�:
 * 		Ext.grid.EditorGridPanel grid: la griglia (this)
 *		Ext.data.Record record: Record della riga
 *		String field: nome del campo
 *		String value: valore del campo
 *		Integer row: indice di riga
 *		Integer column:indice di colonna
 *		Boolean cancel: da impostare a true per bloccare l'edit
 *
 * @return void
 *
 * @access private
 */
	function onBeforeEditGridPartecipants(editEvent)
	{
		var grid=editEvent.grid;
		var row=editEvent.row;
		var col=editEvent.column;
		var record=editEvent.record;
		
		var dataIndex=grid.getColumnModel().getDataIndex(col);
		
	// A seconda della colonna...
		switch (dataIndex)
		{
			case 'code':
				break;
				
			case 'class':
				stores['comboClass'].clearFilter();	  // tolgo il filtro dallo store
			
				var ageCl=record.get('age_class');
								
			// Cerco nello store stores['comboAgeClass'] la riga con il cambo id==ageCl
				var index = stores['comboAgeClass'].find('id', new RegExp('^' + ageCl + '$'),0,true);
				if (index==-1)
				{
					index = stores['comboAgeClass'].find('id', new RegExp(''),0,true);
				}
			/*	var filter								// il filtro
					= '(--|'						
					+ stores['comboAgeClass']			// dallo store 
						.getAt(index)					// prendo il record index
							.get('valid')				// e tiro fuori il campo age_class
								.replace(/\,/g,'|')		// e sostituisco le ',' con le '|' per la regex
					+ ')';
				*/
				var filter='(--|';
					
				var valid=stores['comboAgeClass'].getAt(index).get('valid');
				var tmp=new Array();
				tmp=valid.split(',');
				for (var i=0;i<tmp.length;++i)
				{
					filter+='^' + tmp[i] + '$|';
				}
				filter=filter.substr(0,filter.length-1) + ')';
				//console.debug(ageCl + ' ' + filter);
					
			//console.debug(new RegExp(filter));
			// applico il filtro							
				stores['comboClass'].filter('id',new RegExp(filter));
				break;
				
			case 'age_class':
				//console.debug('beforedit');
			/*
			 * Non posso fare di meglio che eseguire una chiamata sincrona per farmi ritornare
			 * la lista delle classi del sesso selezionato (+ le unisex se ci sono).
			 * Se usassi una request asincrona questa terminerebbe dopo l'expand della tendina per cui
			 * lo store caricato avrebbe la vecchia lista di valori.
			 * 
			 * IMPORTANTISSIMO!!!!!!!!!!!!!!!!!
			 * Il sistema funziona ma il primo beforeedit sulla age_class non filtra; da quello in poi (per tutte le altre tendine)
			 * sì.
			 */
				xmlHttp=CreateXMLHttpRequestObject();
				try
				{
					xmlHttp.open("GET","actions/xmlGetClassesByGender.php?div=" + record.get('division') + "&ath=" + record.get('athlete') + "&sex=" + record.get('sex') + "&row=" + row + "&col=" + col,false);
					xmlHttp.send(null);
					
				// gestisco il response
					var xml=xmlHttp.responseXML;
					//console.debug(xml);
					
					var dq=Ext.DomQuery;
					
					var error=dq.selectNode('error',xml).firstChild.data;
					
					if (error==0)
					{
						var classes=dq.select('class',xml);
						
						stores['comboAgeClass'].clearFilter();
						
						var filter='(--|';
						
						for (var i=0;i<classes.length;++i)
						{
							filter+='^' + classes[i].firstChild.data +'$|';
						}
						
						filter=filter.substr(0,filter.length-1)+  ')';
						
						stores['comboAgeClass'].filter('id',new RegExp(filter));
					}
				}
				catch(e)
				{
					alert(e.toString());
				}
				break;
				
		}
	}

	
/**
 * Gestisce l'evento afteredit della griglia.
 * Viene usato al posto dei blur dei campi per eveitare di perdere
 * il riferimento alla riga/colonna
 *
 * @param Object editEvent: evento con le seguenti proprietà:
 * 		Ext.grid.EditorGridPanel grid: la griglia (this)
 *		Ext.data.Record record: Record della riga
 *		String field: nome del campo
 *		String value: valore del campo
 *		Integer row: indice di riga
 *		Integer column:indice di colonna
 *		Boolean cancel: da impostare a true per bloccare l'edit
 *
 * @return void
 *
 * @access private
 */	
	function onAfterEditGridPartecipants(editEvent)
	{
		var grid=editEvent.grid;
		var row=editEvent.row;
		var col=editEvent.column;
		var field=editEvent.field;
		var record=editEvent.record;
		var value=editEvent.value;
		var dataIndex=grid.getColumnModel().getDataIndex(col);
			
		var params=null		// parametri della request
		var url='';			// url dell'action
		var callback=null;	// fn di callback se la request ha successo 
		
	// A seconda della colonna...
		switch (dataIndex)
		{
			case 'code':
				return;
				
			case 'session':
				params=
				{
					id:record.get('id'),
					session:value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateSession.php';
				callback=xmlUpdateSession;
				break;
				
			case 'target_no':
				params=
				{
					id: record.get('id'),
					session: record.get('session'),
					target: value,
					row: row,
					col: col
				}
				url='actions/xmlUpdateTargetNo.php';
				callback=xmlUpdateTargetNo;
				break;
					
			case 'first_name':
			case 'name':
			case 'division':	
			case 'sub_class':
			case 'sub_team':
				params=
				{
					field: field,
					value: value,
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateField.php';
				callback=xmlUpdateField;
				break;
			case 'athlete':
				params=
				{
					ath: value,
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateAthlete.php';
				callback=xmlUpdateAthlete;
				break;
				
			/*case 'sex':
				params=
				{
					sex: value,
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateSex.php';
				callback=xmlUpdateSex;
				break;*/
				
			case 'ctrl_code':
			case 'sex':
				params=
				{
					ctrl_code: record.get('ctrl_code'),
					sex: record.get('sex'),
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateCtrlCode.php';
				callback=xmlUpdateCtrlCode;
				break;
				
			case 'country_code':
				params=
				{
					id: record.get('id'),
					country_code:value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryCode.php';
				callback=xmlUpdateCountryCode;
				break;
				
			case 'country_name':
				params=
				{
					country_code:record.get('country_code'),
					country_name: value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryName.php';
				callback=xmlUpdateCountryName;
				break;
				
			case 'country_name2':
				params=
				{
					country_code:record.get('country_code2'),
					country_name: value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryName.php';
				callback=xmlUpdateCountryName2;
				break;
				
			case 'country_name3':
				params=
				{
					country_code:record.get('country_code3'),
					country_name: value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryName.php';
				callback=xmlUpdateCountryName3;
				break;	
				
			case 'country_code2':
				params=
				{
					id: record.get('id'),
					country_code:value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryCode2.php';
				callback=xmlUpdateCountryCode2;
				break;
				
			case 'country_code3':
				params=
				{
					id: record.get('id'),
					country_code:value,
					row:row,
					col:col
				}
				url='actions/xmlUpdateCountryCode3.php';
				callback=xmlUpdateCountryCode3;
				break;
				
			case 'age_class':
				stores['comboClass'].clearFilter();
				record.set('class',value);
				params=
				{
					'class': record.get('class'),
					age_class: value,
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateClass.php';
				callback=xmlUpdateClass;
				break;	
	
			case 'class':
				params=
				{
					'class': value,
					age_class: record.get('age_class'),
					id: record.get('id'),
					row:row,
					col:col
				}
				url='actions/xmlUpdateClass.php';
				callback=xmlUpdateClass;
				break;	
			
				
			default:
				break;
		}
			
		var o=
		{
			url: url,
			method:'POST',
			params: params,
			success: callback
		}
		
		Ext.Ajax.request(o);
	}
			
/**
 * Gestisce l'evento select della tendina comboAgeClass.
 * In base al valore che viene impostato viene anche settato il valore della
 * tendina comboClass.
 *
 * @param Ext.form.ComboBox combo: tendina
 * @param Ext.data.Record comboRecord: record dello store agganciato
 * @param Int index: indice dell'elemento selezionato nella dropdown list
 *
 * @return void
 *
 * @access private
 */
	function onSelectComboAgeClass(combo,comboRecord)
	{
		var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
		var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
		
		var rec=gridPartecipants.getStore().getAt(row);
		
	/*
	 * Se il valore della tendina class � buono per le valid_class non faccio nulla altrimenti imposto a --.
	 * Per fare la verifica devo cercare nello store delle classi se nella stringa valid c'è la classe gara settata
	 */
	 	if (comboRecord.get('valid').indexOf(rec.get('class'))==-1)
	 	{
	 		rec.set('class','--');
	 	}
	}
	
/**
 * Gestisce l'evento keyup sul codice nazione
 * Manda la request a xmlFindCountryCode.php per cercare il codice nazione inserito
 * e ritornare il nome.
 *
 * @param Ext.form.TextField field: campo
 * @param Ext.EventObject eventObject
 *
 * @return void
 *
 * @access private
 */
	function onKeyUpTextCountryCode(field,eventObject)
	{
		var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
		var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
		
		var params=
		{
			country_code: field.getValue(),
			row:row,
			col:col
		}
		
		var o=
		{
			url: 'actions/xmlFindCountryCode.php',
			method:'POST',
			params: params,
			success: xmlFindCountryCode
		}
		
		//Ext.Ajax.request(o);
		
		findCountryCodeBuffer.push(o);
		findCountryCode();
	}
	
	function findCountryCode()
	{
		if (findCountryCodeBuffer.getSize()>0)
		{
			var o=findCountryCodeBuffer.shift();
			Ext.Ajax.request(o);
		}
	}
	
	/**
	 * Gestisce l'evento keyup sul codice nazione2
	 * Manda la request a xmlFindCountryCode.php per cercare il codice nazione inserito
	 * e ritornare il nome.
	 *
	 * @param Ext.form.TextField field: campo
	 * @param Ext.EventObject eventObject
	 *
	 * @return void
	 *
	 * @access private
	 */
		function onKeyUpTextCountryCode2(field,eventObject)
		{
			var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
			var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
			
			var params=
			{
				country_code: field.getValue(),
				row:row,
				col:col
			}
			
			var o=
			{
				url: 'actions/xmlFindCountryCode.php',
				method:'POST',
				params: params,
				success: xmlFindCountryCode2
			}
			
			//Ext.Ajax.request(o);
			
			findCountryCode2Buffer.push(o);
			findCountryCode2();
		}
	
	/**
	 * Gestisce l'evento keyup sul codice nazione3
	 * Manda la request a xmlFindCountryCode.php per cercare il codice nazione inserito
	 * e ritornare il nome.
	 *
	 * @param Ext.form.TextField field: campo
	 * @param Ext.EventObject eventObject
	 *
	 * @return void
	 *
	 * @access private
	 */
		function onKeyUpTextCountryCode3(field,eventObject)
		{
			var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
			var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
			
			var params=
			{
				country_code: field.getValue(),
				row:row,
				col:col
			}
			
			var o=
			{
				url: 'actions/xmlFindCountryCode.php',
				method:'POST',
				params: params,
				success: xmlFindCountryCode3
			}
			
			//Ext.Ajax.request(o);
			
			findCountryCode3Buffer.push(o);
			findCountryCode3();
		}	
		
		
		function findCountryCode2()
		{
			if (findCountryCode2Buffer.getSize()>0)
			{
				var o=findCountryCode2Buffer.shift();
				Ext.Ajax.request(o);
			}
		}
		
		function findCountryCode3()
		{
			if (findCountryCode3Buffer.getSize()>0)
			{
				var o=findCountryCode3Buffer.shift();
				Ext.Ajax.request(o);
			}
		}
					
/**
 * Gestisce l'evento keyup della colonna 'code'.
 * Manda la request a xmlFindCode.php che cerca i dati di una persona partendo dalla matricola
 *
 * @param Ext.form.TextField field: campo
 * @param Ext.EventObject eventObject
 *
 * @access private
 */
	function onKeyUpTextCode(field,eventObject)
	{		
		var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
		var col=gridPartecipants.getSelectionModel().getSelectedCell()[1];
		var rec=gridPartecipants.getStore().getAt(row);	
		
		var params={}
		var o={}
		
		params=
		{
			code: field.getValue(),
			id: rec.get('id'),
			row: row,
			col: col
		};
		
	/*
	 * Sil btnBlockFill � premuto non faccio il completamento della riga
	 * e mi limito a salvare la matricola 
	 */
		if (Ext.getCmp('btnBlockFill').pressed)
		{
			o=
			{
				url: 'actions/xmlUpdateCode.php',
				params: params,
				method: 'POST',
				success: xmlUpdateCode
			};
			
			Ext.Ajax.request(o);
			
			return;
		}
		
		o=
		{
			url: 'actions/xmlFindCode.php',
			params: params,
			method: 'POST',
			success: xmlFindCode
		};
		
		//Ext.Ajax.request(o);
		
		findCodeBuffer.push(o);
		updateCode();	
		
	}
	
	function updateCode()
	{
		if (findCodeBuffer.getSize()>0)
		{
			var o=findCodeBuffer.shift();
			Ext.Ajax.request(o);
		}
	}
	
	function onClickBtnAdd()
	{
		me.addArcher();
	}
	
	function onClickBtnSearch()
	{
		if (!winSearch)
		{
			winSearch=new partecipantsApp.Search(me);
			winSearch.bootstrap();
		}
	}
	
	function onClickHideBtns(group)
	{
		return function()
		{
		/*
		 * La chiave di group corrisponde al gruppo
		 */
			var groups=new Array(
				new Array(
					'colCtrlCode','colGender'
				),
				new Array(
					'colCountryCode2','colCountryName2','colSubTeam'
				),
				new Array(
					'colDiv','colAgeClass','colClass','colSubClass'
				),
				new Array(
					'colCountryCode3','colCountryName3'
				)
			);
			
			var cm=gridPartecipants.getColumnModel();
			
		// prelevo lo stato del bottone premuto
			var hide=Ext.getCmp('btnHideColsGroup_'+group).pressed;
			
		// in base al gruppo decido quali colonne toccare
			var cols=groups[group];
			
			if (cols.length>0)
			{
				Ext.each (cols,function(item)
				{
					cm.setHidden(
						cm.getIndexById(item),
						hide
					);
				});
			}
			
			gridPartecipants.doLayout();
		}
	}
	
/****** End Event handlers ******/

/****** Metodi per gestire i response delle actions ajax ******/

/**
 * Gestisce il response di xmlFindCode.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlFindCode(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);					
			var rec=gridPartecipants.getStore().getAt(row);	
					
			switch (error)
			{
				case '0':		// tutto ok
					unsetError(rec,col);
				// colonne del record	
					var items=rec.fields.items
					
					Ext.each		// per ogni
					(
						items,		// colonna
						function(item)
						{
							
						/*
						 * errors, 
						 * session, 
						 * target_no,
						 * code
						 * non li devo toccare
						 */
							
							if (item.mapping=='session' || 
								item.mapping=='target_no' || 
								item.mapping=='errors' ||
								item.mapping=='code')
							{
								return;	
							}
								
						// setto il valore della colonna con il nodo xml omonimo
							var val=xmlRoot.getElementsByTagName(item.mapping).item(0).firstChild.data;
							
							if (val=='#')
								val='';
							
							rec.set(item.name,val);	
							
						}
					);	
					
				// per allineare almeno la classe anagrafica con il codice fiscale
					var o=
					{
						url: 'actions/xmlUpdateCtrlCode.php',
						method:'POST',
						params: 
						{
							ctrl_code: rec.get('ctrl_code'),
							sex: rec.get('sex'),
							id: rec.get('id'),
							noRecalc: 1,		// qui non devo rifare nulla
							row:row,
							col:col
						},
						success: xmlUpdateCtrlCode
					}
					Ext.Ajax.request(o);
					break;
				case '1':		// errore
					setError(rec,col);
					break;
			}
			
		// Dato che il set implica il blocco dell'editing, lo riattivo
			gridPartecipants.startEditing(row,col);
			
			var task=
			{
				run: updateCode,
				interval: 300,	// ms
				repeat: 1,
				scope: this
			};
			
			Ext.TaskMgr.start(task);
			
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	function xmlUpdateCode(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var rec=gridPartecipants.getStore().getAt(row);	
			
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					// non ho ancora deciso		
					break;
				case '1':
					setError(rec,col);
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.ToString());
		}
	}
	
/**
 * Gestisce il response di xmlUpdateField.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlUpdateField(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var field=xmlRoot.getElementsByTagName('field').item(0).firstChild.data;
			var value=xmlRoot.getElementsByTagName('value').item(0).firstChild.data;
			var rec=gridPartecipants.getStore().getAt(row);	
			
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					// updates the value
					rec.set(field, value);
					
					// non ho ancora deciso
					if (field=='division')
					{
						var o=
						{
							url: 'actions/xmlUpdateCtrlCode.php',
							method:'POST',
							params: 
							{
								ctrl_code: rec.get('ctrl_code'),
								id: rec.get('id'),
								noRecalc: 1,		// qui non devo rifare nulla
								row:row,
								col:col
							},
							success: xmlUpdateCtrlCode
						}
						Ext.Ajax.request(o);
					}
					
					break;
				case '1':
					setError(rec,col);
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.ToString());
		}
	}

/**
 * Gestisce il response di xmlUpdateClass.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */	
	function xmlUpdateClass(response)
	{
		var xmlResp={};

		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;

			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			
			var ath=parseInt(xmlRoot.getElementsByTagName('ath').item(0).firstChild.data);
			
			var rec=gridPartecipants.getStore().getAt(row);
			
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					
					var oldAth=rec.get('athlete');
					
					if (ath!=oldAth)
					{
						rec.set('athlete',ath);
					}
					break;
				case '1':
					//console.debug('qui');
					setError(rec,col);
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
/**
 * Gestisce il response di xmlUpdateCtrlCode.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlUpdateCtrlCode(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
		//	alert(row);return;
			var rec=stores['partecipant'].getAt(row);
			
			switch (error)
			{
				case '0':
					stores['comboClass'].clearFilter();
					rec.set('class','--');
					
					unsetError(rec,col);
					
					var items=rec.fields.items
					
				// tolgo il filtro alle classi
					stores['comboAgeClass'].clearFilter();
					
				// riallineo la riga con i dati che mi son tornati
					Ext.each			// per ogni
					(
						items,			// colonna del record
						function(item)
						{
						// la session il target e errors, non li tocco
							if (item.mapping=='session' || item.mapping=='target_no' || item.mapping=='errors')
								return;
								
						// setto il valore della colonna con il nodo xml omonimo
							var val=xmlRoot.getElementsByTagName(item.mapping).item(0).firstChild.data;
							if (val=='#')
							{
								if (item.name!='class')
									val='';
								else
									val='--';
							}
							
							rec.set(item.name,val);
						}
					);
					
				// forzo l'up della classe anagrafica.
					//rec.set('class','--');
					var o=
					{
						url: 'actions/xmlUpdateClass.php',
						method:'POST',
						params:
						{
							'class': rec.get('class'),
							'age_class': rec.get('age_class'),
							id: rec.get('id'),
							row:row,
							col:col
						},
						success: xmlUpdateClass
					}
					
					//Ext.Ajax.request(o);
					
					break;
				case '1':
					setError(rec,col);
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
/**
 * Gestisce il response di xmlFindCountryCode.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlFindCountryCode(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var rec=gridPartecipants.getStore().getAt(row);
			
			switch (error)
			{
				case '0':
				// Imposto il codice e il nome alle colonne del record	
					var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
					var coName=xmlRoot.getElementsByTagName('country_name').item(0).firstChild.data;
	
					if (coName=='#')
						coName='';
					
					rec.set('country_id',coId);
					rec.set('country_name',coName);
					
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
			
		// riattivo l'editing
			gridPartecipants.startEditing(row,col);	
			
			var task=
			{
				run: findCountryCode,
				interval: 300,	// ms
				repeat: 1,
				scope: this
			};
			
			Ext.TaskMgr.start(task);
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	/**
	 * Gestisce il response di xmlFindCountryCode.php sulla seconda nazione
	 *
	 * @param response: xml di ritorno
	 *
	 * @return void
	 *
	 * @access private
	 */
		function xmlFindCountryCode2(response)
		{
			var xmlResp={};
			
			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);
				
				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
				
				var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
				var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
				var rec=gridPartecipants.getStore().getAt(row);
				
				switch (error)
				{
					case '0':
					// Imposto il codice e il nome alle colonne del record	
						var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
						var coName=xmlRoot.getElementsByTagName('country_name').item(0).firstChild.data;
		
						if (coName=='#')
							coName='';
						
						rec.set('country_id2',coId);
						rec.set('country_name2',coName);
						
						break;
					case '1':
						// non ho ancora deciso
						break;
				}
				
			// riattivo l'editing
				gridPartecipants.startEditing(row,col);	
				
				var task=
				{
					run: findCountryCode2,
					interval: 300,	// ms
					repeat: 1,
					scope: this
				};
				
				Ext.TaskMgr.start(task);
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
		
	/**
	 * Gestisce il response di xmlFindCountryCode.php sulla terza nazione
	 *
	 * @param response: xml di ritorno
	 *
	 * @return void
	 *
	 * @access private
	 */
		function xmlFindCountryCode3(response)
		{
			var xmlResp={};
			
			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);
				
				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
				
				var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
				var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
				var rec=gridPartecipants.getStore().getAt(row);
				
				switch (error)
				{
					case '0':
					// Imposto il codice e il nome alle colonne del record	
						var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
						var coName=xmlRoot.getElementsByTagName('country_name').item(0).firstChild.data;
		
						if (coName=='#')
							coName='';
						
						rec.set('country_id3',coId);
						rec.set('country_name3',coName);
						
						break;
					case '1':
						// non ho ancora deciso
						break;
				}
				
			// riattivo l'editing
				gridPartecipants.startEditing(row,col);	
				
				var task=
				{
					run: findCountryCode3,
					interval: 300,	// ms
					repeat: 1,
					scope: this
				};
				
				Ext.TaskMgr.start(task);
			}
			catch (e)
			{
				alert(e.toString());
			}
		}
	
/**
 * Gestisce il response di xmlUpdateCountryCode.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlUpdateCountryCode(response)
	{
		xmlResp=response.responseXML;
		xmlRoot=Ext.util.xmlResponse(xmlResp);
		var xmlResp={};
		
		var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
		var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
		var rec=gridPartecipants.getStore().getAt(row);
		
		try
		{
			
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					unsetError(rec,col);
				// Imposto il codice e il nome alle colonne del record	
					var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
					var coCode=xmlRoot.getElementsByTagName('country_code').item(0).firstChild.data;
					var coName=xmlRoot.getElementsByTagName('country_name').item(0).firstChild.data;
					if (coName=='#')
						coName='';
					
					rec.set('country_id',coId);
					rec.set('country_code',coCode);
					rec.set('country_name',coName);
					break;
				case '1':
					setError(rec,col);
					break;
			}
		}
		catch (e)
		{
			return;
		}
	}
	
	/**
	 * Gestisce il response di xmlUpdateCountryCode2.php
	 *
	 * @param response: xml di ritorno
	 *
	 * @return void
	 *
	 * @access private
	 */
		function xmlUpdateCountryCode2(response)
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			var xmlResp={};
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var rec=gridPartecipants.getStore().getAt(row);
			
			
			try
			{
				
				
				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
				
				switch (error)
				{
					case '0':
						unsetError(rec,col);
					// Imposto il codice e il nome alle colonne del record	
						var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
						var coCode=xmlRoot.getElementsByTagName('country_code').item(0).firstChild.data;
						
						rec.set('country_id2',coId);
						rec.set('country_code2',coCode);
						break;
					case '1':
						setError(rec,col);
						break;
				}
			}
			catch (e)
			{
				return;
			}
		}
		
		/**
		 * Gestisce il response di xmlUpdateCountryCode3.php
		 *
		 * @param response: xml di ritorno
		 *
		 * @return void
		 *
		 * @access private
		 */
			function xmlUpdateCountryCode3(response)
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);
				var xmlResp={};
				
				var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
				var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
				var rec=gridPartecipants.getStore().getAt(row);
				
				
				try
				{
					
					
					var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
					
					switch (error)
					{
						case '0':
							unsetError(rec,col);
						// Imposto il codice e il nome alle colonne del record	
							var coId=xmlRoot.getElementsByTagName('country_id').item(0).firstChild.data;
							var coCode=xmlRoot.getElementsByTagName('country_code').item(0).firstChild.data;
							
							rec.set('country_id3',coId);
							rec.set('country_code3',coCode);
							
							break;
						case '1':
							setError(rec,col);
							break;
					}
				}
				catch (e)
				{
					return;
				}
			}
	
	
	
/**
 * Gestisce il response di xmlUpdateCountryName.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlUpdateCountryName(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var coName=xmlRoot.getElementsByTagName('value').item(0).firstChild.data;
			
			var rec=gridPartecipants.getStore().getAt(row);
			if (coName!='') rec.set('country_name', coName);
		
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					var coCode=rec.get('country_code');		// codice di partenza
					//var coName=rec.get('country_name');		// nuovo nome
					
				// solo se il nome non è la stringa vuota
					if (coName!='')
					{
						var newName=xmlRoot.getElementsByTagName('new_name').item(0).firstChild.data;
					// se il nome � cambiato
						if (newName==1)
						{	
						// Per ogni riga dello store della griglia
							gridPartecipants.getStore().each(function(record)
							{
							// se il country_code è uguale a quello da cui son partito
								if (record.get('country_code')==coCode)
								{
								// imposto la colonna country_name a coName
									record.set('country_name',coName);
								}
								
								if (record.get('country_code2')==coCode)
								{
								// imposto la colonna country_name a coName
									record.set('country_name2',coName);
								}
								
								if (record.get('country_code3')==coCode)
								{
								// imposto la colonna country_name a coName
									record.set('country_name3',coName);
								}
							});
						}
					}
					break;
				case '1':
					setError(rec,col);
					break;
			}
		}
		catch (e)
		{
			return;
		}
	}
	
	/**
	 * Gestisce il response di xmlUpdateCountryName.php (2)
	 *
	 * @param response: xml di ritorno
	 *
	 * @return void
	 *
	 * @access private
	 */
		function xmlUpdateCountryName2(response)
		{
			var xmlResp={};
			
			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);
				
				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
				
				var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
				var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
				var coName=xmlRoot.getElementsByTagName('value').item(0).firstChild.data;
				var rec=gridPartecipants.getStore().getAt(row);
				if (coName!='') rec.set('country_name2', coName);
				switch (error)
				{
					case '0':
						unsetError(rec,col);
						var coCode=rec.get('country_code2');		// codice di partenza
						//var coName=rec.get('country_name2');		// nuovo nome
					// solo se il nome non è la stringa vuota
						if (coName!='')
						{
							var newName=xmlRoot.getElementsByTagName('new_name').item(0).firstChild.data;
						// se il nome � cambiato
							if (newName==1)
							{	
							// Per ogni riga dello store della griglia
								gridPartecipants.getStore().each(function(record)
								{
								// se il country_code � uguale a quello da cui son partito
									if (record.get('country_code2')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name2',coName);
									}
									
									if (record.get('country_code')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name',coName);
									}
									
									if (record.get('country_code3')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name3',coName);
									}
								});
							}
						}
						break;
					case '1':
						setError(rec,col);
						break;
				}
			}
			catch (e)
			{
				return;
			}
		}
		
	/**
	 * Gestisce il response di xmlUpdateCountryName.php (3)
	 *
	 * @param response: xml di ritorno
	 *
	 * @return void
	 *
	 * @access private
	 */
		function xmlUpdateCountryName3(response)
		{
			var xmlResp={};
			
			try
			{
				xmlResp=response.responseXML;
				xmlRoot=Ext.util.xmlResponse(xmlResp);
				
				var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
				
				var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
				var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
				var coName=xmlRoot.getElementsByTagName('value').item(0).firstChild.data;
				var rec=gridPartecipants.getStore().getAt(row);
				var rec=gridPartecipants.getStore().getAt(row);
				if (coName!='') rec.set('country_name3', coName);
				switch (error)
				{
					case '0':
						unsetError(rec,col);
						var coCode=rec.get('country_code3');		// codice di partenza
						//var coName=rec.get('country_name3');		// nuovo nome
					// solo se il nome non è la stringa vuota
						if (coName!='')
						{
							var newName=xmlRoot.getElementsByTagName('new_name').item(0).firstChild.data;
						// se il nome � cambiato
							if (newName==1)
							{	
							// Per ogni riga dello store della griglia
								gridPartecipants.getStore().each(function(record)
								{
								// se il country_code � uguale a quello da cui son partito
									if (record.get('country_code3')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name3',coName);
									}
									
									if (record.get('country_code')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name',coName);
									}
									
									if (record.get('country_code2')==coCode)
									{
									// imposto la colonna country_name a coName
										record.set('country_name2',coName);
									}
								});
							}
						}
						break;
					case '1':
						setError(rec,col);
						break;
				}
			}
			catch (e)
			{
				return;
			}
		}	
		
	
/**
 * Gestisce il response di xmlUpdateSession.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlUpdateSession(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
	
			var rec=gridPartecipants.getStore().getAt(row);
					
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					var tooMany=xmlRoot.getElementsByTagName('too_many').item(0).firstChild.data;
					var oldSession=xmlRoot.getElementsByTagName('old_session').item(0).firstChild.data;
					var session=xmlRoot.getElementsByTagName('session').item(0).firstChild.data;
					
				/*
				 * Se ci sono già tutte le persone per la sessione scelta resetto al vecchio 
				 * valore la tendina e avviso
				 */
					if (tooMany==1)
					{
					// ripristino la tendina
						rec.set('session',oldSession);
						lastSession=oldSession;
					// Avviso del problema
						var msg=xmlRoot.getElementsByTagName('msg').item(0).firstChild.data;
						Ext.Msg.alert(StrError,msg);
					}
					else
					{
						rec.set('session',session);
						lastSession=session;
						//console.debug(lastSession);
					// vale 1 se la sessione � stata cambiata e in quel caso resetto il bersaglio
						var resetTarget=xmlRoot.getElementsByTagName('reset_target').item(0).firstChild.data;
					
						if (resetTarget==1)
						{
							rec.set('target_no','');
						}
					}
					break;
				case '1':
					setError(rec,col);
					break;
			}
		}
		catch (e)
		{
			return;
		}
	}
	
/**
 * Gestisce il response di xmlDropArcher.php
 *
 * @param response: xml di ritorno
 *
 * @return void
 *
 * @access private
 */
	function xmlDropArcher(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			
			var rec=gridPartecipants.getStore().getAt(row);
			
			switch (error)
			{
				case '0':
				// cancello la riga dallo store e quindi dalla griglia
					gridPartecipants.getStore().remove(rec);
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			return;
		}
	}
	
	function xmlAddArcher(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					var newId=xmlRoot.getElementsByTagName('new_id').item(0).firstChild.data;
				// Nuovo record per la griglia
					var rec=new records['partecipant']
					(
						{
							errors: '',
							id: newId,
							status: '0',
							sex: '0',
							country_id: '0',
							country_id2: '0',
							age_class_editable:1,
							session: '0',
							target_no: '',
							code: '',
							first_name: '',
							name: '',
							ctrl_code: '',
							country_code: '',
							country_name: '',
							country_code2: '',
							country_code3: '',
							sub_team:'',
							division: '--',
							age_class: '--',
							'class': '--',
							sub_class: '00'
						}
					);
					
					gridPartecipants.stopEditing();
					var store=gridPartecipants.getStore();					
					store.insert(0,rec);			
					
					var c=gridPartecipants.getColumnModel().findColumnIndex('session');
					if (c!=-1)
					{
						/*params=
						{
							id:rec.get('id'),
							session:lastSession,
							row:0,
							col:gridPartecipants.getColumnModel().getColumnById('colSession')
						}
						url='actions/xmlUpdateSession.php';
						callback=xmlUpdateSession;*/
							
					gridPartecipants.startEditing(0,3);
					
					Ext.Ajax.request(
					{
							url:'actions/xmlUpdateSession.php',
							method: 'POST',
							params:
							{
								id:rec.get('id'),
								session:lastSession,
								row:0,
								col:c
							},
							success: xmlUpdateSession
							
						});
					}
					
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	function xmlUpdateStatus(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var row=gridPartecipants.getSelectionModel().getSelectedCell()[0];
			var rec=gridPartecipants.getStore().getAt(row);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					var newStatus=xmlRoot.getElementsByTagName('new_status').item(0).firstChild.data;
					rec.set('status',newStatus);
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			return;
		}
	}
	
	function xmlUpdateSex(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var rec=gridPartecipants.getStore().getAt(row);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					//var sex=xmlRoot.getElementsByTagName('sex').item(0).firstChild.data;
					//rec.set('sex',sex);
					
				// up del codice di controllo
					var o=
					{
						url: 'actions/xmlUpdateCtrlCode.php',
						method: 'POST',
						params:
						{
							ctrl_code: rec.get('ctrl_code'),
							id: rec.get('id'),
							row:row,
							col:col
						},
						success: xmlUpdateCtrlCode
					}
					
					Ext.Ajax.request(o);				
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	function xmlUpdateAthlete(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var row=parseInt(xmlRoot.getElementsByTagName('row').item(0).firstChild.data);
			var col=parseInt(xmlRoot.getElementsByTagName('col').item(0).firstChild.data);
			var rec=gridPartecipants.getStore().getAt(row);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					//var sex=xmlRoot.getElementsByTagName('sex').item(0).firstChild.data;
					//rec.set('sex',sex);
					
					rec.set('class','--');
					rec.set('age_class','--');
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	function xmlGetFreeTargets(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			
			switch (error)
			{
				case '0':
					var row=xmlRoot.getElementsByTagName('row').item(0).firstChild.data;
					var rec=gridPartecipants.getStore().getAt(row);
					
					var xy=
					[
						parseInt(xmlRoot.getElementsByTagName('x').item(0).firstChild.data),
						parseInt(xmlRoot.getElementsByTagName('y').item(0).firstChild.data)
					]
					
					
					var targets=xmlRoot.getElementsByTagName('target');
					
					var menu=createContextMenuFreeTargets(targets,rec);
					menu.showAt(xy);
					
					break;
				case '1':
					// non ho ancora deciso
					break;
			}
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
	function xmlUpdateTargetNo(response)
	{
		var xmlResp={};
		
		try
		{
			xmlResp=response.responseXML;
			xmlRoot=Ext.util.xmlResponse(xmlResp);
			
			var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
			var row=xmlRoot.getElementsByTagName('row').item(0).firstChild.data;
			var col=xmlRoot.getElementsByTagName('col').item(0).firstChild.data;
			var target_no='';
			
			if (xmlRoot.getElementsByTagName('target_no').item(0).firstChild!==null)
			{
				target_no=xmlRoot.getElementsByTagName('target_no').item(0).firstChild.data;
			}
			
			var rec=gridPartecipants.getStore().getAt(row);
					
			switch (error)
			{
				case '0':
					unsetError(rec,col);
					break;
				case '1':
					setError(rec,col);
					break;
			}
			rec.set('target_no',target_no);
		}
		catch (e)
		{
			alert(e.toString());
		}
	}
	
/****** End Metodi per gestire i response delle actions ajax ******/

/****** Metodi pubblici ******/

	this.setWinSearchNull=function()
	{
		winSearch=null;
	}
	
	this.setWinPhotoNull=function()
	{
		winPhoto=null;
	}
	
	this.setWinEventsAccessNull=function()
	{
		winEventsAccess=null;
	}
	
	this.checkPhoto=function(row)
	{
		var rec=gridPartecipants.getStore().getAt(row);	
		
		var o=
		{
			url: 'actions/xmlCheckPhoto.php',
			params:
			{
				id: rec.get('id'),
				row: row
			},
			method: 'POST',
			success: function(response)
			{
				var xmlResp={};
		
				try
				{
					xmlResp=response.responseXML;
					xmlRoot=Ext.util.xmlResponse(xmlResp);
					
					var error=xmlRoot.getElementsByTagName('error').item(0).firstChild.data;
					
					if (error==0)
					{
						var row=xmlRoot.getElementsByTagName('row').item(0).firstChild.data;
						var hasPhoto=xmlRoot.getElementsByTagName('has_photo').item(0).firstChild.data;
						
						var rec=gridPartecipants.getStore().getAt(row);	
						
						rec.set('has_photo',hasPhoto);
					}
					
				}
				catch (e)
				{
					alert(e.toString());
				}
			}
		}
		
		Ext.Ajax.request(o);
	}
	
	this.setCode=function(code)
	{
		var sm=gridPartecipants.getSelectionModel();
		if (!sm.hasSelection())
		{
			Ext.MessageBox.buttonText.ok=StrOk;
			Ext.Msg.show
			(
				{
					title: StrWarning,
					msg: StrNoRowSelected,
					buttons: Ext.Msg.OK
				}
			);
			
			return;
		}
		var row=sm.getSelectedCell()[0];
		var col=sm.getSelectedCell()[1];
		var rec=gridPartecipants.getStore().getAt(row);	
		
		rec.set('code',code);
		
		var params=
		{
			code: code,
			id: rec.get('id'),
			row: row,
			col: col
		};
		
		var o=
		{
			url: 'actions/xmlFindCode.php',
			params: params,
			method: 'POST',
			success: xmlFindCode
		};
		
		//Ext.Ajax.request(o);
		
		findCodeBuffer.push(o);
		updateCode();	
	}
	
	this.addArcher=function()
	{	
		var o=
		{
			url: 'actions/xmlAddArcher.php',
			method: 'POST',
			success: xmlAddArcher
		};
		
		Ext.Ajax.request(o);
	}
		
/**
 * Il metodo va invocato per far partire l'applicazione
 *
 * @return void
 *
 * @access public
 */
	this.bootstrap=function()
	{
		setupRecords();
		setupStores();
		loadStores();
	}
	
/****** End Metodi pubblici ******/
}


