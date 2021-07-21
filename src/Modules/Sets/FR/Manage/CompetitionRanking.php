<?php

/*
 * This page will request if the rank has to be made on
 * - the total number of 10+X,
 * - the arrow average of the team
 * - the average number of 10+X of the team throughout the whole competition
 *
 *
 * */

require_once(dirname(__FILE__) . '/config.php');

if(!empty($_REQUEST['type'])) {
	require_once('Common/pdf/IanseoPdf.php');
	require_once('Common/Lib/ArrTargets.inc.php');

	$Results=array();
	$OrderBy=array();
	$Events=array();
	$Teams=array();
	$q=safe_r_sql("select ToGoldsChars, ToXNineChars from Tournament where ToId={$_SESSION['TourId']}");
	$r=safe_fetch($q);
	$Golds=$r->ToGoldsChars;
	$XNine=$r->ToXNineChars;

	$SQL=array();
	$SQL[]="select concat(trim(QuD1Arrowstring),trim(QuD2Arrowstring)) as ArrowString, EnCountry as Country, EcCode as EventCode, EvEventName, CoName
		from Qualifications
	    inner join Entries on EnId=QuId and EnTeamFEvent=1
	    inner join Countries on CoId=EnCountry and CoTournament=EnTournament
	    inner join EventClass on EcDivision=EnDivision and EcClass=EnClass and EcTournament=EnTournament and EcTeamEvent=1
	    inner join Events on EvCode=EcCode and EvTeamEvent=1 and EvTournament=EnTournament
	    where EnTournament={$_SESSION['TourId']}";
	$SQL[]="select trim(FinArrowstring) as ArrowString, EnCountry as Country, left(FinEvent, 3) as EventCode, '' as EvEventName, '' as CoName from Finals inner join Entries on EnId=FinAthlete and EnTournament=FinTournament where EnTournament={$_SESSION['TourId']} and FinArrowstring!=''";
	$SQL[]="select trim(TfArrowstring) as ArrowString, TfTeam as Country, TfEvent as EventCode, '' as EvEventName, '' as CoName from TeamFinals where TfTournament={$_SESSION['TourId']} and TfArrowstring!=''";
	$q=safe_r_sql(implode(' UNION ', $SQL));
	switch($_REQUEST['type']) {
		case 'Rank10':
		case 'Rank10Average':
			$Fld1='Golds';
			$Fld2='XNine';
			$Head1=get_text('TVCss3Gold', 'Tournament');
			$Head2=get_text('TVCss3XNine','Tournament');
			while($r=safe_fetch($q)) {
				$Points=ValutaArrowStringGX($r->ArrowString, $Golds, $XNine);
				if(empty($Events[$r->EventCode])) {
					$Events[$r->EventCode]=$r->EvEventName;
				}
				if(empty($Teams[$r->Country])) {
					$Teams[$r->Country]=$r->CoName;
				}
				if(empty($Results[$r->EventCode][$r->Country])) {
					$Results[$r->EventCode][$r->Country]=array(
						'Golds' => 0,
						'XNine' => 0,
					);
				}
				$Results[$r->EventCode][$r->Country]['Golds']+=$Points[1];
				$Results[$r->EventCode][$r->Country]['XNine']+=$Points[2];
			}
			if($_REQUEST['type']=='Rank10Average') {
				$q=safe_r_sql("select count(*) as Total, EnCountry as Country, EcCode as EventCode 
					from Entries 
				    inner join EventClass on EcDivision=EnDivision and EcClass=EnClass and EcTournament=EnTournament and EcTeamEvent=1 
					where EnTournament={$_SESSION['TourId']} and EnTeamFEvent=1
					group by EnCountry, EcCode");
				while($r=safe_fetch($q)) {
					$Results[$r->EventCode][$r->Country]['Golds']=number_format($Results[$r->EventCode][$r->Country]['Golds']/$r->Total, 3);
					$Results[$r->EventCode][$r->Country]['XNine']=number_format($Results[$r->EventCode][$r->Country]['XNine']/$r->Total, 3);
				}
			}
			foreach($Results as $Event => $Countries) {
				foreach($Countries as $Country => $Values) {
					$OrderBy[$Event][$Country] = $Values['Golds'] + $Values['XNine']/1000;
				}

				arsort($OrderBy[$Event],SORT_NUMERIC);
			}
			break;
		case 'RankAverage':
			$Fld1='Average';
			$Fld2='Golds';
			$Head1=get_text('ArrowAverage');
			$Head2=get_text('TVCss3Gold','Tournament');
			while($r=safe_fetch($q)) {
				$Points=ValutaArrowStringGX($r->ArrowString);
				if(empty($Events[$r->EventCode])) {
					$Events[$r->EventCode]=$r->EvEventName;
				}
				if(empty($Teams[$r->Country])) {
					$Teams[$r->Country]=$r->CoName;
				}
				if(empty($Results[$r->EventCode][$r->Country])) {
					$Results[$r->EventCode][$r->Country]=array(
						'Average' => 0,
						'Points' => 0,
						'Golds' => 0,
						'XNine' => 0,
						'Hits' => 0,
					);
				}
				$Results[$r->EventCode][$r->Country]['Points']+=$Points[0];
				$Results[$r->EventCode][$r->Country]['Golds']+=$Points[1];
				$Results[$r->EventCode][$r->Country]['XNine']+=$Points[2];
				$Results[$r->EventCode][$r->Country]['Hits']+=strlen(trim($r->ArrowString));
			}
			foreach($Results as $Event => $Countries) {
				foreach($Countries as $Country => &$Values) {
					$OrderBy[$Event][$Country] = $Values['Points']/$Values['Hits'] + $Values['Golds']/1000 + $Values['XNine']/1000000;
					$Results[$Event][$Country]['Average']=number_format($Values['Points']/$Values['Hits'], 3);
					$Results[$Event][$Country]['Golds']=number_format(100*$Values['Golds']/$Values['Hits'], 1).'%';
				}

				arsort($OrderBy[$Event],SORT_NUMERIC);
			}
			break;
	}


	$pdf=new IanseoPdf('');
	$pdf->startPageGroup();
	$pdf->AddPage();

	$pdf->ln(4);
	$pdf->SetFont('', 'B', 12);
	$pdf->Cell(0,6, get_text($_REQUEST['type'], 'Tournament'), '1', 1,'C', '1');
	$pdf->SetFont('', '', 8);

	$LeftMargin=110;
	foreach($OrderBy as $Event => $Countries) {
		if($LeftMargin==110) {
			$LeftMargin=10;
			$Y=$pdf->GetY();
		} else {
			$LeftMargin=110;
		}
		$pdf->SetLeftMargin($LeftMargin);
		$pdf->SetY($Y);
		$pdf->ln(4);
		$pdf->Cell(90,5, $Events[$Event], '1', 1,'C', '1');
		$pdf->Cell(10,5, get_text('Rank'), '1', 0,'C', '1');
		$pdf->Cell(10,5, $Head1, '1', 0,'C', '1');
		$pdf->Cell(10,5, $Head2, '1', 0,'C', '1');
		$pdf->Cell(60,5, get_text('Team'), '1', 1,'L', '1');
		$rnk=1;
		foreach($Countries as $Country => $dummy) {
			$pdf->Cell(10,4, $rnk, '','','R');
			$pdf->Cell(10,4, $Results[$Event][$Country][$Fld1], '','','R');
			$pdf->Cell(10,4, $Results[$Event][$Country][$Fld2], '','','R');
			$pdf->Cell(60,4, $Teams[$Country]);
			$pdf->ln();
			$rnk++;
		}
	}

	$pdf->Output();
	exit;
}

include('Common/Templates/head.php');

echo '<div align="center" style="margin-top:2em;">
	<div class="Button" onclick="location.href=\'?type=Rank10\'">'.get_text('Rank10', 'Tournament').'</div>
	<div class="Button" onclick="location.href=\'?type=Rank10Average\'">'.get_text('Rank10Average', 'Tournament').'</div>
	<div class="Button" onclick="location.href=\'?type=RankAverage\'">'.get_text('RankAverage', 'Tournament').'</div>
	</div>';

include('Common/Templates/tail.php');

