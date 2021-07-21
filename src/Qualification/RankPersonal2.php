<?php
	define('debug',false);	// settare a true per l'output di debug

	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclQualification, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');


	$Id=(isset($_REQUEST['Id']) ? $_REQUEST['Id'] : null);

	$Out='';

	if (is_null($Id))
		exit;

/*
 * Devo cercare la posione di rank del tizio
 */
	$Query
		= "SELECT "
			. "QuClRank,EnDivision,EnClass "
		. "FROM "
			. "Qualifications INNER JOIN Entries ON QuId=EnId "
		. "WHERE "
			. "QuId=" . StrSafe_DB($Id) . " ";
	$Rs=safe_r_sql($Query);
	//print $Query;exit;
	$x=safe_num_rows($Rs);

	if ($x==0)
	{
		$Out='<tr><th class="Title">' . get_text('ArcherNotFound','Tournament') . '</th></tr>' . "\n";
	}
	elseif ($x==1)
	{
		$Rank=0;
		$Div='';
		$Cl='';
		$r=safe_fetch($Rs);
		$Rank=$r->QuClRank;
		$Div=$r->EnDivision;
		$Cl=$r->EnClass;


		$MyQuery = "SELECT EnId,EnCode as Bib, EnName AS Name, EnFirstName AS FirstName, SUBSTRING(QuTargetNo,1,1) AS SessionNo, SUBSTRING(QuTargetNo,2) AS TargetNo, CoCode AS NationCode, CoName AS Nation, EnClass AS ClassCode, EnDivision AS DivCode,EnAgeClass as AgeClass,  EnSubClass as SubClass, ClDescription, DivDescription, ";
			$MyQuery.= "ToType, ToNumDist as NumDist, IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8, ";
			$MyQuery.= "QuD1Score, QuD1Rank, QuD2Score, QuD2Rank, QuD3Score, QuD3Rank, QuD4Score, QuD4Rank, ";
			$MyQuery.= "QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank, ";
			$MyQuery.= "QuScore, QuClRank AS `Rank`, QuGold, QuXnine, ToGolds AS TtGolds, ToXNine AS TtXNine ";
		$MyQuery.= "FROM Tournament AS t ";
			$MyQuery.= "INNER JOIN Entries AS e ON t.ToId=e.EnTournament ";
			$MyQuery.= "INNER JOIN Countries AS c ON e.EnCountry=c.CoId AND e.EnTournament=c.CoTournament ";
			$MyQuery.= "INNER JOIN Qualifications AS q ON e.EnId=q.QuId ";
			$MyQuery.= "INNER JOIN Classes AS cl ON e.EnClass=cl.ClId AND ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$MyQuery.= "INNER JOIN Divisions AS d ON e.EnDivision=d.DivId AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
			$MyQuery.= "LEFT JOIN TournamentDistances AS td ON t.ToType=td.TdType and TdTournament=ToId AND CONCAT(e.EnDivision,e.EnClass) LIKE TdClasses ";
		$MyQuery.= "WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND ToId = " . StrSafe_DB($_SESSION['TourId']) . " ";
			$MyQuery.= "AND EnDivision=" . StrSafe_DB($Div) . " AND EnClass=" . StrSafe_DB($Cl) . " and QuClRank!=0 ";
			$MyQuery.= "AND ((QuClRank>=" . ($Rank-3) . " AND QuClRank<=" . ($Rank+3) . ") OR (QuClRank<=3)) ";
		$MyQuery.= "ORDER BY QuClRank ASC";

		//print $MyQuery;exit;

		$Rs=safe_r_sql($MyQuery);

		if (safe_num_rows($Rs)>0)
		{
			$Header=false;
			$Divider=false;

			$TotCol=0;
			while ($MyRow=safe_fetch($Rs))
			{
			// l'header
				if (!$Header)
				{
					$TotCol=7+$MyRow->NumDist;
					$Out
						.='<tr>'
							. '<th class="Title" colspan="' . $TotCol. '">' . get_text($MyRow->DivDescription,'','',true) . " - " . get_text($MyRow->ClDescription,'','',true) . '</th>'
						. '</tr>' . "\n"
						. '<tr>'
							. '<th>' . get_text('Rank') . '</th>'
							. '<th>' . get_text('Target') . '</th>'
							. '<th>' . get_text('Archer') . '</th>'
							. '<th>' . get_text('Country') . '</th>';
					for($i=1; $i<=$MyRow->NumDist;$i++)
					{
						$Out
							.='<th>' .  $MyRow->{'Td' . $i} . '</th>';
					}
					$Out
							.='<th>' . get_text('TotaleScore') . '</th>'
							. ($MyRow->ToType!=14?'<th>' . $MyRow->TtGolds . '</th>':'')
							. '<th>' . $MyRow->TtXNine . '</th>'
						. '</tr>';

					$Header=true;
				}
			// righe
				if ($MyRow->Rank>3 && !$Divider)
				{
					$Divider=true;
					$Out.='<tr class="Spacer"><td class="Divider" colspan="' . $TotCol . '"></td></tr>' . "\n";
				}
				$style=($MyRow->EnId==$Id ? 'warning' : '');
				$Out
					.='<tr class="' . $style  .'">'
						. '<td>' . $MyRow->Rank . '</td>'
						. '<td>' . $MyRow->SessionNo . ' - ' . $MyRow->TargetNo  . '</td>'
						. '<td>' . $MyRow->FirstName . ' ' . $MyRow->Name . '</td>'
						. '<td>' . $MyRow->NationCode . ' - ' . $MyRow->Nation .  '</td>';
				for($i=1; $i<=$MyRow->NumDist;$i++)
				{
					$Out
						.='<td>' . str_pad(($MyRow->{'QuD' . $i . 'Score'}),3," ",STR_PAD_LEFT) . '</td>';
				}
				$Out
						.='<td>' . number_format(($MyRow->QuScore),0,'',get_text('NumberThousandsSeparator')) . '</td>'
						. ($MyRow->ToType!=14?'<td>' . $MyRow->QuGold . '</td>':'')
						. '<td>' . $MyRow->QuXnine . '</td>'
					. '</tr>' . "\n";
			}
		}
		else
		{
			$Out='<tr><th class="Title">' . get_text('Error') . '</th></tr>' . "\n";
		}

	}
	else
	{
		$Out='<tr><th class="Title">' . get_text('Error') . '</th></tr>' . "\n";
	}

	$JS_SCRIPT=array(
		'',
		'',
		'',
		'',
		);

	$PAGE_TITLE=get_text('FindYourRank', 'Tournament');

	include('Common/Templates/head.php');
?>
<table class="Tabella">
	<?php
		print $Out;
	?>
</table>
<br/>
<table class="Tabella">
	<tr><td class="Center"><a class="Link" href="RankPersonal1.php"><?php echo get_text('Back') ?></a></td></tr>
</table>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>