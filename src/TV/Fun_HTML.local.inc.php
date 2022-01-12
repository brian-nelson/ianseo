<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Lib/Fun_DateTime.inc.php');

/*
													- Fun_HTML.local.inc.php -
	Contiene le funzioni e le variabili globali per la sezione HTML
*/

	if(!empty($_SESSION['TourId'])) {
		$tourType=null;
		$q="SELECT ToType,ToTypeSubRule,ToPrintLang FROM Tournament WHERE ToId='{$_SESSION['TourId']}'";
		$r=safe_r_sql($q);
		$xxx=safe_fetch($r);
		$tourType=(empty($xxx->ToType) ? '' : $xxx->ToType);
		$tourSubRule=(empty($xxx->ToTypeSubRule) ? '' : $xxx->ToTypeSubRule);

		@define('PRINTLANG', $xxx->ToPrintLang);

		$Arr_RotPages = array
		(
			0 => array(get_text('ResultIndClass','Tournament'), 'Qualification_Ind_rot.php'),
			1 => array(get_text('ResultSqClass','Tournament'),'Qualification_Team_rot.php'),
			2 => array(get_text('ResultIndAbs','Tournament'),'AbsQualification_Ind_rot.php'),
			3 => array(get_text('ResultSqAbs','Tournament'),'AbsQualification_Team_rot.php'),
			4 => array(get_text('Elimination'),'Elimination_Ind_rot.php'),
			5 => array(get_text('RankingInd'),'Final_Ind_rot.php'),
			6 => array(get_text('RankingSq'),'Final_Team_rot.php'),
			7 => array(get_text('StartlistSession','Tournament'),'Session_rot.php')
		);

		$Arr_Pages = array
		(
			'ALFA'  => get_text('StartlistAlfabetical','Tournament'),//'Session_rot.php')
			'LIST'  => get_text('StartlistSession','Tournament'),//'Session_rot.php')
			'LSPH'  => get_text('StartlistSessionPicture','Tournament'),//'Session_rot.php')
			'QUAL'  => get_text('ResultIndClass','Tournament'), //'Qualification_Ind_rot.php'),
			'QUALS' => get_text('ResultIndClassSnap','Tournament'), //'Qualification_Ind_rot.php'),
			'QUALT' => get_text('ResultSqClass','Tournament'),//'Qualification_Team_rot.php'),
			'QUALC' => get_text('ResultIndSubClass','Tournament'), //'Qualification_Ind_rot.php'),
			'ABS'   => get_text('ResultIndAbs','Tournament'),//'AbsQualification_Ind_rot.php'),
			'ABSS'  => get_text('ResultIndAbsSnap','Tournament'),//'AbsQualification_Ind_rot.php'),
			'ABST'  => get_text('ResultSqAbs','Tournament'),//'AbsQualification_Team_rot.php'),
			'ELIM'  => get_text('Elimination'),//'Elimination_Ind_rot.php'),
			'FIN'   => get_text('I-Session', 'Tournament'),//'Final_Ind_rot.php'),
			'FINT'  => get_text('T-Session', 'Tournament'),//'Final_Team_rot.php'),
			'MEDL'  => get_text('MedalList'),//'MedalList_rot.php')
			'RAND'  => get_text('AthleteSummary','Tournament'),//'Session_rot.php')
			'RANK'  => get_text('FinalRankInd','Tournament'),//'Session_rot.php')
			'RANKT'  => get_text('FinalRankTeams','Tournament'),//'Session_rot.php')

		);

		if(stripos($_SESSION['TourName'],'bundesliga')!==false) {
			$Arr_Pages['BLABS']= 'Bundesliga Rounds';
		}

	// se è un championships olandese indoor
		if ($tourType==6 && in_array($tourSubRule,array('','SetChampionship')))
		{
			$Arr_Pages['NLCLST']=get_text('StartlistSession','Tournament') . ' NL - Championship';
			$Arr_Pages['NLCABS']='Elim Rank ' . 'NL - Championship';
		}

	}


