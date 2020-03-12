<?php
/* *********************************
 * Queste pagine sono dedicate alla persona che più cara mi fu nel tiro con l'arco
 *
 * Renato Doni
 *
 * Con te se ne va un grande amico, un grande consigliere, un grande arciere
 *
 * Con te se ne va un pezzo di storia
 *
 * Ma soprattutto con te se ne va uno degli ispiratori del tiro con l'arco "adulto",
 * cresciuto e maturato nel vero spirito olimpico, come avrebbe voluto De Coubertin
 *
 * Che tu possa dall'alto guidare tutti noi arcieri sulla giusta via
 *
 * Christian Deligant
 *
 * =========
 *
 * These pages are to honor the person I cared most in archery
 *
 * Reanto Doni
 *
 * You have been a great friend, a great counsellor, a great archer
 *
 * You were a huge piece of modern archery history
 *
 * The saddest thing of all is that you were one of the founders of the "Adult" archery,
 * created and grown in the true Olympic Spirit, as would have De Coubertin wanted
 *
 *  May your spirit guide all us archers through the right path from above
 *
 *  Christian Deligant
 *
 * */


	define('debug',false);	// settare a true per l'output di debug

	define('IN_PHP', true);

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	if (empty($_REQUEST['Rule'])) printCrackError();
	require_once('Common/Fun_FormatText.inc.php');
	require_once('TV/Fun_HTML.local.inc.php');

	$RuleId=intval($_REQUEST['Rule']);
	$TourCode=stripslashes($_REQUEST['Tour']);
	$TourId=getIdFromCode($TourCode);
	$RuleOrder=(empty($_REQUEST['Order']) ? 0 : intval($_REQUEST['Order']));
    checkACL(AclOutput,AclReadOnly,true,$TourId);

	$pagine=array();

	// get the defaults of the rule
	$q=safe_r_sql("select * from TVRules where TVRId=$RuleId AND TVRTournament=$TourId");
	if(!($RULE=safe_fetch($q))) printCrackError();

	// set the correct defines for the torunament
	set_defines($RULE->TVRTournament);

	// Estraggo gli spezzoni di regola
	$Select
		= "SELECT * FROM TVSequence WHERE TVSRule=$RuleId AND TVSTournament=$TourId ORDER BY TVSOrder";

	$Rs=safe_r_sql($Select);
	if(!safe_num_rows($Rs)) printcrackerror();

	$RotMatches=false;
	while($r=safe_fetch($Rs)) {
		$tmp='';
		switch($r->TVSTable) {
			case 'DB':
				$t=safe_r_sql("select * from TVParams where TVPId=$r->TVSContent AND TVPTournament=$r->TVSTournament");
				$tmp=genera_html_rot(safe_fetch($t), $RULE);
				break;
			case 'MM':
				$t=safe_r_sql("select * from TVContents where TVCId=$r->TVSContent AND TVCTournament=" . ($r->TVSCntSameTour==1 ? $r->TVSTournament : "-1"));
				$tmp=genera_content_rot(safe_fetch($t), $r);
				break;
		}

		if($tmp) $pagine[]=$tmp;
	}

	$pagina='';
	$Styles=array();
	$JavaScript=array();

	$quadro=0;
	$firstdiv='';
	foreach($pagine as $Title => $Events) {
		foreach($Events as $key => $Event) {
			if($quadro and $quadro<$RuleOrder) {
				$quadro++;
				continue;
			}

			$pagina .= '<div class="blocco'.($Event['type']=='MM'?' MM':'').'" id="scrolltop'.$quadro.'" width="100%">'."\n";
			if( $Event['type']=='DB') {
				$pagina .= '<table width="100%">'."\n";
				$pagina .= $Event['cols'];
				$pagina .= $Event['head'];
				$pagina .= $Event['fissi'];
				$pagina .= $Event['basso'];
				$pagina .= '</table>'."\n";
			} else {
				$pagina .= '<table width="100%">'."\n";
				$pagina .= $Event['basso'];
				$pagina .= '</table>'."\n";
			}
			$pagina .= '</div>'."\n";

			if(!$quadro) {
				$firstdiv=$pagina;
				if($quadro<$RuleOrder) $pagina='';
			}


			$Styles[$quadro]=$Event['style'];

			$quadro++;
			if(!empty($pagina)) break 2;
		}
	}

	if(!$pagina) {
		$pagina=$firstdiv;
		$quadro=0;
	}
	$NextRule=$CFG->ROOT_DIR.'TV/'.basename(__FILE__).go_get(array('Rule'=>$RuleId, 'Tour'=>$TourCode, 'Order'=>$quadro));

	include('Common/Templates/head-html-lightrot.php');

	print $pagina;

	include('Common/Templates/tail-html-rot.php');


