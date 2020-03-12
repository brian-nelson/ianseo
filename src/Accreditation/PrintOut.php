<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadOnly);
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Sessions.inc.php');

	$Arr_Operations = array();
	$NumOp = 0;

	$MyQuery="SELECT AOTId, AOTDescr FROM AccOperationType ORDER BY AOTDescr";
	$Rs=safe_r_sql($MyQuery);
	$SelectOperation="";
	if(safe_num_rows($Rs)>0)
	{
		$SelectOperation = '<br /><select name="OperationType">';
		while($MyRow=safe_fetch($Rs))
		{
			$SelectOperation .= '<option value="' . $MyRow->AOTDescr . '">' . get_text($MyRow->AOTDescr,'Tournament')  . '</option>';
			$Arr_Operations[$MyRow->AOTDescr] = get_text($MyRow->AOTDescr,'Tournament');
		}
		$SelectOperation .= '</select><br />';
		$NumOp=count($Arr_Operations);
		safe_free_result($Rs);
	}

	include('Common/Templates/head.php');


	$SesNo=0;

	$sessions=GetSessions('Q');

	$SesNo=count($sessions);

	$ComboSessions= '<select name="Session">' . "\n";
		$ComboSessions.='<option value="All">' . get_text('AllSessions','Tournament') . '</option>' . "\n";
		foreach ($sessions AS $s)
		{
			$ComboSessions.='<option value="' . $s->SesOrder. '">' . $s->Descr . '</option>' . "\n";
		}
	$ComboSessions.='</select>' . "\n";


	switch ($NumOp)
	{
		case 1:
		case 2:
		case 3:
			$SmallCellW = 20;
			break;
		case 4:
		case 5:
			$SmallCellW = 15;
			break;
		case 6:
		case 7:
			$SmallCellW = 10;
			break;
		case 8:
		case 9:
			$SmallCellW = 7;
			break;
	}
	//$SmallCellW = ceil(100/$NumOp);

	print '<table class="Tabella">'  . "\n";
	print '<tr><th class="Title" colspan="' . $NumOp . '">' . get_text('PrintList','Tournament')  . '</th></tr>' . "\n";
// Lista Piazzole
	print '<tr><th class="SubTitle" colspan="' . ($NumOp) . '">' . get_text('StartlistSession','Tournament')  . '</th></tr>' . "\n";
	print '<tr>';
	foreach($Arr_Operations as $Key=>$Value)
	{
		print '<td class="Center" width="' . $SmallCellW . '%">';
		print '<a href="PrnSession.php?OperationType=' . $Key . '" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('StartlistSession','Tournament') . '" border="0"><br>' . $Value . '</a>';
		print '</td>';
	}
	print '</tr>' . "\n";
	echo '<tr class="Divider"><td  colspan="' . ($NumOp) . '">&nbsp;</td></tr>';
// Lista per società
	print '<tr><th class="SubTitle" colspan="' . ($NumOp) . '">' . get_text('StartlistCountry','Tournament')  . '</th></tr>' . "\n";
	print '<tr>';
	foreach($Arr_Operations as $Key=>$Value)
	{
		print '<td class="Center" width="' . $SmallCellW . '%">';
		print '<a href="PrnCountry.php?OperationType=' . $Key . '" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('StartlistCountry','Tournament') . '" border="0"><br>' . $Value . '</a>';
		print '</td>';
	}
	print '</tr>' . "\n";
	echo '<tr class="Divider"><td  colspan="' . ($NumOp) . '">&nbsp;</td></tr>';
// Ordine alfabetico
	print '<tr><th class="SubTitle" colspan="' . ($NumOp) . '">' . get_text('StartlistAlpha','Tournament')  . '</th></tr>' . "\n";
	print '<tr>';
	foreach($Arr_Operations as $Key=>$Value)
	{
		print '<td class="Center" width="' . $SmallCellW . '%">';
		print '<a href="PrnAlphabetical.php?OperationType=' . $Key . '" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('StartlistAlpha','Tournament') . '" border="0"><br>' . $Value . '</a>';
		print '</td>';
	}
	print '</tr>' . "\n";
	echo '<tr class="Divider"><td  colspan="' . ($NumOp) . '">&nbsp;</td></tr>';
// IdCard & Label
	print '<tr><th class="SubTitle" colspan="' . ($NumOp) . '">' . get_text('Partecipants') . '</th></tr>' . "\n";
	print '<tr>';
	print '<td class="Center">';
	print '<a href="IdCards.php" class="Link"><img src="../Common/Images/pdf.gif" alt="' . get_text('IdCard','Tournament') . '" border="0"><br>' . get_text('IdCard','Tournament') . '</a>';
	print '</td>';
	print '<td class="Center">';
	print '<a href="PrnLabels.php" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('PartecipantLabel','Tournament') . '" border="0"><br>' . get_text('PartecipantLabel','Tournament') . '</a>';
	print '</td>';
	print '</tr>';
	echo '<tr class="Divider"><td  colspan="' . ($NumOp) . '">&nbsp;</td></tr>';
