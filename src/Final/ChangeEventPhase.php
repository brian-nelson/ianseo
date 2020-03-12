<?php
/*							- ChangeEventPhase.php -
	Ritorna i matchno di una fase di un evento.
*/
	define('debug',false);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Fun_Final.local.inc.php');

	$Ev=isset($_REQUEST['Ev']) ? $_REQUEST['Ev'] : null;
	$Ph=isset($_REQUEST['Ph']) ? $_REQUEST['Ph'] : null;
	$Team = (isset($_REQUEST['TeamEvent']) ? $_REQUEST['TeamEvent'] : 0);

	if (is_null($Ev) || is_null($Ph) || !CheckTourSession())
	{
		print get_text('CrackError');
		exit;
	}
    checkACL(array(AclIndividuals,AclTeams, AclOutput), AclReadOnly, false);

	$Errore=0;

	// tiro fuori i match
	$rs=GetFinMatches($Ev,$Ph,null,$Team,true);

	//$rs=safe_r_sql($query);

	if (!$rs)
		$Errore=1;

	if($Ph==-1) {
		require_once('Common/Lib/CommonLib.php');
		$MatchNames=getPoolMatches();
		$MatchNamesWA=getPoolMatchesWA();
	}

	if (!debug)
		header('Content-Type: text/xml');

	$xml= '<response>' . "\n";
		$xml.='<error>' . $Errore . '</error>' . "\n";

		if ($Errore==0)
		{
			while ($row=safe_fetch($rs))
			{
				$Target= ltrim($row->target1, '0');
				if($row->target1!=$row->target2) $Target.='/'.ltrim($row->target2, '0');
				$title=$Target;
				if($Ph==-1) {
					if($row->elimType==3 and isset($MatchNames[$row->match1])) {
						$title=$MatchNames[$row->match1];
					} elseif($row->elimType==4 and isset($MatchNamesWA[$row->match1])) {
						$title=$MatchNamesWA[$row->match1];
					} else {
						$title=get_text( $row->phase . '_Phase');
					}
				}
				$xml
					.='<match>' . "\n"
						. '<matchno1>' .  $row->match1 . '</matchno1>' . "\n"
						. '<name1><![CDATA[' . $title . ' - ' . $row->name1 . ']]></name1>' . "\n"
						. '<matchno2>' .  $row->match2 . '</matchno2>' . "\n"
						. '<name2><![CDATA[' . $row->name2 . ']]></name2>' . "\n"
					. '</match>' . "\n";
			}
		}

	$xml.='</response>' . "\n";

	print $xml;
?>