<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Lib/CommonLib.php');
	//print_r($_REQUEST);exit;

	CheckTourSession(true);
    checkACL(AclQualification, AclReadWrite);

	$BackNoFinal=0;
	if(!empty($_REQUEST["BackNo"])) $BackNoFinal = max(0, intval($_REQUEST['BackNo']));
	if($BackNoFinal>4) $BackNoFinal=0;
    switch($BackNoFinal) {
        case 0:
            checkACL(AclQualification, AclReadOnly);
            break;
        case 1:
            checkACL(AclIndividuals, AclReadOnly);
            break;
        case 2:
            checkACL(AclTeams, AclReadOnly);
            break;
        case 3:
        case 4:
        checkACL(AclEliminations, AclReadOnly);
            break;
    }


	if(isset($_REQUEST["deleteLayout"]))  {
		safe_w_sql("DELETE FROM BackNumber WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=". StrSafe_DB($BackNoFinal));
		cd_redirect('BackNumber.php?BackNo='.$BackNoFinal);
	}

	$RowBn=NULL;
	$Select
		= "(SELECT BackNumber.*, LENGTH(BnBackground) as ImgSize, 1 as Customized "
		. "FROM BackNumber  "
		. "WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=" . StrSafe_DB($BackNoFinal) . ")"
		. "UNION "
		. "(SELECT BackNumber.*, LENGTH(BnBackground) as ImgSize, 0 as Customized "
		. "FROM BackNumber  "
		. "WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=0) "
		. "LIMIT 1";
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)==1) {
		$RowBn=safe_fetch($Rs);
	} else {
		include('Tournament/BackNumberEmpty.php');
		$RowBn=emptyBackNumber();
	}

	if(isset($_REQUEST["Submit"]))
	{
		$TmpUpdate = '';
		foreach($_REQUEST as $Key => $Value)
		{
			if(substr($Key,0,2)=='Bn')
			{
				if(is_array($Value))
				{
					$Tmp = 0;
					foreach($Value as $SubValue)
						$Tmp += $SubValue;
					$TmpUpdate .= $Key . " = " . StrSafe_DB($Tmp) . ', ';
				}
				else
				{
					$TmpUpdate .= $Key . " = " . StrSafe_DB(str_replace('#','',$Value)) . ', ';
				}
			}
		}



		if(safe_num_rows($Rs)==0 || $RowBn->Customized==0)
			safe_w_sql("INSERT INTO BackNumber (BnTournament, BnFinal) VALUES (" . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB($BackNoFinal) . ")");

		safe_w_sql("UPDATE BackNumber SET " . substr($TmpUpdate,0,-2) . " WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=". StrSafe_DB($BackNoFinal));

		if(isset($_FILES['UploadedBgImage']) && $_FILES['UploadedBgImage']['error']==UPLOAD_ERR_OK && $_FILES['UploadedBgImage']['size']>0 && $_FILES['UploadedBgImage']['size']<=1048576)
		{
			$TmpData = addslashes($ImgString=fread(fopen($_FILES['UploadedBgImage']['tmp_name'], "r"), filesize($_FILES['UploadedBgImage']['tmp_name'])));
			$Rs=safe_w_sql("UPDATE BackNumber SET BnBackground='" . $TmpData . "' WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=" . StrSafe_DB($BackNoFinal));
			unlink($_FILES['UploadedBgImage']['tmp_name']);
			if($im=@imagecreatefromstring($ImgString)) imagejpeg($im, $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-BackNo-'.$BackNoFinal.'.jpg', 90);
		}
		elseif(isset($_REQUEST["DeleteBgImage"]) && $_REQUEST["DeleteBgImage"]==1)
		{
			$Rs=safe_w_sql("UPDATE BackNumber SET BnBackground='' WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=" . StrSafe_DB($BackNoFinal));
			@unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-BackNo-'.$BackNoFinal.'.jpg');
		}
		elseif($RowBn->Customized==0 && $RowBn->ImgSize>0)
		{
			safe_w_sql("UPDATE BackNumber SET BnBackground=" .  StrSafe_DB($RowBn->BnBackground) . " WHERE BnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND BnFinal=". StrSafe_DB($BackNoFinal));
		}
		$Rs=safe_r_sql($Select);
		$RowBn=safe_fetch($Rs);
	}


	$JS_SCRIPT=array(
		phpVars2js(array('MsgAreYouSure' => get_text('MsgAreYouSure'))),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
		);

	$PAGE_TITLE=get_text('BackNumbers', 'BackNumbers');

	include('Common/Templates/head.php');


	echo '<form id="PrnParameters" method="post" action="" enctype="multipart/form-data"><input type="hidden" name="BackNo" value="' . $BackNoFinal . '">';
	echo '<table class="Tabella">';
	echo '<tr><th class="Title" colspan="3">' . get_text('BackNumbers', 'BackNumbers')  . ' - ';
	switch($BackNoFinal){
		case 0:
			echo get_text('QualRound');
			break;
		case 1:
			echo get_text('IndFinal');
			break;
		case 2:
			echo get_text('TeamFinal');
			break;
		case 3:
			echo get_text('Eliminations_1');
			break;
		case 4:
			echo get_text('Eliminations_2');
			break;
	}
	echo '</th></tr>';
	echo '<tr><th class="SubTitle" width="50%">' . get_text('PageDimension', 'BackNumbers')  . '</th>';
	echo '<th class="SubTitle" width="0%">&nbsp;</th>';
	echo '<th class="SubTitle" width="50%">' . get_text('BgImage', 'BackNumbers')  . '</th></tr>';
//Parametri
	echo '<tr>';
//Dimensione Pettorale
	echo '<td width="50%"><br>';
	echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="BnHeight" id="BnHeight" size="7" maxlength="5" value="' .($RowBn ? $RowBn->BnHeight : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="BnWidth" id="BnWidth" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnWidth : '') . '"><br>&nbsp;<br>';
	echo  '<strong>' . get_text('Offest2nd', 'BackNumbers') . "</strong><br>" . get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="BnOffsetX" id="BnOffsetX" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnOffsetX : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="BnOffsetY" id="BnOffsetY" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnOffsetY : '') . '">';

	echo '</td>';