// Bill e Cash
	print '<tr><th class="SubTitle" colspan="2">' . get_text('BillAndCash','Tournament') . '</th></tr>' . "\n";
	print '<tr>';
	print '<td class="Center" width="25%">';
	print '<a href="PrnBill.php" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('Bill','Tournament') . '" border="0"><br>' . get_text('Bill','Tournament') . '</a>';
	print '</td>';

	print '<td class="Center" width="25%">';
	print '<a href="PrnCash.php" class="Link" target="PrintOut"><img src="../Common/Images/pdf.gif" alt="' . get_text('Cash','Tournament') . '" border="0"><br>' . get_text('Cash','Tournament') . '</a>';
	print '</td>';
	print '</tr>' . "\n";
	print '</table>' . "\n";

	echo '<br />';
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="5">' . get_text('PrintList','Tournament')  . '</th></tr>';
	echo '<tr>';
	echo '<th class="SubTitle" width="18%">' . get_text('StartlistSession','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="18%">' . get_text('StartlistCountry','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="18%">' . get_text('StartlistAlpha','Tournament')  . '</th>';
	echo '<th class="SubTitle" width="18%">' . get_text('PartecipantLabel','Tournament')  . '</th>';
	//echo '<th class="SubTitle" width="10%">' . get_text('IdCard','Tournament') . '</th>';
	echo '<th class="SubTitle" width="18%">' . get_text('Bill','Tournament') . '</th>';
	echo '</tr>';
	echo '<tr>';
//Stampa Piazzole
	echo '<td width="18%" class="Center"><form action="PrnSession.php" method="get" target="PrintOut">&nbsp;';
	echo $SelectOperation;
	echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;' . $ComboSessions;
	echo '<br />&nbsp;<br />';
	echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
	echo '</form></td>';
//Elenco per Società
	echo '<td width="18%" class="Center"><form action="PrnCountry.php" method="get" target="PrintOut">&nbsp;';
	echo $SelectOperation;
	echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;'. $ComboSessions;
	echo '<br />&nbsp;<br />';
	echo get_text('Country') . '&nbsp;&nbsp;&nbsp;<input name="CountryName" type="text" size="20" maxlength="30">';
	echo '<br />&nbsp;<br />';
	echo get_text('NoPhoto') . '&nbsp;&nbsp;&nbsp;<input name="NoPhoto" type="checkbox" size="20" maxlength="30">';
	echo '<br />&nbsp;<br />';
	echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
	echo '</form></td>';
//Elenco Alfabetico
	echo '<td width="18%" class="Center"><form action="PrnAlphabetical.php" method="get" target="PrintOut">&nbsp;';
	echo $SelectOperation;
	echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;'. $ComboSessions;
	echo '<br />&nbsp;<br />';
	echo get_text('Archers') . '&nbsp;&nbsp;&nbsp;<input name="ArcherName" type="text" size="20" maxlength="30">';
	echo '<br />&nbsp;<br />';
	echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
	echo '</form></td>';
//Etichette
	echo '<td width="18%" class="Center"><form action="PrnLabels.php" method="get" target="PrintOut">&nbsp;';
	echo $SelectOperation;
	echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;'. $ComboSessions;
	echo '<br />&nbsp;<br />';
	echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
	echo '</form></td>';
// idcard
//		echo '<td width="10%" class="Center"><form action="Card.php" method="get" target="PrintOut">&nbsp;';
//		echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;';
//		echo '<select name="Session">';
//		echo '<option value="">' . get_text('AllSessions','Tournament') . '</option>';
//		for($i=0; $i<=$SesNo; $i++)
//			echo '<option value="' . $i . '">' . $i . '</option>';
//		echo '</select><br />&nbsp;<br />';
//		echo get_text('Name','Tournament') . '&nbsp;&nbsp;&nbsp;<input name="ArcherName" type="text" size="20" maxlength="30">';
//		echo '<br />&nbsp;<br />';
//		echo get_text('Country') . '&nbsp;&nbsp;&nbsp;<input name="CountryName" type="text" size="20" maxlength="30">';
//		echo '<br />&nbsp;<br />';
//		echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
//		echo '</form></td>';
// Bill
	echo '<td width="18%" class="Center"><form action="PrnBill.php" method="get" target="PrintOut">&nbsp;';
	echo '<input type="hidden" name="OperationType" value="Accreditation">';
	echo '<br />' . get_text('Session') . '&nbsp;&nbsp;&nbsp;'. $ComboSessions;
	echo '<br />&nbsp;<br />';
	echo get_text('Country') . '&nbsp;&nbsp;&nbsp;<input name="CountryName" type="text" size="20" maxlength="30">';
	echo '<br />&nbsp;<br />';
	echo '<input type="submit" name="Submit" value="' . get_text('CmdOk') . '">';
	echo '</form></td>';
	echo '</tr>';

	echo '</table>';

	include('Common/Templates/tail.php');
?>