/*
	- MakeEventFilter($Event)
	Ritorna la condizione per il filtro degli eventi nei rotate.
	$Event proviene dal file di regole
*/
	function MakeEventFilter($Event) {
		if(!$Event) return '';

		$Ret = "=" . StrSafe_DB($Event) . " ";

		$Arr_Ev = explode('|',$Event);
		if (count($Arr_Ev)>1) {
			sort($Arr_Ev);
			foreach ($Arr_Ev as $Key => $Value)
				$Arr_Ev[$Key]=StrSafe_DB($Value);

			$Ret = "IN (" . implode(',',$Arr_Ev) . ") ";
		}

		return $Ret;
	}

/*
	- MakePhaseFilter($Phase)
	Ritorna la condizione per il filtro delle fasi nei rotate.
	$Phase proviene dal file di regole
*/
	function MakePhaseFilter($Phase)
	{
		$Ret='';
		if (trim($Phase)!='')
		{
			$Arr_Ph = explode('|',$Phase);
			if (is_array($Arr_Ph))	// IN
			{
				foreach ($Arr_Ph as $Key => $Value)
					$Arr_Ph[$Key]=StrSafe_DB($Value);

				$Ret = "IN (" . implode(',',$Arr_Ph) . ") ";
			}
		}
		return $Ret;
	}

	function decode_chain($row, $edit=true) {
		$ret='';
		$ret.='<tr>';
		$ret.='<td>'.$row->TVSOrder.'</td>';
		if($edit) {
			$ret.='<td>';
			$ret.='<a href="'.go_get(array('ChainId'=>$row->TVSId, 'ChainType'=>$row->TVSTable)).'">edit</a>';
			$ret.='&nbsp;<a href="'.go_get('ChainDel',$row->TVSId).'" onclick="return confirm(\''.get_text('MsgAreYouSure').'\')">del</a>';
			if($row->TVSOrder!=1)
				$ret.='&nbsp;<a href="'.go_get(array('ChainId'=>$row->TVSId,'from'=>$row->TVSOrder,'to'=>$row->TVSOrder-1)).'">up</a>';
			else
				$ret.='&nbsp;<i>up</i>';
			if(!$row->last)
				$ret.='&nbsp;<a href="'.go_get(array('ChainId'=>$row->TVSId,'from'=>$row->TVSOrder,'to'=>$row->TVSOrder+1)).'">down</a>';
			else
				$ret.='&nbsp;<i>down</i>';
			$ret.='</td>';
		}

		if($row->TVSTable=='DB') {
			$q=safe_r_sql("select * from TVParams where TVPId=$row->TVSContent AND TVPTournament=$row->TVSTournament");
			if($r=safe_fetch($q)) {
				$ret.='<td>'.get_text('Tournament','Tournament').'</td>';
				$ret.='<td>'.$r->TVPPage.'</td>';
				$w=array();
				if(strlen($r->TVPEventInd)) $w[]='Events: '.$r->TVPEventInd;
				if(strlen($r->TVPEventTeam)) $w[]='Events: '.$r->TVPEventTeam;
				if(strlen($r->TVPPhasesInd)) $w[]='Phases: '.$r->TVPPhasesInd;
				if(strlen($r->TVPPhasesTeam)) $w[]='Phases: '.$r->TVPPhasesTeam;
				if($r->TVPColumns) $w[]='Columns: '.$r->TVPColumns;
				$ret.='<td>'.($w?implode('<br/>',$w):'&nbsp;').'</td>';
				$ret.='<td>'.($r->TVP_Carattere?'&nbsp;':'X').'</td>';
			}
		} elseif($row->TVSTable=='MM') {
			$q=safe_r_sql("select * from TVContents where TVCId=$row->TVSContent AND TVCTournament=" . ($row->TVSCntSameTour==1 ? $row->TVSTournament:'-1'));
			if($r=safe_fetch($q)) {
				$ret.='<td>'.get_text('Multimedia','Tournament').'</td>';
				$ret.='<td>'.$r->TVCName.'</td>';
				$ret.='<td>'.$row->TVSTime.'</td>';
				$ret.='<td>'.$row->TVSScroll.'</td>';
			}
		}
		$ret.='</tr>';
		return $ret;
	}

	function set_defines($TourId) {
		return;
//		$tt=StrSafe_DB($TourId);
//
//		$q="
//			SELECT IFNULL(MAX(SesTar4Session),0) AS max_session
//			FROM
//				Session
//			WHERE
//				SesTournament={$tt}
//		";
//		$t=safe_r_sql($q);
//		if($u=safe_fetch($t)) {
			//define ("TargetNoPadding",3);
//		}

	}

	function get_rot_grids($n,$fase) {
		$grid=array();
		switch($n % 4) {
			case '1':
				$grid[0]='<td class="top">&nbsp;</td><td class="left">&nbsp;</td>';
				$grid[1]='<td class="">&nbsp;</td><td class="left bottom">&nbsp;</td>';
				$grid[2]='<td class="">&nbsp;</td><td class="left">&nbsp;</td>';
				break;
			case '2':
				$grid[0]='<td class="bottom ">&nbsp;</td><td class="left">&nbsp;</td>';
				$grid[1]='<td class="">&nbsp;</td><td class="left">&nbsp;</td>';
				$grid[2]='<td class="">&nbsp;</td><td class="left">&nbsp;</td>';
				break;
			default:
				$grid[0]='<td>&nbsp;</td><td>&nbsp;</td>';
				$grid[1]='<td>&nbsp;</td><td>&nbsp;</td>';
				$grid[2]='<td>&nbsp;</td><td>&nbsp;</td>';
		}

		if($fase >= 16) {
			switch($n % 8) {
				case '1':
					$grid[0].='<td class="">&nbsp;</td>';
					$grid[1].='<td class="">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '2':
				case '4':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '3':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left bottom">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '5':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left">&nbsp;</td>';
					$grid[2].='<td class="">&nbsp;</td>';
					break;
				default:
					$grid[0].='<td class="">&nbsp;</td>';
					$grid[1].='<td class="">&nbsp;</td>';
					$grid[2].='<td class="">&nbsp;</td>';
			}
		}
		if($fase >= 32) {
			switch($n % 16) {
				case '3':
					$grid[0].='<td class="">&nbsp;</td>';
					$grid[1].='<td class="">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '4':
				case '5':
				case '6':
				case '8':
				case '9':
				case '10':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '7':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left bottom">&nbsp;</td>';
					$grid[2].='<td class="left">&nbsp;</td>';
					break;
				case '11':
					$grid[0].='<td class="left">&nbsp;</td>';
					$grid[1].='<td class="left">&nbsp;</td>';
					$grid[2].='<td class="">&nbsp;</td>';
					break;
				default:
					$grid[0].='<td class="">&nbsp;</td>';
					$grid[1].='<td class="">&nbsp;</td>';
					$grid[2].='<td class="">&nbsp;</td>';
			}
		}
//		if($n>=32) {
//			switch($n % 32) {
//				case '7':
//					$grid[0].='<td class="">&nbsp;</td>';
//					$grid[1].='<td class="">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '8':
//				case '9':
//				case '10':
//				case '11':
//				case '12':
//				case '13':
//				case '14':
//				case '16':
//				case '17':
//				case '18':
//				case '19':
//				case '20':
//				case '21':
//				case '22':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '15':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left bottom">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '23':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left">&nbsp;</td>';
//					$grid[2].='<td class="">&nbsp;</td>';
//					break;
//				default:
//					$grid[0].='<td class="">&nbsp;</td>';
//					$grid[1].='<td class="">&nbsp;</td>';
//					$grid[2].='<td class="">&nbsp;</td>';
//			}
//		}
//		if($n>=64) {
//			switch($n % 64) {
//				case '15':
//					$grid[0].='<td class="">&nbsp;</td>';
//					$grid[1].='<td class="">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '16':
//				case '17':
//				case '18':
//				case '19':
//				case '20':
//				case '21':
//				case '22':
//				case '23':
//				case '24':
//				case '25':
//				case '26':
//				case '27':
//				case '28':
//				case '29':
//				case '30':
//				case '32':
//				case '33':
//				case '34':
//				case '35':
//				case '36':
//				case '37':
//				case '38':
//				case '39':
//				case '40':
//				case '41':
//				case '42':
//				case '43':
//				case '44':
//				case '45':
//				case '46':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '31':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left bottom">&nbsp;</td>';
//					$grid[2].='<td class="left">&nbsp;</td>';
//					break;
//				case '47':
//					$grid[0].='<td class="left">&nbsp;</td>';
//					$grid[1].='<td class="left">&nbsp;</td>';
//					$grid[2].='<td class="">&nbsp;</td>';
//					break;
//				default:
//					$grid[0].='<td class="">&nbsp;</td>';
//					$grid[1].='<td class="">&nbsp;</td>';
//					$grid[2].='<td class="">&nbsp;</td>';
//			}
//		}
		return $grid;
	}