//Esempio...
	echo '<td width="0%" rowspan="2"><br>';
	echo '<img src="../Tournament/ImgBackNumber.php?IdTpl='.$BackNoFinal.'"></td>';
//Sfondo
	echo '<td width="50%" rowspan="2"><br>';
	if($RowBn && $RowBn->ImgSize>0)
	{
		echo '<input name="DeleteBgImage" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament') . "<br>&nbsp;<br>";
	}
	else
	{
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><input name="UploadedBgImage" type="file" size="20" /><br>&nbsp;<br>';
	}
	echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="BnBgX" id="BnBgX" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnBgX : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="BnBgY" id="BnBgY" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnBgY : '') . '"><br>&nbsp;<br>';
	echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="BnBgH" id="BnBgH" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnBgH : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="BnBgW" id="BnBgW" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnBgW : '') . '"><br>&nbsp;<br>';
	echo '</td>';
	echo '</tr>';
//Operazioni
	echo '<tr><td>';
	$BackTo=$CFG->ROOT_DIR;
	switch ($BackNoFinal)
	{
		case 0:
			$BackTo.='Qualification/';
			break;
		case 1:
			$BackTo.='Final/Individual/';
			break;
		case 2:
			$BackTo.='Final/Team/';
			break;
		case 3:
		case 4:
			$BackTo.='Elimination/';
			break;
		default:
			$BackTo.='Tournament/';
	}
	echo '<input name="Print" type="button" value="' . get_text('PrintBackNo','BackNumbers') . '" onClick="javascipt:document.location=\'' . $BackTo . 'PrintBackNo.php\'">';
	echo str_repeat("&nbsp;",10);
	echo '<form action="" method="POST">';
	echo '<input name="deleteLayout" type="submit" value="' . get_text('ResetBackNo','BackNumbers') . '" onclick="return(confirm(\''.get_text('MsgAreYouSure').'\'))">';
	echo '</form>';
	echo '</td></tr>';
