<?php
/*
															- MakeSnapshot.php -
	Genera lo snapshot
*/

	define('debug',false);	// settare a true per l'output di debug
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');

	$Errore=0;
	$xmlReturn='';
	if (!IsBlocked(BIT_BLOCK_QUAL))
	{
		if(isset($_REQUEST["Session"]) && preg_match("/^[1-9]{1}$/",$_REQUEST["Session"]) &&
			isset($_REQUEST["Distance"]) && preg_match("/^[1-8]{1}$/",$_REQUEST["Distance"]) &&
			isset($_REQUEST["fromTarget"]) && preg_match("/^[0-9]{1,3}$/",$_REQUEST["fromTarget"]) &&
			isset($_REQUEST["toTarget"]) && preg_match("/^[0-9]{1,3}$/",$_REQUEST["toTarget"]))
		{
			if(isset($_REQUEST["numArrows"]) && preg_match("/^[0-9]{1,2}$/",$_REQUEST["numArrows"]))
			{
				if($_REQUEST["numArrows"]==0)
				{
					for($i=3; $i<=30; $i+=3 )
					{
						$xmlReturn .= '<numArrows>'  . useArrowsSnapshot($_REQUEST["Session"], $_REQUEST["Distance"], $_REQUEST["fromTarget"], $_REQUEST["toTarget"],$i) . '</numArrows>' . "\n";
					}
				}
				else
					$xmlReturn .= '<numArrows>'  . useArrowsSnapshot($_REQUEST["Session"], $_REQUEST["Distance"], $_REQUEST["fromTarget"], $_REQUEST["toTarget"],$_REQUEST["numArrows"]) . '</numArrows>' . "\n";
			}
			else
				$xmlReturn .= '<numArrows>'  . recalSnapshot($_REQUEST["Session"], $_REQUEST["Distance"], $_REQUEST["fromTarget"], $_REQUEST["toTarget"]) . '</numArrows>' . "\n";
		}
		else
			$Errore = 1;

	}
	else
		$Errore=1;

	// produco l'xml di ritorno

	if (!debug)
		header('Content-Type: text/xml');

	print '<response>' . "\n";
	print '<error>' . $Errore . '</error>' . "\n";
	print '<msg><![CDATA[' . ($Errore==1 ? get_text('MakeSnapshotError','Tournament') : get_text('MakeSnapshotOk','Tournament')) . ']]></msg>';
	print $xmlReturn;
	print '</response>' . "\n";


?>