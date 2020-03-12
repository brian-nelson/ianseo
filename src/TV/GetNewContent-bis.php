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
	$quadro=(empty($_GET['Quadro']) ? 0 : $_GET['Quadro']);

	// get the segment
	$t=safe_r_sql("select * from TVParams where TVPId=$SegmentId AND TVPTournament=$TourId");
	if(!($u=safe_fetch($t))) die('No data');

	// get the rule
	$q=safe_r_sql("select * from TVRules where TVRId=$RuleId AND TVRTournament=$TourId");
	if(!($RULE=safe_fetch($q))) die('No data');

	// set the correct defines for the torunament
	set_defines($RULE->TVRTournament);

	// set the event and the div number
	$u->TVPEventInd = $Event;
	$u->TVPEventTeam = $Event;
	if(!empty($_GET['Session']))
		$u->TVPSession=intval($_GET['Session']);

	$RotMatches=false;
	$tmp=genera_html_rot($u, $RULE);

	$pagina='';
	foreach($tmp as $key => $Event) {
		if( $Event['type']=='DB') {
			$pagina .= '<table width="100%">'."\n";
			$pagina .= $Event['cols'];
			$pagina .= $Event['head'];
			$pagina .= $Event['fissi'];
			//$pagina .= '</table>'."\n";
		}

		// il riquadro di riempimento
		$pagina .= '<tbody id="scrolldiv'.$quadro.'" title="'.$Event['type'].$quadro.'">'."\n";
		if($Event['basso']) {
			$pagina .= '<tbody width="100%" id="scrolltab'.$quadro.'" class="'.$Event['type'].'">'."\n";
			$pagina .= $Event['cols'];
			$pagina .= $Event['basso'];
			$pagina .= '</tbody>'."\n";
		} else {
			$pagina .= '<div id="scrolltab'.$quadro.'"></div>'."\n";
		}
		$pagina .= '</tbody>'."\n";
	}

	echo str_replace('GetNewContent.php', basename(__FILE__), $pagina);

?>