//Pettorale
	echo '<tr><th class="SubTitle" colspan="3">' . get_text('Target')  . '</th></tr>';
	echo '<tr>';
	echo '<td width="50%"><br>';
	echo  get_text('IncludeSession','BackNumbers') . '&nbsp;<select name="BnIncludeSession"><option value="0"' . ($RowBn && $RowBn->BnIncludeSession == 0 ? ' selected' : '') . '>' . get_text('NoSession','Tournament') . '</option><option value="1"' . ($RowBn && $RowBn->BnIncludeSession == 1 ? ' selected' : '') . '>' . get_text('NumSession', 'Tournament') . '</option><option value="2"' . ($RowBn && $RowBn->BnIncludeSession == 2 ? ' selected' : '') . '>' . get_text('SessionDescr', 'Tournament') . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<br>&nbsp;<br>';
	echo  get_text('Print', 'Tournament') . '&nbsp;<input type="checkbox" name="BnTargetNo[]" value="1"' . (($RowBn && $RowBn->BnTargetNo & 1) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharType', 'BackNumbers') . '&nbsp;<select name="BnTargetNo[]"><option value="0"' . (($RowBn && $RowBn->BnTargetNo & 6) == 0 ? ' selected' : '') . '>Arial</option><option value="2"' . (($RowBn && $RowBn->BnTargetNo & 2) == 2 ? ' selected' : '') . '>Times</option><option value="4"' . (($RowBn && $RowBn->BnTargetNo & 4) == 4 ? ' selected' : '') . '>Courier</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharColor', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoColor" id="BnTnoColor" size="6" maxlength="7" value="#' . ($RowBn ? $RowBn->BnTnoColor : '') . '">&nbsp;<input type="text" id="Ex_BnTnoColor" size="1" style="background-color:#' . ($RowBn ? $RowBn->BnTnoColor : '') . '" readonly>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'BnTnoColor\',\'Ex_BnTnoColor\');">';
	echo '<br>&nbsp;<br>';
	echo  get_text('Bold', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnTargetNo[]" value="8"' . (($RowBn && $RowBn->BnTargetNo & 8) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Italic', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnTargetNo[]" value="16"' . (($RowBn && $RowBn->BnTargetNo & 16) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Alignment', 'BackNumbers') . '&nbsp;<select name="BnTargetNo[]"><option value="32"' . ($RowBn && ($RowBn->BnTargetNo & 96) == 32 ? ' selected' : '') . '>' . get_text('AlignR', 'BackNumbers') . '</option><option value="64"' . ($RowBn && ($RowBn->BnTargetNo & 96) == 64 ? ' selected' : '') . '>' . get_text('AlignL', 'BackNumbers') . '</option><option value="96"' . ($RowBn && ($RowBn->BnTargetNo & 96) == 96 ? ' selected' : '') . '>' . get_text('AlignC', 'BackNumbers') . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharSize', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoSize" id="BnTnoSize" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnTnoSize : '') . '">';
	echo '</td>';
	echo '<td width="0%">&nbsp;</td>';
	echo '<td width="50%"><br>';
	echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoX" id="BnTnoX" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnTnoX : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoY" id="BnTnoY" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnTnoY : '') . '"><br>&nbsp;<br>';
	echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoH" id="BnTnoH" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnTnoH : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="BnTnoW" id="BnTnoW" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnTnoW : '') . '"><br>&nbsp;<br>';
	echo '</td>';
	echo '</tr>';
