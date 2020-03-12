<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_Various.inc.php');

	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);

	$array = array(
		'StrTourPartecipants' => get_text('TourPartecipants', 'Tournament'),
		'StrSession' => get_text('Session'),
		'StrTarget' => get_text('Target'),
		'StrCode' => get_text('Code', 'Tournament'),
		'StrFamilyName' => get_text('FamilyName', 'Tournament'),
		'StrName' => get_text('Name', 'Tournament'),
		//'StrCtrlCodeShort' => get_text('CtrlCodeShort', 'Tournament'),
		'StrDOB' => get_text('DOB', 'Tournament'),
		'StrGender' => get_text('Sex', 'Tournament'),
		'StrCountry' => get_text('Country'),
		'StrNationShort' => get_text('NationShort', 'Tournament'),
		'StrDiv' => get_text('Div'),
		'StrAgeCl' => get_text('AgeCl'),
		'StrCl' => get_text('Cl'),
		'StrSubCl' => get_text('SubCl', 'Tournament'),
		'StrError' => get_text('Error'),
		'StrWarning' => get_text('Warning', 'Tournament'),
		'StrMsgAreYouSure' => get_text('MsgAreYouSure'),
		'StrYes' => get_text('Yes'),
		'StrNo' => get_text('No'),
		'StrCmdAdd' => get_text('CmdAdd', 'Tournament'),
		'StrStatus' => get_text('Status', 'Tournament'),
		'StrOk' => get_text('CmdOk'),
		'StrSearch' => get_text('Search', 'Tournament'),
		'StrFilterRules' => get_text('FilterRules'),
		'StrArcher' => get_text('Archer'),
		'StrNoRowSelected' => get_text('NoRowSelected', 'Tournament'),
		'StrBlockFill' => get_text('BlockFill', 'Tournament'),
		'StrPhoto' => get_text('Photo', 'Tournament'),
		'StrClose' => get_text('Close'),
		'StrEvents' => get_text('Events', 'Tournament'),
		'StrEventAccess' => get_text('EventAccess', 'Tournament'),
		'StrError' => get_text('Error'),
		'WebDir' => $CFG->ROOT_DIR,
		'StrSubTeam'=>get_text('PartialTeam'),
		'StrHideGroup'=>get_text('HideGroup')
	);

	foreach($Arr_StrStatus as $key=>$val) {
		$array['Arr_StrStatus'][$key]=$val;
	}

	$JS_SCRIPT=array(
		'<link href="'.$CFG->ROOT_DIR.'Common/Styles/ext-2.2/css/ext-all.css" media="screen" rel="stylesheet" type="text/css">',
		'<link href="'.$CFG->ROOT_DIR.'Partecipants-exp/css/partecipants.css" media="screen" rel="stylesheet" type="text/css">',
		phpVars2js($array),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
//		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite-compressed.js"></script>',
//		'<script type="text/javascript">firebug.env.css = "'.$CFG->ROOT_DIR.'Partecipants-exp/firebuglite/firebug-lite.css";</script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/adapter/ext/ext-base.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext-all-debug.js"></script>',
		'<script type="text/javascript">Ext.BLANK_IMAGE_URL=\''.$CFG->ROOT_DIR.'Common/Styles/ext-2.2/images/default/s.gif\';</script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/plugins/FilterRow.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext.ux/ext.ux.js"></script> ',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ext-2.2/ext.util/ext.util.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/js/partecipantsApp.Partecipants.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/js/partecipantsApp.Search.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/js/partecipantsApp.Photo.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Partecipants-exp/js/partecipantsApp.EventsAccess.js"></script>',
		'<script type="text/javascript">Ext.BLANK_IMAGE_URL=\''.$CFG->ROOT_DIR.'Common/Styles/ext-2.2/images/default/s.gif\';</script>',
		'<script type="text/javascript">',
		'	Ext.onReady',
		'	(',
		'		function()',
		'		{',
		'		// evito che il backspace torni indietro nella cronologia',
		'			var keyMap=new Ext.KeyMap',
		'			(',
		'				document,',
		'				{',
		'					key: Ext.EventObject.BACKSPACE,',
		'					fn: function(keyCode,e)',
		'					{',
		'						e.preventDefault();',
		'						return;',
		'					},',
		'					scope: document',
		'				}	',
		'			);',
		'			var p=new partecipantsApp.Partecipants(\'grid-partecipants\');',
		'			p.bootstrap();',
		'		},',
		'		window',
		'	);',
		'</script>',
		);

	//$PAGE_TITLE=get_text('PrintBackNo','BackNumbers');

	include('Common/Templates/head.php');

?>
<div id="grid-partecipants">
<?php
/*
	* Qui verrà renderizzata la griglia dei partecipanti.
	* NON bisogna aggiungere nulla a questo div
	*/
?>
</div>
<?php
	include('Common/Templates/tail.php');
?>