<?php
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/CommonLib.php');
/**
 * Esegue il passaggio di fase per gli individuali
 *
 * @param int $t: id torneo
 * @param string $e: filtro sull'evento se != null
 * @param int $p: filtro (nella join) sulla fase se != null
 * @param int $m: filtro (nella join) sul matchno se != null. Se è settato lui verrà ignorato $p
 *
 * @return: mixed
 * 		Se $e != null e $m != null significa che la query ritorna uno scontro secco quindi verranno ritornate le due righe.
 * 		Verranno inoltre ritornati nello stesso caso anche l'id dell'atleta da propagare e il risultato della propagazione (int 0 o 1)
 *
 * 		Ritorna false negli altri casi
 */
//TODO predisporre anche per più matchno in modo da usarla con htt
	function NextPhaseInd($t=null,$e=null,$p=null,$m=null)
	{
		$tour=(!is_null($t) ? $t : $_SESSION['TourId']);

		$eventFilter=(!is_null($e) ? "AND FinEvent=" . StrSafe_DB($e) . " " : "");

		$joinFilter="";
		if (!is_null($p))
			$joinFilter=" AND GrPhase=" . StrSafe_DB($p) . " ";

		if (!is_null($m))
		{
			$m1=-1;
			$m2=-1;

			if ($m%2==0)
			{
				$m1=$m;
				$m2=$m+1;
			}
			else
			{
				$m1=$m-1;
				$m2=$m;
			}
			$joinFilter="AND (GrMatchNo=" . $m1 . " OR GrMatchNo=" . ($m2) .  ") ";
		}


		$query="";

		$query
			= "SELECT CONCAT(EnFirstName,' ',SUBSTRING(EnName,1,1),'.') AS Atleta,"
			. "CoCode,CoName,"
			. "GrPhase,	/* Grids*/ "
			. "FinMatchNo AS MatchNo,FinEvent AS `Event`, FinAthlete AS Athlete, FinScore AS Score,FinTie AS Tie, "
			. "IF(GrPhase>2, FLOOR(FinMatchNo/2),FLOOR(FinMatchNo/2)-2) AS NextMatchNo "
			. "FROM Finals INNER JOIN Grids ON FinMatchNo=GrMatchNo " . $joinFilter . " "
			. "LEFT JOIN Entries ON FinAthlete=EnId "
			. "WHERE FinTournament=" . StrSafe_DB($tour) . " " . $eventFilter . " "
			. "ORDER BY FinEvent, NextMatchNo ASC, FinScore DESC, FinTie DESC ";

		$propagato=0;
		$athProp = 0;

		$rs=safe_r_sql($query);

		if (safe_num_rows($rs) && safe_num_rows($rs)%2==0)
		{
			while ($row0 = safe_fetch($rs))
			{
				$row1 = safe_fetch($rs);

				$athProp = 0;

				if ($row0->GrPhase>=2)
				{
					if (($row0->Score>0 || $row0->Tie>0) &&
						($row0->Score!=$row1->Score || $row0->Tie!=$row1->Tie))
					{
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete =" . StrSafe_DB($row0->Athlete) . ", ";
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row0->Event) . " AND FinMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($tour) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						//print '1 ' . $MyUpQuery . '<br>';
						$athProp=$row0->Athlete;
					}
					else
					{
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete ='0', ";
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row0->Event) . " AND FinMatchNo=" . StrSafe_DB($row0->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($tour) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						//print '2 ' . $MyUpQuery . '<br>';
					}
				}

				if ($row1->GrPhase==2)
				{
					if (($row1->Score>0 || $row1->Tie>0) &&
						($row0->Score!=$row1->Score || $row0->Tie!=$row1->Tie))
					{
					//print $row0->score.' - ' .$row1->score . ' + '. $row0->tie.' - ' .$row1->tie .'<br>';
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete =" . StrSafe_DB($row1->Athlete) . ", ";
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row1->Event) . " AND FinMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($tour) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						//print '3 ' . $MyUpQuery . '<br>';
						$athProp=$row1->Athlete;
					}
					else
					{
						$MyUpQuery = "UPDATE Finals SET ";
						$MyUpQuery.= "FinAthlete =" . StrSafe_DB($row1->Athlete) . ", "; // 0
						$MyUpQuery.= "FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " ";
						$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($row1->Event) . " AND FinMatchNo=" . StrSafe_DB(($row1->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($tour) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
					}
					//print '4 ' . $MyUpQuery . '<br>';
				}
			}

		// propagazione
			$propaga=true;
			if (!is_null($e) && !is_null($m) && $m<=7)
				$propaga=false;

			if ($propaga)
			{
				$oldId=($athProp!=0 ? StrSafe_DB($row1->Athlete) : StrSafe_DB($row0->Athlete) . ',' . StrSafe_DB($row1->Athlete));

				$Update
					= "UPDATE Finals SET "
					. "FinAthlete=" . StrSafe_DB($athProp) . " "
					. "WHERE FinAthlete IN (" . $oldId . ") AND FinTournament=" . StrSafe_DB($tour) . " AND FinEvent=" . StrSafe_DB($e) . " AND FinMatchNo<" . StrSafe_DB($row0->NextMatchNo) . " AND (FinScore<>0 OR FinTie<>0) ";
				$RsProp = safe_w_sql($Update);

				if (safe_w_affected_rows()>0)
				{
					$propagato=1;
				}
			}

		}

		if (!is_null($e) && !is_null($m))
			return array($row0,$row1,$athProp,$propagato);
		else
			return false;
	}

	function ExportLSTFinInd($Event=null)
	{
		$rank=Obj_RankFactory::create('FinalInd',array());
		$rank->read();
		$rankData=$rank->getData();
		$StrData="";
		// se ho degli eventi
		if(count($rankData['sections']))
		{
			foreach($rankData['sections'] as $section)
			{
				$ElimCols=0;
				if($section['meta']['elim1'])
					$ElimCols++;
				if($section['meta']['elim2'])
					$ElimCols++;

				$NumPhases=numPhases($section['meta']['firstPhase']);

				//Se Esistono righe caricate....
				if(count($section['items']))
				{

					$StrData.= "\n" . get_text($section['meta']['descr'],'','',true) . "\n";
					$StrData.= str_pad(substr($section['meta']['fields']['rank'],0,5),5,' ',STR_PAD_RIGHT) . " ";
					$StrData.= str_pad(substr($section['meta']['fields']['bib'],0,8),8,' ',STR_PAD_RIGHT) . " ";
					$StrData.= str_pad(substr($section['meta']['fields']['athlete'],0,30),30,' ',STR_PAD_RIGHT) . " ";
					$StrData.= str_pad(substr($section['meta']['fields']['countryName'],0,40),40,' ',STR_PAD_RIGHT) . "  ";
					$StrData.= str_pad(substr($section['meta']['fields']['qualRank'],0,10),10,' ',STR_PAD_LEFT) . " ";
					for($i=1; $i<=$ElimCols; $i++)
						$StrData.= str_pad(substr($section['meta']['fields']['elims']['e' . $i],0,16),16,' ',STR_PAD_LEFT) . " ";
					foreach($section['meta']['fields']['finals'] as $k=>$v)
					{
						if(is_numeric($k) && $k!=1)
							$StrData.= str_pad(substr($v,0,8),8,' ',STR_PAD_LEFT) . " ";
					}
					$StrData.= "\n";

					foreach($section['items'] as $item)
					{
						$StrData.= str_pad(($item['rank'] ? $item['rank'] : ' '),5,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['bib'],0,8),8,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['athlete'],0,30),30,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['countryCode'],0,10),10,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['countryName'],0,30),30,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr(number_format($item['qualScore'],0,get_text('NumberDecimalSeparator'), get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['qualRank'],-2,2),0,10),10,' ',STR_PAD_LEFT) . " ";
						//Gironi Eliminatori
						if(array_key_exists('e1',$item['elims']))
							$StrData .= str_pad(substr(number_format($item['elims']['e1']['score'],0,get_text('NumberDecimalSeparator'),get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['elims']['e1']['rank'],-2,2),0,16),16,' ',STR_PAD_LEFT)  . " ";
						if(array_key_exists('e2',$item['elims']))
							$StrData .= str_pad(substr(number_format($item['elims']['e2']['score'],0,get_text('NumberDecimalSeparator'),get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['elims']['e2']['rank'],-2,2),0,16),16,' ',STR_PAD_LEFT)  . " ";
						//Risultati  delle varie fasi
						foreach($item['finals'] as $k=>$v)
						{
							$tmp = '';
							if($v['tie']==2)
								$tmp = get_text('Bye');
							else
							{
								if($k==4 && $section['meta']['matchMode']!=0 && $item['rank']>=5)
									$tmp = "(" . $v['score'] .") " . $v['setScore'];
								else
								{
									$tmp = ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']);
									if(strlen($v['tiebreak'])>0 && $k<=1)
										$tmp .= " T.".str_replace('|',',',$v['tiebreak']);
									elseif($k<=1 && $v['tie']==1)
										$tmp .= "*";
								}
							}
							$StrData.= str_pad(substr($tmp,0,8),8,' ',STR_PAD_LEFT) . " ";
						}
						$StrData.= "\n";
					}
				}
			}
		}
		return $StrData;
	}

	function ExportLSTFinTeam($Event=null)
	{
		$rank=Obj_RankFactory::create('FinalTeam',array());
		$rank->read();
		$rankData=$rank->getData();


		/*echo "<pre>";
		print_r($rankData);
		echo "</pre>";exit;*/
		$StrData = "";

		if(count($rankData['sections']))
		{
			foreach($rankData['sections'] as $section)
			{
				$NumPhases=numPhases($section['meta']['firstPhase']);
				$NumComponenti=($section['meta']['maxTeamPerson'] ? $section['meta']['maxTeamPerson'] : 3);
				if(count($section['items']))
				{

					$StrData.= "\n" . get_text($section['meta']['descr'],'','',true) . "\n";
							// Header vero e proprio
					$StrData.= str_pad(substr($section['meta']['fields']['rank'],0,5),5,' ',STR_PAD_RIGHT) . " ";
					$StrData.= str_pad(substr($section['meta']['fields']['countryName'],0,40),40,' ',STR_PAD_RIGHT) . "  ";
					$StrData.= str_pad(substr($section['meta']['fields']['qualRank'],0,10),10,' ',STR_PAD_LEFT) . " ";
					foreach($section['meta']['fields']['finals'] as $k=>$v)
					{
						if(is_numeric($k) && $k!=1)
							$StrData.= str_pad(substr($v,0,8),8,' ',STR_PAD_LEFT) . " ";
					}

					$StrData.= "\n";



					foreach($section['items'] as $item)
					{
						$StrData.= str_pad(($item['rank'] ? $item['rank'] : ' '),5,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['countryCode'],0,10),10,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr($item['countryName'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')'),0,30),30,' ',STR_PAD_RIGHT) . " ";
						$StrData.= str_pad(substr(number_format($item['qualScore'],0,get_text('NumberDecimalSeparator'), get_text('NumberThousandsSeparator')) . '-' . substr('00' . $item['qualRank'],-2,2),0,10),10,' ',STR_PAD_LEFT) . " ";
						//Risultati  delle varie fasi
						foreach($item['finals'] as $k=>$v)
						{
							$tmp = '';
							if($v['tie']==2)
								$tmp = get_text('Bye');
							else
							{
								if($k==4 && $section['meta']['matchMode']!=0 && $item['rank']>=5)
									$tmp = $v['setScore'] . " (" . $v['score'] .")";
								else
								{
									$tmp = ($section['meta']['matchMode']==0 ? $v['score'] : $v['setScore']);
									if(strlen($v['tiebreak'])>0 && $k<=1)
									{
										$tmpTxt="";
										$tmpArr=explode("|",$v['tiebreak']);
										for($countArr=0; $countArr<count($tmpArr); $countArr+=$NumComponenti)
											$tmpTxt .= array_sum(array_slice($tmpArr,$countArr,$NumComponenti)). ",";
										$tmp .=  " T." . substr($tmpTxt,0,-1);
									}
									elseif($k<=1 && $v['tie']==1)
										$tmp .= "*";
								}
							}
							$StrData.= str_pad(substr($tmp,0,8),8,' ',STR_PAD_LEFT) . " ";
						}
						$StrData.= "\n";
					}
				}
			}
		}
		return $StrData;
	}

	function GetFinMatches($Event, $Phase=null, $MatchNo=null, $TeamEvent=0, $OnlyNames=true, $TourId='') {
		if(!$TourId) $TourId=$_SESSION['TourId'];
		$rs=false;

	// fase e matchno nulli non va bene;  $Phase ha la precedenza su $MatchNo
		if (is_null($Phase) && is_null($MatchNo) )
			return false;

		$query="";

		if ($TeamEvent==0)		//individuali
		{
			$query= "
				SELECT
					IF(e1.EnFirstName IS NOT NULL, CONCAT(e1.EnFirstName,' ',e1.EnName, ' (',c1.CoCode,')'),'***') AS name1,
					IF(e2.EnFirstName IS NOT NULL, CONCAT(e2.EnFirstName,' ',e2.EnName, ' (',c2.CoCode,')'),'***') AS name2,
					f1.FinMatchNo AS match1,
					f2.FinMatchNo AS match2,
					f1.FinEvent AS event,
					f1.FinShootFirst as first1,
					f2.FinShootFirst as first2,
					f1.FinStatus AS status1,
					f2.FinStatus AS status2,
					f1.FinWinLose AS win1,
					f2.FinWinLose AS win2,
					f1.FinArrowPosition as pos1,
					f2.FinArrowPosition as pos2,
					f1.FinTiePosition as tiepos1,
					f2.FinTiePosition as tiepos2,
					fs1.FSTarget AS target1,
					fs2.FSTarget AS target2,
					fs1.FSScheduledDate	AS FsDate1,
					fs2.FSScheduledDate AS FsDate2,
					fs1.FSScheduledTime AS FsTime1,
					fs2.FSScheduledTime AS FsTime2,
					g1.GrPhase AS phase,
					f1.FinLive AS live,
					ev1.EvTeamEvent AS teamEvent,
					ev1.EvMixedTeam AS mixedTeam,
					ev1.EvElimType AS elimType,
					IF(f1.FinDateTime>=f2.FinDateTime,f1.FinDateTime,f2.FinDateTime) AS LastUpdate
			";

				if ($OnlyNames==false)
				{
					$query.="
						,ev1.EvMatchMode AS matchMode,ev1.EvMatchArrowsNo AS matchArrowsNo,
						f1.FinScore AS score1,
						f1.FinSetScore AS setScore1,
						f1.FinSetPoints AS setPoints1,
						f1.FinSetPointsByEnd AS setPointsByEnd1,
						f1.FinTie AS tie1,
						f1.FinArrowstring AS arrowString1,
						f1.FinTiebreak AS tiebreak1,
						f1.FinTbClosest AS closest1,
						f2.FinScore AS score2,
						f2.FinSetScore AS setScore2,
						f2.FinSetPoints AS setPoints2,
						f2.FinSetPointsByEnd AS setPointsByEnd2,
						f2.FinTie AS tie2,
						f2.FinArrowstring AS arrowString2,
						f2.FinTiebreak AS tiebreak2,
						f2.FinTbClosest AS closest2
					";
				}

			$query.= "
				FROM
					Finals AS f1
					INNER JOIN
						Finals AS f2
					ON f1.FinEvent=f2.FinEvent AND f1.FinMatchNo=IF((f1.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f1.FinTournament=f2.FinTournament

					INNER JOIN
						Events AS ev1
					ON f1.FinEvent=ev1.EvCode AND ev1.EvTeamEvent=0 AND ev1.EvTournament=" . StrSafe_DB($TourId) . "

					INNER JOIN
						Grids AS g1
					ON f1.FinMatchNo=g1.GrMatchNo

					LEFT JOIN
						Entries AS e1
					ON f1.FinAthlete=e1.EnId

					LEFT JOIN
						Countries AS c1
					ON e1.EnCountry=c1.CoId

					LEFT JOIN
						Entries AS e2
					ON f2.FinAthlete=e2.EnId

					LEFT JOIN
						Countries AS c2
					ON e2.EnCountry=c2.CoId

					LEFT JOIN
						FinSchedule AS fs1
					ON f1.FinMatchNo=fs1.FSMatchNo AND f1.FinEvent=fs1.FSEvent AND fs1.FSTeamEvent=0 AND f1.FinTournament=fs1.FSTournament

					LEFT JOIN
						FinSchedule AS fs2
					ON f2.FinMatchNo=fs2.FSMatchNo AND f2.FinEvent=fs2.FSEvent AND fs2.FSTeamEvent=0 AND f2.FinTournament=fs2.FSTournament

				WHERE
					f1.FinTournament=" . StrSafe_DB($TourId) . " AND (f1.FinMatchNo % 2)=0
					AND (e1.EnFirstName IS NOT NULL OR e2.EnFirstName IS NOT NULL)
					AND ev1.EvCode=" . StrSafe_DB($Event) . " ";

					if ($Phase==-1)
						$query.= " ";
					elseif (!is_null($Phase))
						$query.= "AND g1.GrPhase=" . StrSafe_DB($Phase) . " ";
					elseif (!is_null($MatchNo))
						$query.= "AND f1.FinMatchNo=" . StrSafe_DB($MatchNo) . "
			";

			$query.="
				ORDER BY EvProgr".($Phase==-1 ? ', GrPhase desc' : '').", GrMatchNo
			";
		}
		else		// team
		{
			$query= "
				SELECT
					IF(c1.CoName IS NOT NULL, CONCAT(c1.CoName,' (',c1.CoCode, IF(tf1.TfSubTeam!='0',CONCAT(' - ',tf1.TfSubTeam),''),')'),'***') AS name1,
					IF(c2.CoName IS NOT NULL, CONCAT(c2.CoName,' (',c2.CoCode, IF(tf2.TfSubTeam!='0',CONCAT(' - ',tf2.TfSubTeam),''),')'),'***') AS name2,
					tf1.TfMatchNo AS match1,
					tf2.TfMatchNo AS match2,
					tf1.TfEvent AS event,
					tf1.TfShootFirst as first1,
					tf2.TfShootFirst as first2,
					tf1.TfStatus AS status1,
					tf2.TfStatus AS status2,
					tf1.TfWinLose AS win1,
					tf2.TfWinLose AS win2,
					fs1.FSTarget AS target1,
					fs2.FSTarget AS target2,
					fs1.FSScheduledDate	AS FsDate1,
					fs2.FSScheduledDate AS FsDate2,
					fs1.FSScheduledTime AS FsTime1,
					fs2.FSScheduledTime AS FsTime2,
					ev1.EvEventName AS eventName,
					g1.GrPhase AS phase,
					tf1.TfLive AS live,
					ev1.EvTeamEvent AS teamEvent,
					ev1.EvMixedTeam AS mixedTeam,
					IF(tf1.TfDateTime>=tf2.TfDateTime,tf1.TfDateTime,tf2.TfDateTime) AS LastUpdate
			";

				if ($OnlyNames==false)
				{
					$query.="
						,ev1.EvMatchMode AS matchMode,ev1.EvMatchArrowsNo AS matchArrowsNo,
						tf1.TfScore AS score1,tf1.TfSetScore AS setScore1,tf1.TfSetPoints AS setPoints1,tf1.TfTie AS tie1,tf1.TfArrowstring AS arrowString1,tf1.TfTiebreak AS tiebreak1,tf1.TfTbClosest AS closest1,
						tf2.TfScore AS score2,tf2.TfSetScore AS setScore2,tf2.TfSetPoints AS setPoints2,tf2.TfTie AS tie2,tf2.TfArrowstring AS arrowString2,tf2.TfTiebreak AS tiebreak2,tf2.TfTbClosest AS closest2
					";
				}

			$query.= "
				FROM
					TeamFinals AS tf1
					INNER JOIN
						TeamFinals AS tf2
					ON tf1.TfEvent=tf2.TfEvent AND tf1.TfMatchNo=IF((tf1.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf1.TfTournament=tf2.TfTournament

					INNER JOIN
						Events AS ev1
					ON tf1.TfEvent=ev1.EvCode AND ev1.EvTeamEvent=1 AND ev1.EvTournament=" . StrSafe_DB($TourId) . "

					INNER JOIN
						Grids AS g1
					ON tf1.TfMatchNo=g1.GrMatchNo

					LEFT JOIN
						Countries AS c1
					ON tf1.TfTeam=c1.CoId

					LEFT JOIN
						Countries AS c2
					ON tf2.TfTeam=c2.CoId

					LEFT JOIN
						FinSchedule AS fs1
					ON tf1.TfMatchNo=fs1.FSMatchNo AND tf1.TfEvent=fs1.FSEvent AND fs1.FSTeamEvent=1 AND tf1.TfTournament=fs1.FSTournament

					LEFT JOIN
						FinSchedule AS fs2
					ON tf2.TfMatchNo=fs2.FSMatchNo AND tf2.TfEvent=fs2.FSEvent AND fs2.FSTeamEvent=1 AND tf2.TfTournament=fs2.FSTournament

				WHERE
					tf1.TfTournament=" . StrSafe_DB($TourId) . " AND (tf1.TfMatchNo % 2)=0
					AND (c1.CoName IS NOT NULL OR c2.CoName IS NOT NULL)
					AND ev1.EvCode=" . StrSafe_DB($Event) . " ";

					if (!is_null($Phase))
						$query.= "AND g1.GrPhase=" . StrSafe_DB($Phase) . " ";
					elseif (!is_null($MatchNo))
						$query.= "AND tf1.TfMatchNo=" . StrSafe_DB($MatchNo) . "
			";

			$query.="
				ORDER BY EvProgr ASC,GrMatchNo ASC
			";
		}

		//print $query;exit;
		$rs=safe_r_sql($query);

		return $rs;
	}

	function GetFinMatches_sql($OtherWhere="",$TeamEvent=0,$OrderBy="EvProgr ASC,GrMatchNo ASC",$OnlyNames=true)
	{
		$rs=false;

		$query="";

		if ($TeamEvent==0)		//individuali
		{
			$query= "
				SELECT DISTINCTROW
					e1.EnFirstName familyName1,
					e2.EnFirstName familyName2,
					i1.IndRank rank1,
					i2.IndRank rank2,
					IF(e1.EnFirstName IS NOT NULL, if(e1.EnNameOrder, CONCAT(upper(e1.EnFirstName),' ',e1.EnName), CONCAT(e1.EnName,' ',upper(e1.EnFirstName))),'') AS name1,
					IF(e2.EnFirstName IS NOT NULL, if(e2.EnNameOrder, CONCAT(upper(e2.EnFirstName),' ',e2.EnName), CONCAT(e2.EnName,' ',upper(e2.EnFirstName))),'') AS name2,
					IF(c1.CoCode IS NOT NULL, c1.CoCode,'') AS countryCode1,
					IF(c1.CoName IS NOT NULL, c1.CoName,'') AS countryName1,
					IF(c2.CoCode IS NOT NULL, c2.CoCode,'') AS countryCode2,
					IF(c2.CoName IS NOT NULL, c2.CoName,'') AS countryName2,
					f1.FinMatchNo AS match1,
					f2.FinMatchNo AS match2,
					fs1.FSTarget AS target1,
					fs2.FSTarget AS target2,
					f1.FinEvent AS event,
					f1.FinWinLose AS win1,
					f2.FinWinLose AS win2,
					ev1.EvEventName AS eventName,
					ev1.EvFinalFirstPhase as firstPhase,
					g1.GrPhase AS phase,
					ev1.EvTeamEvent AS teamEvent,
					ev1.EvMatchMode AS matchMode,
					ev1.EvMatchArrowsNo AS matchArrowsNo,
					UNIX_TIMESTAMP(IF(f1.FinDateTime>=f2.FinDateTime,f1.FinDateTime,f2.FinDateTime)) AS lastUpdate
			";

				if ($OnlyNames==false) {
					$query.="
						, f1.FinScore AS score1,f1.FinSetScore AS setScore1,f1.FinSetPoints AS setPoints1,f1.FinTie AS tie1,f1.FinArrowstring AS arrowString1,f1.FinTiebreak AS tiebreak1,f1.FinTbClosest AS tieclosest1,
						f2.FinScore AS score2,f2.FinSetScore AS setScore2,f2.FinSetPoints AS setPoints2,f2.FinTie AS tie2,f2.FinArrowstring AS arrowString2,f2.FinTiebreak AS tiebreak2,f2.FinTbClosest AS tieclosest2,
						f1.FinTie AS tie1,f2.FinTie AS tie2
					";
				}

			$query.= "
				FROM
					Finals AS f1
					INNER JOIN
						Finals AS f2
					ON f1.FinEvent=f2.FinEvent AND f1.FinMatchNo=IF((f1.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f1.FinTournament=f2.FinTournament

					INNER JOIN
						Events AS ev1
					ON f1.FinEvent=ev1.EvCode AND ev1.EvTeamEvent=0 AND ev1.EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "

					INNER JOIN
						Grids AS g1
					ON f1.FinMatchNo=g1.GrMatchNo

					LEFT JOIN
						Entries AS e1
					ON f1.FinAthlete=e1.EnId
					
					LEFT JOIN
					    Individuals As i1
					ON f1.FinEvent=i1.IndEvent AND f1.FinTournament=i1.IndTournament AND f1.FinAthlete=i1.IndId

					LEFT JOIN
						Countries AS c1
					ON e1.EnCountry=c1.CoId

					LEFT JOIN
						Entries AS e2
					ON f2.FinAthlete=e2.EnId
					
					LEFT JOIN
					    Individuals As i2
					ON f2.FinEvent=i2.IndEvent AND f2.FinTournament=i2.IndTournament AND f2.FinAthlete=i2.IndId

					LEFT JOIN
						Countries AS c2
					ON e2.EnCountry=c2.CoId

					LEFT JOIN
						FinSchedule AS fs1
					ON f1.FinMatchNo=fs1.FSMatchNo AND f1.FinEvent=fs1.FSEvent AND fs1.FSTeamEvent=0 AND f1.FinTournament=fs1.FSTournament

					LEFT JOIN
						FinSchedule AS fs2
					ON f2.FinMatchNo=fs2.FSMatchNo AND f2.FinEvent=fs2.FSEvent AND fs2.FSTeamEvent=0 AND f2.FinTournament=fs2.FSTournament

				WHERE
					(f1.FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (f1.FinMatchNo % 2)=0
					AND (e1.EnFirstName IS NOT NULL OR e2.EnFirstName IS NOT NULL)) "
					. $OtherWhere . "
				";

			/*$query.="
				ORDER BY EvProgr ASC,GrMatchNo ASC
			";*/
			if ($OrderBy!="")
			{
				$query.="ORDER BY " . $OrderBy . " ";
			}
		}
		else		// team
		{
			$query= "
				SELECT DISTINCTROW
					c1.CoCode familyName1,
					c2.CoCode familyName2,
					t1.TeRank rank1,
					t2.TeRank rank2,
					IF(c1.CoName IS NOT NULL,CONCAT(c1.CoName, IF(tf1.TfSubTeam!='0',CONCAT(' - ',tf1.TfSubTeam),''),''),'') AS name1,
					IF(c1.CoCode IS NOT NULL,c1.CoCode,'') AS countryCode1,
					IF(c1.CoName IS NOT NULL,Name1,'') AS countryName1,

					IF(c2.CoName IS NOT NULL,CONCAT(c2.CoName, IF(tf2.TfSubTeam!='0',CONCAT(' - ',tf2.TfSubTeam),''),''),'') AS name2,
					IF(c2.CoCode IS NOT NULL,c2.CoCode,'') AS countryCode2,
					IF(c2.CoName IS NOT NULL,Name2,'') AS countryName2,

					tf1.TfMatchNo AS match1,
					tf2.TfMatchNo AS match2,
					fs1.FSTarget AS target1,
					fs2.FSTarget AS target2,
					ev1.EvEventName AS eventName,
					ev1.EvFinalFirstPhase as firstPhase,
					tf1.TfEvent AS event,
					tf1.TfWinLose AS win1,
					tf2.TfWinLose AS win2,
					g1.GrPhase AS phase,
					ev1.EvTeamEvent AS teamEvent,
					ev1.EvMatchMode AS matchMode,
					ev1.EvMatchArrowsNo AS matchArrowsNo,
					UNIX_TIMESTAMP(IF(tf1.TfDateTime>=tf2.TfDateTime,tf1.TfDateTime,tf2.TfDateTime)) AS lastUpdate
			";

				if ($OnlyNames==false)
				{
					$query.="
						,tf1.TfScore AS score1,tf1.TfSetScore AS setScore1,tf1.TfSetPoints AS setPoints1,tf1.TfTie AS tie1,tf1.TfArrowstring AS arrowString1,tf1.TfTiebreak AS tiebreak1,tf1.TfTbClosest AS tieclosest1,
						tf2.TfScore AS score2,tf2.TfSetScore AS setScore2,tf2.TfSetPoints AS setPoints2,tf2.TfTie AS tie2,tf2.TfArrowstring AS arrowString2,tf2.TfTiebreak AS tiebreak2,tf2.TfTbClosest AS tieclosest2,
						tf1.TfTie AS tie1,tf2.TfTie AS tie2
					";
				}

			$query.= "
				FROM
					TeamFinals AS tf1
					INNER JOIN
						TeamFinals AS tf2
					ON tf1.TfEvent=tf2.TfEvent AND tf1.TfMatchNo=IF((tf1.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf1.TfTournament=tf2.TfTournament

                    LEFT JOIN
					    Teams As t1
					ON tf1.TfEvent=t1.TeEvent AND tf1.TfTournament=t1.TeTournament AND tf1.TfTeam=t1.TeCoId AND tf1.TfSubTeam=t1.TeSubTeam AND t1.TeFinEvent=1

                    LEFT JOIN
					    Teams As t2
					ON tf2.TfEvent=t2.TeEvent AND tf2.TfTournament=t2.TeTournament AND tf2.TfTeam=t2.TeCoId AND tf2.TfSubTeam=t2.TeSubTeam AND t2.TeFinEvent=1


					INNER JOIN
						Events AS ev1
					ON tf1.TfEvent=ev1.EvCode AND ev1.EvTeamEvent=1 AND ev1.EvTournament=" . StrSafe_DB($_SESSION['TourId']) . "

					INNER JOIN
						Grids AS g1
					ON tf1.TfMatchNo=g1.GrMatchNo

					LEFT JOIN
						Countries AS c1
					ON tf1.TfTeam=c1.CoId

					LEFT JOIN
						Countries AS c2
					ON tf2.TfTeam=c2.CoId

					LEFT JOIN
						(SELECT TfcCoId, TfcSubTeam, TfcEvent, GROUP_CONCAT(if(EnNameOrder, CONCAT(upper(EnFirstName),' ',EnName), CONCAT(EnName,' ',upper(EnFirstName))) order by EnFirstName SEPARATOR ', ') as Name1
						FROM TeamFinComponent
						INNER JOIN Entries ON TfcId=EnId
						WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . "
						GROUP BY TfcCoId, TfcSubTeam, TfcEvent) as tfc1
					ON tf1.TfTeam=tfc1.TfcCoId AND tf1.TfSubTeam=tfc1.TfcSubTeam AND tf1.TfEvent=tfc1.TfcEvent

					LEFT JOIN
						(SELECT TfcCoId, TfcSubTeam, TfcEvent, GROUP_CONCAT(if(EnNameOrder, CONCAT(upper(EnFirstName),' ',EnName), CONCAT(EnName,' ',upper(EnFirstName))) order by EnFirstName SEPARATOR ', ') as Name2
						FROM TeamFinComponent
						INNER JOIN Entries ON TfcId=EnId
						WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . "
						GROUP BY TfcCoId, TfcSubTeam, TfcEvent) as tfc2
					ON tf2.TfTeam=tfc2.TfcCoId AND tf2.TfSubTeam=tfc2.TfcSubTeam AND tf2.TfEvent=tfc2.TfcEvent

					LEFT JOIN
						FinSchedule AS fs1
					ON tf1.TfMatchNo=fs1.FSMatchNo AND tf1.TfEvent=fs1.FSEvent AND fs1.FSTeamEvent=1 AND tf1.TfTournament=fs1.FSTournament

					LEFT JOIN
						FinSchedule AS fs2
					ON tf2.TfMatchNo=fs2.FSMatchNo AND tf2.TfEvent=fs2.FSEvent AND fs2.FSTeamEvent=1 AND tf2.TfTournament=fs2.FSTournament

				WHERE
					(tf1.TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND (tf1.TfMatchNo % 2)=0
					AND (c1.CoName IS NOT NULL OR c2.CoName IS NOT NULL)) "
					. $OtherWhere . "
			";

			/*$query.="
				ORDER BY EvProgr ASC,GrMatchNo ASC
			";*/
			if ($OrderBy!="")
			{
				$query.="ORDER BY " . $OrderBy . " ";
			}
		}
		//print $query;
		$rs=safe_r_sql($query);

		return $rs;
	}

	function CalcScoreRowsColsSO($myRow, $TourId=0)
	{
		$team=$myRow->teamEvent;

		$obj=getEventArrowsParams($myRow->event,$myRow->phase,$team, $TourId);

		$rows=$obj->ends;
		$cols=$obj->arrows;
		$so=$obj->so;

		return array($rows,$cols,$so);
	}

/**
 * Toggle/Sets the live session
 * @param int $TeamEvent 0 if individual, 1 if team event
 * @param char $event the code of the event
 * @param int $match the "even" MatchNo
 * @param int $TourId (optional) the TourId
 * @param bool $Toggle (optional) true toggles the "Live" flag, false always sets it
 * @return The recordset containing the "live" status of the selected match
 */
function setLiveSession($TeamEvent, $event, $match, $TourId=0, $Toggle=true) {
	$prefix = ($TeamEvent ? 'Tf' : 'Fin');
	if(!$TourId) $TourId=$_SESSION['TourId'];
	$Where="{$prefix}Tournament=$TourId
		AND {$prefix}MatchNo IN($match, " . ($match+1) . ")
		AND {$prefix}Event=" . StrSafe_DB($event);
	if($match!=-1) {
		$MatchFilter=" ";
	}

	$sql="select Sch.*, cast(if(EvWinnerFinalRank>1, EvWinnerFinalRank*100 + GrPhase, 1+(1/(1+GrPhase))) as decimal(15,4)) as OrderBy, {$prefix}Live Live from " . ($TeamEvent ? 'Team' : '') . "Finals Fin
		left join (select * from FinSchedule
			inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
			inner join Grids on FsMatchNo=GrMatchNo
			where FsTournament=$TourId and FsEvent='$event' and FsTeamEvent=".($TeamEvent ? 1 : 0)." and FsMatchNo = $match and FSScheduledDate>0 and FSScheduledTime>0
			) Sch on true
		WHERE $Where";
	$q=safe_r_sql($sql);
	if($r=safe_fetch($q)) {
		unsetLiveSession($TourId);

		if(!$r->Live) {
			$sql = "UPDATE " . ($TeamEvent ? 'Team' : '') . "Finals SET
				{$prefix}Live = 1,
				{$prefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
				WHERE $Where";
			safe_w_sql($sql);
		} elseif(!$Toggle) {
			$sql = "UPDATE " . ($TeamEvent ? 'Team' : '') . "Finals SET
				{$prefix}Live = 1,
				{$prefix}DateTime={$prefix}DateTime
				WHERE $Where";
			safe_w_sql($sql);
		}

		// Set/unset active session in scheduler
		if($r->FSScheduledDate) {
			$ActiveSessions=array();
			if(!$r->Live or !$Toggle) {
				// works reverse as it is the previous state!
				$key=$r->FSScheduledDate
					.'|'.substr($r->FSScheduledTime,0,5)
					.'|'.$r->GrPhase
					.'|'.$r->EvFinalFirstPhase
					.'|'.round($r->OrderBy, 4);
				$ActiveSessions=array($key);
			}
			Set_Tournament_Option('ActiveSession', $ActiveSessions, false, $TourId);
		}
	}

	$sql = "SELECT {$prefix}Live as Live
		FROM " . ($TeamEvent ? 'Team' : '') . "Finals
		WHERE {$prefix}Tournament=$TourId
		AND {$prefix}MatchNo =" . StrSafe_DB($match) . "
		AND {$prefix}Event=" . StrSafe_DB($event);

	return safe_r_sql($sql);
}

function unsetLiveSession($TourId) {
	safe_w_sql("UPDATE Finals SET FinLive='0',
		FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
		WHERE FinTournament=$TourId
		AND FinLive!='0'");
	safe_w_sql("UPDATE TeamFinals SET TfLive='0',
		TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
		WHERE TfTournament=$TourId
		AND TfLive!='0'");
}