//Atleta
	echo '<tr><th class="SubTitle" colspan="3">' . get_text('Athlete')  . '</th></tr>';
	echo '<tr>';
	echo '<td width="50%"><br>';
	echo  get_text('AllCaps', 'BackNumbers') . '&nbsp;<select name="BnCapitalFirstName" id="BnCapitalFirstName">'
		. '<option value="">'.get_text('No').'</option>'
		. '<option value="1"'.($RowBn && $RowBn->BnCapitalFirstName ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>'
		. '</select>';
	echo  '&nbsp;&nbsp;' . get_text('GivenNameInitial', 'BackNumbers') . '&nbsp;<select name="BnGivenNameInitial" id="BnGivenNameInitial">'
		. '<option value="">'.get_text('No').'</option>'
		. '<option value="1"'.($RowBn && $RowBn->BnGivenNameInitial ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>'
		. '</select>';
	echo '<br>&nbsp;<br>';
	echo  get_text('Print', 'Tournament') . '&nbsp;<input type="checkbox" name="BnAthlete[]" value="1"' . (($RowBn && ($RowBn->BnAthlete & 1)) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharType', 'BackNumbers') . '&nbsp;<select name="BnAthlete[]"><option value="0"' . (($RowBn && ($RowBn->BnAthlete & 6)) == 0 ? ' selected' : '') . '>Arial</option><option value="2"' . (($RowBn && ($RowBn->BnAthlete & 2)) == 2 ? ' selected' : '') . '>Times</option><option value="4"' . (($RowBn && ($RowBn->BnAthlete & 4)) == 4 ? ' selected' : '') . '>Courier</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharColor', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthColor" id="BnAthColor" size="6" maxlength="7" value="#' . ($RowBn ? $RowBn->BnAthColor : '') . '">&nbsp;<input type="text" id="Ex_BnAthColor" size="1" style="background-color:#' . ($RowBn ? $RowBn->BnAthColor : '') . '" readonly>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'BnAthColor\',\'Ex_BnAthColor\');">';
	echo '<br>&nbsp;<br>';
	echo  get_text('Bold', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnAthlete[]" value="8"' . (($RowBn && $RowBn->BnAthlete & 8) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Italic', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnAthlete[]" value="16"' . (($RowBn && $RowBn->BnAthlete & 16) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Alignment', 'BackNumbers') . '&nbsp;<select name="BnAthlete[]"><option value="32"' . ($RowBn && ($RowBn->BnAthlete & 96) == 32 ? ' selected' : '') . '>' . get_text('AlignR', 'BackNumbers') . '</option><option value="64"' . ($RowBn && ($RowBn->BnAthlete & 96) == 64 ? ' selected' : '') . '>' . get_text('AlignL', 'BackNumbers') . '</option><option value="96"' . ($RowBn && ($RowBn->BnAthlete & 96) == 96 ? ' selected' : '') . '>' . get_text('AlignC', 'BackNumbers') . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharSize', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthSize" id="BnAthSize" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnAthSize : '') . '">';
	echo '</td>';
	echo '<td width="0%">&nbsp;</td>';
	echo '<td width="50%"><br>';
	echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthX" id="BnAthX" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnAthX : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthY" id="BnAthY" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnAthY : '') . '"><br>&nbsp;<br>';
	echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthH" id="BnAthH" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnAthH : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="BnAthW" id="BnAthW" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnAthW : '') . '"><br>&nbsp;<br>';
	echo '</td>';
	echo '</tr>';
//Squadra
	echo '<tr><th class="SubTitle" colspan="3">' . get_text('Country')  . '</th></tr>';
	echo '<tr>';
	echo '<td width="50%"><br>';
	echo  get_text('CountryCodeOnly', 'BackNumbers') . '&nbsp;<select name="BnCountryCodeOnly" id="BnCountryCodeOnly">'
		. '<option value="">'.get_text('CountryCodeOnly','BackNumbers').'</option>'
		. '<option value="1"'.($RowBn->BnCountryCodeOnly==1 ? ' selected="selected"' : '').'>'.get_text('CountryLongName','BackNumbers').'</option>'
		. '<option value="2"'.($RowBn->BnCountryCodeOnly==2 ? ' selected="selected"' : '').'>'.get_text('CountryCodeAndLong','BackNumbers').'</option>'
		. '<option value="3"'.($RowBn->BnCountryCodeOnly==3 ? ' selected="selected"' : '').'>'.get_text('CountryFlagAndLong','BackNumbers').'</option>'
		. '<option value="4"'.($RowBn->BnCountryCodeOnly==4 ? ' selected="selected"' : '').'>'.get_text('CountryFlagOnly','BackNumbers').'</option>'
		. '</select>';
	echo '<br>&nbsp;<br>';
	echo  get_text('Print', 'Tournament') . '&nbsp;<input type="checkbox" name="BnCountry[]" value="1"' . (($RowBn && $RowBn->BnCountry & 1) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharType', 'BackNumbers') . '&nbsp;<select name="BnCountry[]"><option value="0"' . (($RowBn && $RowBn->BnCountry & 6) == 0 ? ' selected' : '') . '>Arial</option><option value="2"' . (($RowBn && $RowBn->BnCountry & 2) == 2 ? ' selected' : '') . '>Times</option><option value="4"' . (($RowBn && $RowBn->BnCountry & 4) == 4 ? ' selected' : '') . '>Courier</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharColor', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoColor" id="BnCoColor" size="6" maxlength="7" value="#' . ($RowBn ? $RowBn->BnCoColor : '') . '">&nbsp;<input type="text" id="Ex_BnCoColor" size="1" style="background-color:#' . ($RowBn ? $RowBn->BnCoColor : '') . '" readonly>&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'BnCoColor\',\'Ex_BnCoColor\');">';
	echo '<br>&nbsp;<br>';
	echo  get_text('Bold', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnCountry[]" value="8"' . (($RowBn && $RowBn->BnCountry & 8) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Italic', 'BackNumbers') . '&nbsp;<input type="checkbox" name="BnCountry[]" value="16"' . (($RowBn && $RowBn->BnCountry & 16) != 0 ? ' checked' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Alignment', 'BackNumbers') . '&nbsp;<select name="BnCountry[]"><option value="32"' . ($RowBn && ($RowBn->BnCountry & 96) == 32 ? ' selected' : '') . '>' . get_text('AlignR', 'BackNumbers') . '</option><option value="64"' . ($RowBn && ($RowBn->BnCountry & 96) == 64 ? ' selected' : '') . '>' . get_text('AlignL', 'BackNumbers') . '</option><option value="96"' . ($RowBn && ($RowBn->BnCountry & 96) == 96 ? ' selected' : '') . '>' . get_text('AlignC', 'BackNumbers') . '</option></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('CharSize', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoSize" id="BnCoSize" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnCoSize : '') . '">';
	echo '</td>';
	echo '<td width="0%">&nbsp;</td>';
	echo '<td width="50%"><br>';
	echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoX" id="BnCoX" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnCoX : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoY" id="BnCoY" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnCoY : '') . '"><br>&nbsp;<br>';
	echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoH" id="BnCoH" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnCoH : '') . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="BnCoW" id="BnCoW" size="7" maxlength="5" value="' . ($RowBn ? $RowBn->BnCoW : '') . '"><br>&nbsp;<br>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="3" align="Center"><br>';
	echo '<input name="Submit" type="submit" value="' . get_text('CmdSave') . '"><br>&nbsp;<br>';
	echo '</td>';
	echo '</tr>';
	echo '<tr class="Divider"><td  colspan="3"></td></tr>';




	echo '</table>';
	echo '</form>';


	include('Common/Templates/tail.php');
?>