function genera_html_rot($TVsettings, $RULE) {
	global $Arr_Pages, $CFG, $RotMatches;

	$ret = array();
	$ST=array();
	$JS = 'timeStop[%1$s]='.intval($TVsettings->TVPTimeStop*1000/$TVsettings->TVPTimeScroll).";\n";
	$JS.= 'timeScroll[%1$s]='.$TVsettings->TVPTimeScroll.";\n";

	if(!$TVsettings->TVPDefault) {
		$ST['TV_Carattere']=$TVsettings->TVP_Carattere;
		$ST['TV_TR_BGColor']=$TVsettings->TVP_TR_BGColor;
		$ST['TV_TRNext_BGColor']=$TVsettings->TVP_TRNext_BGColor;
		$ST['TV_TR_Color']=$TVsettings->TVP_TR_Color;
		$ST['TV_TRNext_Color']=$TVsettings->TVP_TRNext_Color;
		$ST['TV_Content_BGColor']=$TVsettings->TVP_Content_BGColor;
		$ST['TV_Page_BGColor']=$TVsettings->TVP_Page_BGColor;
		$ST['TV_TH_BGColor']=$TVsettings->TVP_TH_BGColor;
		$ST['TV_TH_Color']=$TVsettings->TVP_TH_Color;
		$ST['TV_THTitle_BGColor']=$TVsettings->TVP_THTitle_BGColor;
		$ST['TV_THTitle_Color']=$TVsettings->TVP_THTitle_Color;
	}
	$TourId=$RULE->TVRTournament;

	// get the code of the tournament
	$q=safe_r_sql("select ToCode from Tournament where ToId=$TourId");
	$r=safe_fetch($q);
	$TourCode=$r->ToCode;

	switch($TVsettings->TVPPage) {
		case 'QUAL':
			include('Rot_qual.php');
			break;
		case 'QUALC':
			include('Rot_qual_cat.php');
			break;
		case 'QUALS':
			include('Rot_qual_snap.php');
			break;
		case 'QUALT';
			include('Rot_qual_t.php');
			break;
		case 'ABS':
			include('Rot_abs.php');
			break;
		case 'ABSS':
			include('Rot_abs_snap.php');
			break;
		case 'ABST':
			include('Rot_abs_t.php');
			break;
		case 'ELIM':
			include('Rot_elim.php');
			$RotMatches=true;
			break;
		case 'FIN':
			include('Rot_fin.php');
			$RotMatches=true;
			break;
		case 'FINT':
			include('Rot_fin_t.php');
			$RotMatches=true;
			break;
		case 'LIST':
			include('Rot_list.php');
			break;
		case 'ALFA':
			include('Rot_alfa.php');
			break;
		case 'LSPH':
			include('Rot_list_foto.php');
			break;
		case 'RAND':
			include('Rot_athl_sch.php');
			break;

		case 'BLABS':
			include('Rot_ElimBL.php');
			$RotMatches=true;
			break;

		case 'MEDL':
			include('Rot_MedalList.php');
			$RotMatches=true;
			break;

	}

	if(!defined('PNLG-ROTS')) {
		foreach($ret as $event=>$rows) {
			if(substr_count(strtolower($rows['fissi']),"<tr")>6) {
				$ret[$event]['basso'] = $ret[$event]['fissi'].$ret[$event]['basso'];
				$ret[$event]['fissi']='';
			}
		}
	}
	return $ret;
}


function preset_chains() {

}

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
