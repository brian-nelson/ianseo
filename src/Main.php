<?php
	require_once('./config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	CheckTourSession(true);
    $aclLevel = checkACL(AclCompetition,AclNoAccess);

	if(!empty($_REQUEST['redraw'])) {
		include_once('Common/CheckPictures.php');
		CheckPictures('', true, false, !empty($_REQUEST['force'])); // cancella le foto più vecchie di 1 giorno
	}

	$PAGE_TITLE=get_text('TourMainInfo', 'Tournament');

	include('Common/Templates/head.php');


	$MyRow=NULL;
	/*$Select
		= "SELECT ToId,ToType,ToCode,ToName,ToCommitee,ToComDescr,ToWhere,DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom,DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, "
		. "DATE_FORMAT(ToWhenFrom,'%d') AS DtFromDay,DATE_FORMAT(ToWhenFrom,'%m') AS DtFromMonth,DATE_FORMAT(ToWhenFrom,'%Y') AS DtFromYear, "
		. "DATE_FORMAT(ToWhenTo,'%d') AS DtToDay,DATE_FORMAT(ToWhenTo,'%m') AS DtToMonth,DATE_FORMAT(ToWhenTo,'%Y') AS DtToYear, "
		. "ToNumSession, ToTar4Session1, ToTar4Session2,ToTar4Session3,ToTar4Session4,ToTar4Session5,ToTar4Session6,ToTar4Session7,ToTar4Session8,ToTar4Session9, "
		. "ToAth4Target1,ToAth4Target2,ToAth4Target3,ToAth4Target4,ToAth4Target5,ToAth4Target6,ToAth4Target7,ToAth4Target8,	ToAth4Target9, "
		. "TtName,TtNumDist "
		. "FROM Tournament LEFT JOIN Tournament*Type ON ToType=TtId "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

	/*$Select
		= "SELECT ToId,ToType,ToCode,ToName,ToCommitee,ToComDescr,ToWhere,DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom,DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, "
		. "DATE_FORMAT(ToWhenFrom,'%d') AS DtFromDay,DATE_FORMAT(ToWhenFrom,'%m') AS DtFromMonth,DATE_FORMAT(ToWhenFrom,'%Y') AS DtFromYear, "
		. "DATE_FORMAT(ToWhenTo,'%d') AS DtToDay,DATE_FORMAT(ToWhenTo,'%m') AS DtToMonth,DATE_FORMAT(ToWhenTo,'%Y') AS DtToYear, "
		. "ToNumSession, ToTar4Session1, ToTar4Session2,ToTar4Session3,ToTar4Session4,ToTar4Session5,ToTar4Session6,ToTar4Session7,ToTar4Session8,ToTar4Session9, "
		. "ToAth4Target1,ToAth4Target2,ToAth4Target3,ToAth4Target4,ToAth4Target5,ToAth4Target6,ToAth4Target7,ToAth4Target8,	ToAth4Target9, "
		. "ToTypeName AS TtName,ToNumDist AS TtNumDist "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";*/

	$Select
		= "SELECT ToId,ToType,ToCode,ToName,ToNameShort,ToCommitee,ToComDescr,ToWhere,DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom,DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, "
		. "DATE_FORMAT(ToWhenFrom,'%d') AS DtFromDay,DATE_FORMAT(ToWhenFrom,'%m') AS DtFromMonth,DATE_FORMAT(ToWhenFrom,'%Y') AS DtFromYear, "
		. "DATE_FORMAT(ToWhenTo,'%d') AS DtToDay,DATE_FORMAT(ToWhenTo,'%m') AS DtToMonth,DATE_FORMAT(ToWhenTo,'%Y') AS DtToYear, "
		. "ToNumSession, ToTypeName AS TtName,ToNumDist AS TtNumDist "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1)
	{
		$MyRow=safe_fetch($Rs);
?>
<table class="Tabella">
<tr><th class="Title" colspan="2"><?php print (isset($_REQUEST['New']) ? get_text('NewTour', 'Tournament') : ManageHTML($MyRow->ToName)); ?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tr><td class="Title" colspan="2"><?php echo get_text('TourMainInfo', 'Tournament') ?></td></tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourCode','Tournament');?></th>
<td class="Bold"><?php print $MyRow->ToCode; ?></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourName','Tournament');?></th>
<td class="Bold"><?php print ManageHTML($MyRow->ToName); ?></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourShortName','Tournament');?></th>
<td class="Bold"><?php print ManageHTML($MyRow->ToNameShort); ?></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourCommitee','Tournament');?></th>
<td>
<?php
	print $MyRow->ToCommitee . ' - ' . ManageHTML($MyRow->ToComDescr);
?>
</td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourType','Tournament');?></th>
<td>
<?php
		/*$Sel = "SELECT * FROM Tournament*Type WHERE TtId=" . StrSafe_DB($MyRow->ToType) . " ORDER BY TtOrder ASC ";
		$RsSel=safe_r_sql($Sel);
		if (safe_num_rows($RsSel)==1)
		{
			$Row=safe_fetch($RsSel);
			print ManageHTML(get_text($Row->TtName, 'Tournament')) . ', ' . $Row->TtNumDist . ' ' . get_text($Row->TtNumDist==1?'Distance':'Distances','Tournament');
		}*/

	// in questo caso non riscrivo la query usando le colonne dei tipi delle Tournament ma piglio dalla $MyRow estratta sopra
		print ManageHTML(get_text($MyRow->TtName, 'Tournament')) . ', ' . $MyRow->TtNumDist . ' ' . get_text($MyRow->TtNumDist==1?'Distance':'Distances','Tournament');
?>

</td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourIsOris','Tournament');?></th>
<td>
<?php
	print get_text($_SESSION['ISORIS'] ? 'Yes' : 'No');
?>
</td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourWhere','Tournament');?></th>
<td><?php print ManageHTML($MyRow->ToWhere);?></td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php print get_text('TourWhen','Tournament');?></th>
<td><?php print get_text('From','Tournament') . ' ' . $MyRow->DtFrom . '<br>' . get_text('To','Tournament') . '&nbsp;&nbsp;&nbsp;' . $MyRow->DtTo; ?>
</td>
</tr>

<tr>
<th class="TitleLeft" width="15%"><?php echo get_text('NumSession', 'Tournament') ?></th>
<td><?php print $MyRow->ToNumSession; ?></td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('SessionDescr', 'Tournament');?></th>
<td>
<?php
		if ($MyRow->ToNumSession>0) {
		// info sessioni
			$sessions=GetSessions('Q');

			foreach ($sessions as $s)
			{
				print get_text('Session') . ' ' . $s->SesOrder . ': ' . $s->SesName . ' --> ' . $s->SesTar4Session . ' ' . get_text('Targets', 'Tournament') . ', ' . $s->SesAth4Target . ' ' . get_text('Ath4Target', 'Tournament')  . '<br>';
			}
		} else {
			print get_text('NoSession','Tournament');
		}
?>
</td>
</tr>
<tr>
<th class="TitleLeft" width="15%"><?php print get_text('StaffOnField','Tournament');?></th>
<td>
<?php
		$Select
			= "SELECT ti.*, it.*"
			. "FROM TournamentInvolved AS ti LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId "
			. "WHERE ti.TiTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
			. "ORDER BY ti.TiType ASC,ti.TiName ASC ";
		$RsS = safe_r_sql($Select);
		if (safe_num_rows($RsS)>0)
		{
			while ($Row=safe_fetch($RsS))
			{
				print ManageHTML($Row->TiName) ;
				if($Row->TiCode) print ' (' . $Row->TiCode . ')';
				print ', ' . get_text($Row->ItDescription,'Tournament') . '<br>';
			}
		}
		else
		{
			print get_text('NoStaffOnField','Tournament');
		}
?>
</td>
</tr>
<?php
    if($aclLevel == AclReadWrite) {
        echo '<tr>';
        echo '<th class="TitleLeft" width="15%">' . get_text('Photo', 'Tournament') . '</th>';
        echo '<td><a href="?redraw=1" class="Link">' . get_text('RedrawPictures', 'Tournament') . '</a><br/><a href="?redraw=1&force=1" class="Link">' . get_text('RecreatePictures', 'Tournament') . '</a></td>';
        echo '</tr>';
    }
    if($INFO->ACLEnabled and ($_SERVER["REMOTE_ADDR"]!='::1' AND $_SERVER["REMOTE_ADDR"]!='127.0.0.1')) {
        echo '<tr class="Divider"><td colspan="2"></td></tr>';
        echo '<tr>';
        echo '<th class="TitleLeft" width="15%">'.get_text('Block_IP','Tournament').'</th>';
        echo '<td style="font-size: 200%;">'.$_SERVER["REMOTE_ADDR"].'</td>';
        echo '</tr>';
    }
?>
        </table>
<?php
	}

	include('Common/Templates/tail.php');
?>