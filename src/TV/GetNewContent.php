<?php

	define('debug',false);	// settare a true per l'output di debug

	define('IN_PHP', true);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	if (empty($_REQUEST['Rule'])) printCrackError();
	require_once('Common/Fun_FormatText.inc.php');
	require_once('TV/Fun_HTML.local.inc.php');

	if(empty($_GET['Segment']) or empty($_GET['Rule']) or empty($_GET['Tour']) or (empty($_GET['Event']) and empty($_GET['Session']))) die('No data!');

	$SegmentId=intval($_GET['Segment']);
	$RuleId=intval($_GET['Rule']);
	$TourId=intval($_GET['Tour']);
    checkACL(AclOutput,AclReadOnly, false, $TourId);
	$Event='';
	if(!empty($_GET['Event']))
		$Event=preg_replace('/[^a-zA-Z0-9_-]*/','',$_GET['Event']);
	$Phase='';
	if(isset($_GET['Phase']))
		$Phase=preg_replace('/[^a-zA-Z0-9_-]*/','',$_GET['Phase']);
	$quadro=(empty($_GET['Quadro']) ? 0 : $_GET['Quadro']);

	// get the segment
	$t=safe_r_sql("select TVParams.*, ToPrintLang from TVParams inner join Tournament on ToId=TVPTournament where TVPId=$SegmentId AND TVPTournament=$TourId");
	if(!($u=safe_fetch($t))) die('No data');

	@define('PRINTLANG', $u->ToPrintLang);

	// get the rule
	$q=safe_r_sql("select * from TVRules where TVRId=$RuleId AND TVRTournament=$TourId");
	if(!($RULE=safe_fetch($q))) die('No data');

	// set the correct defines for the tournament
	set_defines($RULE->TVRTournament);

	// set the event and the div number
	$u->TVPEventInd = $Event;
	$u->TVPEventTeam = $Event;
	$u->TVPPhasesTeam = $Phase;
	$u->TVPPhasesInd = $Phase;
	if(!empty($_GET['Session']))
		$u->TVPSession=intval($_GET['Session']);

	$RotMatches=false;
	$tmp=genera_html_rot($u, $RULE);

	$pagina='';
	$Style='';
	foreach($tmp as $key => $Event) {
		if( $Event['type']=='DB') {
			$pagina .= '<table width="100%">'."\n";
			$pagina .= $Event['cols'];
			$pagina .= $Event['head'];
			$pagina .= $Event['fissi'];
			$pagina .= '</table>'."\n";
		}

		// il riquadro di riempimento
		$pagina .= '<div id="scrolldiv'.$quadro.'" title="'.$Event['type'].$quadro.'" width="100%">'."\n";
		if($Event['basso']) {
			$pagina .= '<table width="100%" id="scrolltab'.$quadro.'" class="'.$Event['type'].'">'."\n";
			$pagina .= $Event['cols'];
			$pagina .= $Event['basso'];
			$pagina .= '</table>'."\n";
		} else {
			$pagina .= '<div id="scrolltab'.$quadro.'" width="100%"></div>'."\n";
		}
		$pagina .= '</div>'."\n";

		if(!empty($Event['style']['TV_Content_BGColor'])) $Style.= "table {background-color: {$Event['style']['TV_Content_BGColor']}; }\n";
		if(!empty($Event['style']['TV_Carattere'])) {
			$Style.=  "table {font-size: {$Event['style']['TV_Carattere']}px; }\n";
			$Style.=  "table tr { background-color:{$Event['style']['TV_TR_BGColor']}; color: {$Event['style']['TV_TR_Color']}; }\n";
			$Style.=  "table tr.Next { background-color:{$Event['style']['TV_TRNext_BGColor']}; color: {$Event['style']['TV_TRNext_Color']};}\n";
			$Style.=  "table th { background-color:{$Event['style']['TV_TH_BGColor']}; color: {$Event['style']['TV_TH_Color']};}\n";
			$Style.=  "table th.Title { background-color:{$Event['style']['TV_THTitle_BGColor']}; color: {$Event['style']['TV_THTitle_Color']};}";
		}
	}

	if(!empty($_REQUEST['output']) and $_REQUEST['output']=='svg') {
		$pagina='<style>'.$Style.'</style>'.$pagina;
	}

	echo $pagina;

?>