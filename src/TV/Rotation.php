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

// 	debug_svela($pagine);

	include('Common/Templates/head-html-rot-bis.php');

	print $pagina . $firstdiv;

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
/*

Vecchia gestione video ianseo:
	Regole TV
	con parametri tempo e grafica e
		sequenza pagine in rotazione definiti in EditRule.php
	uscita video con ridimensionamenti per finestra output gestiti secondo html

Nuova proposta di Visualizzazione:

uscite video definite canali da settare on/off da regia
ogni canale preimpostato per risoluzione video, header, sottopancia, 4 corner per loghi, colore background per uscita come grafica in sovrapposizione, colore backgroud2 per uscita come ianseo attuale righe dispari, 2 colori testo per testo e ombreggiatura…



RISOLUZIONI VIDEO

	es: TV SD pal 768 x 576 (4:3), ntsc 720 x 480 (3:2)
	comp. e videoproiettori 4:3 svga 800 x 600, xga 1024x768, sxga 1280 x 1024
	comp. e videoproiettori 16:10 wxga 1280 x 800 - 1440 x 900 - 1280 x 768
	tv HD 16:9 HD720 1280 x 720 HD1080 1920 x 1080 e il vecchio HD Ready Wxga 1366 x 768

in ogni caso le risoluzioni più utilizzate sono sostanzialmente
pal e HD1080 per grafica broadcast
800 x 600 e 1024 x 768 per videoproiettori
HD Ready e HD1080 per televisori LCD

a seconda della attivazione o meno di header e sottopancia avremo risoluzioni variate del frame di uscita centrale. comunque possiamo definire altezza di header e sottopancia in termini percentuali rispetto alla risoluzione del canale.
ATTENZIONE!!! potrebbe essere interessante l'utilizzo degli schermi ruotati di 90 gradi per permettere un numero maggiore di righe visualizzate, quindi prevedere in partenza l'inversione tra altezza e larghezza.

i corner per loghi li attiverei solamente in presenza di header e sottopancia per evitare flickering video.
dimensioni uguali a altezza header o sottopancia

COSA FAR USCIRE SUI CANALI ATTIVATI

i contenuti di base sono:
 - generate dal db -
Classifiche (le vecchie Pagine in Rotazione di Ianseo)
Lista Piazzole (anche specifiche dei match di finale)
Premiazioni
Scheduling

- altre -
Messaggi di Testo (salvataggio del testo e relativi parametri o generazione immagine)
Immagine (singola o slideshow)
Video

da valutare se centralizzare l'archiviazione e la lettura di immagini e video. Personalmente opterei per un dump sul pc che pilota l'uscita video e controllo ciclico di eventuali aggiornamenti da effettuare

Nella categoria immagini distinguerei un sottogruppo per la parte advertising. Loghi e img a risoluzioni differenti per l'inclusione secondo parametri configurabili - futura gestione stile campagna ad-server e possibile integrazione con stampe classifiche, pettorali, cartelli e quant'altro.


la sequenza di una selezione di contenuti (es: immagine titolo - classifica OLSM - immagine - classifica OLSF - advertising - schedule sessione - video - ….) la identifichiamo come PROGRAMMAZIONE
il mantenere gli advertising separati permette di personalizzare le uscite (es:  classifiche con loghi FITA e LOC per canale principale   -  classifica con logo Sponsor per canale dedicato in uno stand …)

HEADER e SOTTOPANCIA:
riservati a contenuti specifici:
Header solo per Titoli (Testi tipo 1) o Advertising + Eventuali uno o due loghi nei due angoli superiori
Sottopancia solo per Testi Statici (Testi tipo 2), Testi a scorrimento orizzontale (Testi tipo 3), scheduling statico o a scorrimento, advertising + eventuali uno o due loghi nei due angoli inferiori. Eventuale scorrimento classifiche quando utilizzati come sovrapposizione su TV

*/
?>