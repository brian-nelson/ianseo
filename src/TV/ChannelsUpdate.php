<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'msg'=>get_text('InvalidAction'));

if(empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'delsplit':
		if(empty($_REQUEST['id']) or !($Id=intval($_REQUEST['id'])) or empty($_REQUEST['side']) or !($SideId=intval($_REQUEST['side']))) {
			JsonOut($JSON);
		}
		// creates a new empty channel
		$q=safe_r_sql("delete from TVOut where TVOId=$Id and TVOSide=$SideId");
		$JSON['error']=0;
		break;
	case 'delchannel':
		if(empty($_REQUEST['id']) or !($Id=intval($_REQUEST['id']))) {
			JsonOut($JSON);
		}
		// creates a new empty channel
		$q=safe_r_sql("delete from TVOut where TVOId=$Id");
		DelParameter('TVOUT-Reload-'.$Id);
		$JSON['error']=0;
		break;
	case 'newchannel':
		// creates a new empty channel
		$Id=1;
		$q=safe_r_sql("select max(TVOId) as MaxId from TVOut");
		if($r=safe_fetch($q)) {
			$Id=$r->MaxId+1;
		}
		safe_w_sql("insert into TVOut set
			TVOId=$Id,
			TVOLastUpdate=now()");
		$JSON['error']=0;
		break;
	case 'newsplit':
		// creates a new empty channel
		if(empty($_REQUEST['id']) or !($Id=intval($_REQUEST['id']))) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("select max(TVOSide) as MaxId from TVOut where TVOId=$Id");
		$NewId=1;
		if($r=safe_fetch($q)) {
			$NewId=$r->MaxId+1;
		}
		safe_w_sql("insert into TVOut set
			TVOId=$Id,
			TVOSide=$NewId,
			TVOLastUpdate=now()");
		$JSON['error']=0;
		break;
	case 'update':
		if(empty($_REQUEST['fld']) or !isset($_REQUEST['val']) or empty($_REQUEST['id']) or !isset($_REQUEST['side'])) {
			JsonOut($JSON);
		}
		$SQL='';
		$Id=intval($_REQUEST['id']);
		$Side=intval($_REQUEST['side']);
		$Field='';
		$AllIds=false;

		switch($_REQUEST['fld']) {
			case 'Name':
				$Field='TVOName';
				$AllIds=true;
				break;
			case 'Message':
				$Field='TVOMessage';
				break;
			case 'Side':
				$Field='TVOSide';
				$q=safe_r_sql("select TVOSide from TVOut where TVOId=$Id and TVOSide=".intval($_REQUEST['val']));
				if(safe_num_rows($q)) {
					// the new side already exists!!! so Error
					$JSON['msg']=get_text('NoSameOrders', 'Errors');
					JsonOut($JSON);
				}
				$JSON['NewOrder']=intval($_REQUEST['val']);
				break;
			case 'Height':
				$Field='TVOHeight';
				break;
			case 'Url':
				$Field='TVOUrl';
				break;
			case 'File':
				$Field='TVOFile';
				break;
			case 'Code':
				$Field='TVOTourCode';

				$JSON['TVRules']=array(0=>get_text('TVSelectPage', 'Tournament'));
				$q=safe_r_sql("select TVRId, TVRName from TVRules where TVRTournament=".intval(getIdFromCode($_REQUEST['val']))." order by TVRId");
				while($r=safe_fetch($q)) {
					$JSON['TVRules'][$r->TVRId]=$r->TVRName;
				}
				break;
			case 'Rule':
				$Field='TVORuleId';
				break;
			case 'Status':
				$Field='TVORuleType';
				break;
			case 'Reload':
				setParameter('TVOUT-Reload-'.$Id, 1);
				$JSON['error']=0;
				break;
			case 'Path':
				$Path=trim($_REQUEST['val']);
				if(is_dir($Path)) {
					$Bits=explode(DIRECTORY_SEPARATOR, $Path);
					switch(strtolower($Bits[1])) {
						case 'etc':
						case 'usr':
						case 'windows':
							JsonOut($JSON);
							break;
					}
					if(substr($Path,-1)!=DIRECTORY_SEPARATOR) {
						$Path.=DIRECTORY_SEPARATOR;
					}
					setParameter('TVOUT-Path', $Path);
					$JSON['error']=0;
				}
				break;
		}

		if($Field) {
			$SQL="update ignore TVOut set $Field=".StrSafe_DB($_REQUEST['val'])." where TVOId=$Id".($AllIds ? "" : " and TVOSide=$Side");
			safe_w_sql($SQL);
			$JSON['error']=0;
		}
		break;
}

JsonOut($JSON);
