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

//	if(empty($_SESSION['TourId'])) {
//		include_once('Common/UpdatePreOpen.inc.php');
//		UpdatePreOpen($TourId);
//		$Tour=CreateTourSession($TourId);
//	}

	$pagine=array();

	// get the defaults of the rule
	$q=safe_r_sql("select TVRules.*, ToPrintLang from TVRules inner join Tournament on TVRTournament=ToId where TVRId=$RuleId AND TVRTournament=$TourId");
	if(!($RULE=safe_fetch($q))) printCrackError();

	@define('PRINTLANG', $RULE->ToPrintLang);


	// set the correct defines for the torunament
	set_defines($RULE->TVRTournament);

	// Estraggo gli spezzoni di regola
	$Select
		= "SELECT * FROM TVSequence "
		. "WHERE TVSRule=$RuleId AND TVSTournament=$TourId "
		. "ORDER BY TVSOrder ";

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
			$pagina .= '<div class="blocco'.($Event['type']=='MM'?' MM':'').'" id="scrolltop'.$quadro.'" width="100%">'."\n";
			if( $Event['type']=='DB') {
				$pagina .= '<table width="100%" id="scrollhead'.$quadro.'">'."\n";
				$pagina .= $Event['cols'];
				$pagina .= $Event['head'];
				$pagina .= $Event['fissi'];
				$pagina .= '</table>'."\n";
			}

			// il riquadro di riempimento
			$pagina .= '<div id="scrolldiv'.$quadro.'" title="'.$Event['type'].$quadro.'" width="100%">'."\n";
			if($Event['basso']) {
				$pagina .= '<table width="100%" id="scrolltab'.$quadro.'" class="'.$Event['type'].'" title="'.$Event['type'].$quadro.'">'."\n";
				$pagina .= $Event['cols'];
				$pagina .= $Event['basso'];
				$pagina .= '</table>'."\n";
			} else {
				$pagina .= '<div id="scrolltab'.$quadro.'" width="100%"></div>'."\n";
			}
			$pagina .= '</div>'."\n";
			$pagina .= '</div>'."\n";

			$JavaScript[]=sprintf($Event['js'], $quadro);
			$Styles[$quadro]=$Event['style'];

			if(!$firstdiv) {
				$m=array();
				$firstdiv=str_replace(
					array('image_0', 'id="scrolltop'.$quadro.'"','id="scrolldiv'.$quadro.'"','id="scrolltab'.$quadro.'"','title="'.$Event['type'].$quadro.'"'),
					array('image_end', 'id="scrolltopEnd"','id="scrolldivEnd"','id="scrolltabEnd"','title="'.$Event['type'].'End"'),
					$pagina);
				$JavaScript[]=str_replace(
					array("[End]",'image_0'),
					array("['End']",'image_end'),
					sprintf($Event['js'], "End"));
				$Styles['End']=$Event['style'];
			}

			$quadro++;
		}
	}

	include('Common/Templates/head-html-rot5.php');

// 	print $pagina . $firstdiv;

	include('Common/Templates/tail-html-rot.php');



function Genera_content_rot($Content, $Segment) {
	static $static=0;
	global $CFG;
	$ret=array();

	$ret['cols']='';
	$ret['head']='';
	$ret['fissi']='';
	$ret['type']='MM';
	$ret['style']='';
	$ret['js'] = 'timeStop[%1$s]='.intval($Segment->TVSTime*1000/$Segment->TVSScroll).";\n";
	$ret['js'].= 'timeScroll[%1$s]='.$Segment->TVSScroll.";\n";
	if($Segment->TVSFullScreen) $ret['js'].= 'resize(document.getElementById(\'image_'.($static).'\'))'.";\n";

	switch($Content->TVCMimeType) {
		case 'image/gif':
		case 'image/jpeg':
		case 'image/png':
			$ret['basso']='<tr><td valign="middle" align="center"><img id="image_'.($static++).'" src="Photos/TV-'.getCodeFromId($Content->TVCTournament).'-'.$Content->TVCId.'.jpg"></td></tr>';
			break;
		case 'text/html':
			$ret['basso']='<tr><td valign="middle" align="center">'.$Content->TVCContent.'</td></tr>';
			break;
		default:
			$ret['basso']='<tr><td>Unknown MIME-TYPE</td></tr>';
	}

	return array($ret);
}

