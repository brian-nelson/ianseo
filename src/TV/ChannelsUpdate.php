<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1);

if(empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'save':
		if(!empty($_REQUEST['name'])) {
			safe_w_sql("insert into TVOut set
				TVOName=".StrSafe_DB($_REQUEST['name']).",
				TVOMessage=".StrSafe_DB($_REQUEST['msg']).",
				TVOUrl=".StrSafe_DB($_REQUEST['url']).",
				TVOTourCode=".StrSafe_DB($_REQUEST['tour']).",
				TVORuleId=".intval($_REQUEST['rule']).",
				TVORuleType=".intval($_REQUEST['status']).",
				TVOLastUpdate=now()");
			$JSON['newID']=safe_w_last_id();
		}
		$JSON['error']=0;
		break;
	case 'update':
		$SQL='';
		switch(true) {
			case !empty($_REQUEST['Name']):
				$k=key($_REQUEST['Name']);
				$v=current($_REQUEST['Name']);
				$SQL="update TVOut set TVOName=".StrSafe_DB($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				break;
			case !empty($_REQUEST['Message']):
				$k=key($_REQUEST['Message']);
				$v=current($_REQUEST['Message']);
				$SQL="update TVOut set TVOMessage=".StrSafe_DB($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				break;
			case !empty($_REQUEST['Url']):
				$k=key($_REQUEST['Url']);
				$v=current($_REQUEST['Url']);
				$SQL="update TVOut set TVOUrl=".StrSafe_DB($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				break;
			case !empty($_REQUEST['Tournament']):
				$k=key($_REQUEST['Tournament']);
				$v=current($_REQUEST['Tournament']);
				$SQL="update TVOut set TVOTourCode=".StrSafe_DB($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				$JSON['TVRules']=array();
				$q=safe_r_sql("select TVRId, TVRName from TVRules where TVRTournament=".intval(getIdFromCode($tmp[1]))." order by TVRId");
				while($r=safe_fetch($q)) {
					$JSON['TVRules'][$r->TVRId]=$r->TVRName;
				}
				break;
			case !empty($_REQUEST['Rule']):
				$k=key($_REQUEST['Rule']);
				$v=current($_REQUEST['Rule']);
				$SQL="update TVOut set TVORuleId=".intval($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				break;
			case !empty($_REQUEST['Status']):
				$k=key($_REQUEST['Status']);
				$v=current($_REQUEST['Status']);
				$SQL="update TVOut set TVORuleType=".intval($v)." where TVOId=".intval($k);
				safe_w_sql($SQL);
				$JSON['error']=0;
				break;
			case !empty($_REQUEST['Reload']):
				require_once('Common/Lib/Fun_Modules.php');
				setModuleParameter('TVOUT', 'Reload', 1);
				$JSON['error']=0;
				break;
		}

		break;
}

JsonOut($JSON);