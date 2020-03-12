<?php
/*

=> IDcard can have a background
=> IDcard can have a back with schedule AND-OR a freetext
=> ALL elements are positional (negative x or y position means not printed at all)



*/

// ACL and other checks are made in the config
require_once('./IdCardEdit-config.php');

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
require_once('IdCardEmpty.php');

define('MAX_PHOTO_PIXEL', 3000);

$CardFile="{$CardType}-{$CardNumber}";

if(isset($_GET['delete'])) {
	$IdOrder=intval($_GET['delete']);

	safe_w_sql("delete from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber and IceOrder=$IdOrder");
	if(file_exists($File=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$CardFile."-{$IdOrder}.jpg")) unlink($File);
	if(file_exists($File=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-RandomImage-'.$CardFile."-{$IdOrder}.jpg")) unlink($File);

	CD_redirect(basename(__FILE__).go_get('delete', '', true));
}

$RowBn=NULL;
$Select
	= "SELECT IdCards.*, LENGTH(IcBackground) as ImgSize "
	. "FROM IdCards  "
	. "WHERE IcTournament={$_SESSION['TourId']} and IcType='$CardType' and IcNumber=$CardNumber";
$Rs=safe_r_sql($Select);

if(!empty($_REQUEST['DeleteBgImage'])) {
	safe_w_sql("update IdCards set IcBackground='' where IcTournament={$_SESSION['TourId']} and IcType='$CardType' and IcNumber=$CardNumber");
	unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$CardFile.'-Accreditation.jpg');
}

if(!empty($_FILES["Content"])) {
	// ATTENTION!!!!
	foreach ($_FILES["Content"]['size'] as $IdOrder => $Options) {
		$q=safe_r_sql("select * from IdCardElements where IceCardType='$CardType' and IceCardNumber=$CardNumber and IceOrder=$IdOrder and IceTournament={$_SESSION['TourId']}");
		if(!($r=safe_fetch($q))) {
			continue;
		}
		$SQL = array();
		if (!empty($_FILES['Content']['size'][$IdOrder]['Image'])) {
			unset($img);
			switch ($_FILES['Content']['type'][$IdOrder]['Image']) {
				case 'image/png':
					$img = imagecreatefrompng($_FILES['Content']['tmp_name'][$IdOrder]['Image']);
				case 'image/jpeg':
					if (!isset($img)) $img = imagecreatefromjpeg($_FILES['Content']['tmp_name'][$IdOrder]['Image']);
					break;
			}
			if (!empty($img)) {
				$tmpfile = $CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '-' . $r->IceType . '-' . $CardFile . '-' . $IdOrder . '.jpg';
				$srcW = imagesx($img);
				$srcH = imagesy($img);
				if ($srcW > MAX_PHOTO_PIXEL or $srcH > MAX_PHOTO_PIXEL) {
					// max dimension is a square of 2000 pixel!
					$ratio = 1;
					if ($srcW > MAX_PHOTO_PIXEL) $ratio = MAX_PHOTO_PIXEL / $srcW;
					if ($srcH > MAX_PHOTO_PIXEL) $ratio = min($ratio, MAX_PHOTO_PIXEL / $srcH);
					$dstW = intval($srcW * $ratio);
					$dstH = intval($srcH * $ratio);
					$im2 = imagecreatetruecolor($dstW, $dstH);
					imagecopyresampled($im2, $img, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
					imagejpeg($im2, $tmpfile, 85);
				} else {
					imagejpeg($img, $tmpfile, 85);
				}
				$SQL[] = 'IceContent=' . StrSafe_DB(file_get_contents($tmpfile));
			}
		}
		if (!empty($_FILES['Content']['size'][$IdOrder]['ImageSvg'])) {
			$img = file_get_contents($_FILES['Content']['tmp_name'][$IdOrder]['ImageSvg']);

			if (!empty($img)) {
				$tmpfile = $CFG->DOCUMENT_PATH . 'TV/Photos/' . $_SESSION['TourCodeSafe'] . '-' . $r->IceType . '-' . $CardFile . '-' . $IdOrder . '.svg';
				file_put_contents($tmpfile, $img);
				$SQL[] = 'IceContent=' . StrSafe_DB(gzdeflate($img));
			}
		}

		if($SQL) {
			safe_w_sql("update IdCardElements set " . implode(',', $SQL) . " where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber and IceOrder=$IdOrder");
		}
	}
}

if(!empty($_REQUEST["NewContent"])) {
	$q=safe_r_sql("select max(IceOrder)+1 NewOrder from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber");
	if($r=safe_fetch($q) and $r->NewOrder) {
		$NewOrder=$r->NewOrder;
	} else {
		$NewOrder=1;
	}
	$Options='';
	if($_REQUEST["NewContent"]=='RandomImage') {
		// selects the defaults from the previous images...
		$q=safe_r_sql("select IceOptions from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber and IceType=".StrSafe_DB($_REQUEST["NewContent"]));
		if($r=safe_fetch($q)) {
			$Options=', IceOptions='.StrSafe_DB($r->IceOptions);
		}
	}
	$sql="IceTournament={$_SESSION['TourId']}, IceCardType='$CardType', IceCardNumber=$CardNumber, IceOrder=$NewOrder, IceType=".StrSafe_DB($_REQUEST["NewContent"]);
	safe_w_sql("INSERT INTO IdCardElements set $sql $Options");
	$SetOrder=intval($_REQUEST["NewOrder"]);
	if(!empty($SetOrder)) {
		if($SetOrder < $NewOrder) {
			switchOrder($NewOrder, $SetOrder, $CardType, $CardNumber);
		}
	}
}

if(!empty($_FILES['UploadedBgImage']['size'])) {
	unset($img);
	switch($_FILES['UploadedBgImage']['type']) {
		case 'image/png':
			$img=imagecreatefrompng($_FILES['UploadedBgImage']['tmp_name']);
		case 'image/jpeg':
			if(!isset($img)) $img=imagecreatefromjpeg($_FILES['UploadedBgImage']['tmp_name']);
			break;
	}
	if(!empty($img)) {
		$tmpfile=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$CardFile.'-Accreditation.jpg';
		$srcW=imagesx($img);
		$srcH=imagesy($img);
		if($srcW>MAX_PHOTO_PIXEL or $srcH>MAX_PHOTO_PIXEL) {
			// max dimension is a square of 2000 pixel!
			$ratio=1;
			if($srcW>MAX_PHOTO_PIXEL) $ratio=MAX_PHOTO_PIXEL/$srcW;
			if($srcH>MAX_PHOTO_PIXEL) $ratio=min($ratio, MAX_PHOTO_PIXEL/$srcH);
			$dstW=intval($srcW*$ratio);
			$dstH=intval($srcH*$ratio);
			$im2=imagecreatetruecolor($dstW, $dstH);
			imagecopyresampled($im2, $img, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			imagejpeg($im2, $tmpfile, 85);
		} else {
			imagejpeg($img, $tmpfile, 85);
		}
		$SQL="IcTournament={$_SESSION['TourId']}, IcType='$CardType', IcNumber=$CardNumber, IcBackground=".StrSafe_DB(file_get_contents($tmpfile));
		safe_w_sql("INSERT INTO IdCards set $SQL on duplicate key update $SQL");
	}
}

$RowBn=emptyIdCard(safe_fetch($Rs));

$JS_SCRIPT=array(
	phpVars2js(array('CardType' => $CardType, 'CardNumber' => $CardNumber)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
	//'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jscolor.js"></script>',
	'<script type="text/javascript" src="./IdCardEdit.js"></script>',
	);

//$PAGE_TITLE=get_text('BackNumbers', 'BackNumbers');

include('Common/Templates/head.php');


echo '<form id="PrnParameters" method="post" action="" enctype="multipart/form-data">';
echo '<table class="Tabella">';
echo '<tr><th class="SubTitle" width="50%">' . get_text('BadgeDimention', 'BackNumbers')  . '</th>';
echo '<th class="SubTitle" width="0%">&nbsp;</th>';
echo '<th class="SubTitle" width="50%">' . get_text('BgImage', 'BackNumbers')  . '</th></tr>';

//Parametri
echo '<tr>';

//Dimensione Accredito
echo '<td width="50%" valign="top">
	<table align="center">
		<tr align="center">
			<th colspan="2">&nbsp;</th>
			<th colspan="2">'.get_text('IdCardOffsets', 'BackNumbers') . '</th>
		</tr>
		<tr align="center">
			<th>'.get_text('Width', 'BackNumbers') . '</th>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[Width]" id="IdWidth" size="3" value="' . $RowBn->Settings["Width"] . '"></td>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[OffsetX]" id="IdRepX" size="10" value="' . $RowBn->Settings["OffsetX"] . '"></td>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[PaperWidth]" id="IdPaperWidth" size="10" value="' . $RowBn->Settings["PaperWidth"] . '"></td>
		</tr>
		<tr align="center">
			<th>'.get_text('Heigh', 'BackNumbers') . '</th>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[Height]" id="IdHeight" size="3" value="' . $RowBn->Settings["Height"] . '"></td>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[OffsetY]" id="IdRepY" size="10" value="' . $RowBn->Settings["OffsetY"] . '"></td>
			<td><input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[PaperHeight]" id="IdPaperHeight" size="10" value="' . $RowBn->Settings["PaperHeight"] . '"></td>
		</tr>
	</table>';

// print the matching of the divclass
echo '<br/><table class="Tabella">';
switch($CardType) {
	case 'A':
	case 'Q':
	case 'E':
		$IsAthlete=($CardType!='A' ? '1' : '');
		$Classes=array();
		$q=safe_r_sql("select * from Classes where ClTournament={$_SESSION['TourId']} ".($IsAthlete ? 'and ClAthlete=1' : '')." order by ClViewOrder");
		while($r=safe_fetch($q)) {
			$Classes[$r->ClId]=$r->ClDescription;
		}
		$Divisions=array();
		$q=safe_r_sql("select * from Divisions where DivTournament={$_SESSION['TourId']} ".($IsAthlete ? 'and DivAthlete=1' : '')." order by DivViewOrder");
		while($r=safe_fetch($q)) {
			$Divisions[$r->DivId]=$r->DivDescription;
		}

		$Categories=array();
		$q=safe_r_sql("select * from Classes inner join Divisions on DivTournament=ClTournament and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' ".($IsAthlete ? 'and DivAthlete=1' : '')." order by DivViewOrder, ClViewOrder");
		while($r=safe_fetch($q)) {
			$Categories[$r->DivId][$r->ClId]=$r->DivId.$r->ClId;
		}

		$Matches=getModuleParameter('Accreditation', 'Matches-'.$CardType.'-'.$CardNumber, '');
		if($Matches) {
			$Matches=explode(',', $Matches);
		} else {
			$Matches=array();
		}

		echo '<tr><th colspan="'.(1+count($Classes)).'" class="Title">'.get_text('SetAccreditationMatches', 'BackNumbers').'</th></tr>';
		echo '<tr><th></th>';
		foreach($Classes as $key => $desc) {
			echo '<th onclick="toggleClass(\''.$key.'\')">'.$desc.'</th>';
		}
		echo '</tr>';

		foreach($Divisions as $Div => $desc) {
			echo '<tr>';
			echo '<th onclick="toggleDiv(\''.$Div.'\')">'.$desc.'</th>';
			foreach($Classes as $Cl => $desc) {
				if(isset($Categories[$Div][$Cl])) {
					echo '<td><input type="checkbox" onclick="toggleCategory()" class="CategorySelects ClSelect'.$Cl.' DivSelect'.$Div.'" value="'.$Categories[$Div][$Cl].'"'.(in_array($Categories[$Div][$Cl], $Matches) ? ' checked="checked"' : '').'></td>';
				} else {
					echo '<td></td>';
				}
			}
			echo '</tr>';
		}
		break;
	case 'I':
	case 'T':
		$Events=array();
		$q=safe_r_sql("select * from Events where EvTeamEvent=".($CardType=='I' ? 0 : 1)." and EvTournament='{$_SESSION['TourId']}' order by EvProgr");
		while($r=safe_fetch($q)) $Events[$r->EvCode]=$r->EvCode;

		$Matches=getModuleParameter('Accreditation', 'Matches-'.$CardType.'-'.$CardNumber, '');
		if($Matches) {
			$Matches=explode(',', $Matches);
		} else {
			$Matches=array();
		}

		echo '<tr><th colspan="'.(count($Events)).'" class="Title">'.get_text('SetAccreditationMatches', 'BackNumbers').'</th></tr>';
		echo '<tr>';
		foreach($Events as $EvCode => $desc) {
			echo '<td><input type="checkbox" onclick="toggleCategory()" class="CategorySelects" value="'.$EvCode.'"'.(in_array($EvCode, $Matches) ? ' checked="checked"' : '').'>'.$desc.'</td>';
		}
		echo '</tr>';
		break;
}

echo '</table>';

echo '</td>';

//Esempio...
echo '<td width="0%"><img id="IdCardImage" src="ImgIdCard.php?CardType='.$CardType.'&CardNumber='.$CardNumber.'"></td>';

//Sfondo
echo '<td width="50%"><br>';
if($RowBn->ImgSize>0) {
	echo '<img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$CardType.'-'.$CardNumber.'-Accreditation.jpg" style="max-height:250px; max-width:400px">';
	echo '<br/><input name="DeleteBgImage" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament') . "<br>&nbsp;<br>";
} else {
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="6000000"><input name="UploadedBgImage" type="file" size="20" /><br>&nbsp;<br>';
}
echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[IdBgX]" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgX"] . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[IdBgY]" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgY"] . '" /><br>&nbsp;<br>';
echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[IdBgH]" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgH"] . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" onchange="UpdateCardSettings()" id="IdCardsSettings[IdBgW]" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgW"] . '" /><br>&nbsp;<br>';
echo '</td>';
echo '</tr>';

echo '</table>';

echo '<table class="Tabella">';
echo '<tr><th>&nbsp;</th><th>' . get_text('Progr')  . '</th>
	<th colspan="3">' . get_text('Content', 'BackNumbers')  . '</th>
	<th nowrap="nowrap">' . get_text('PosX', 'BackNumbers') . '
		<br/>' . get_text('PosY', 'BackNumbers') . '</th>
	<th>' . get_text('Width', 'BackNumbers') . '
		<br/>' . get_text('Heigh', 'BackNumbers') . '</th>
	<th>' . get_text('CharColor', 'BackNumbers') . '
		<br/>' . get_text('BackColor', 'BackNumbers') . '</th>
	<th>' . get_text('BackCat', 'BackNumbers') . '</th>
	<th>' . get_text('CharType', 'BackNumbers') . '</th>
	<th>' . get_text('CharSize', 'BackNumbers') . '</th>
	<th>' . get_text('Alignment', 'BackNumbers') . '</th>
	</tr>';

// All the already inserted elements
$NewOrder=0;
$SQL="select * from IdCardElements where IceTournament={$_SESSION['TourId']} and IceCardType='$CardType' and IceCardNumber=$CardNumber order by IceOrder";
$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	echo getFieldPos($r);
	$NewOrder=$r->IceOrder;
}

$NewOrder++;
// Inserts a new block
$Select='<option value=""></option>';
// Comp Logo Left
if(file_exists($CFG->DOCUMENT_PATH.($im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToLeft.jpg"))) {
	$Select.='<option value="ToLeft">'.get_text('ToLeft', 'BackNumbers').'</option>';
}
// Comp Logo Right
if(file_exists($CFG->DOCUMENT_PATH.($im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToRight.jpg"))) {
	$Select.='<option value="ToRight">'.get_text('ToRight', 'BackNumbers').'</option>';
}
// Comp Logo Bottom
if(file_exists($CFG->DOCUMENT_PATH.($im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToBottom.jpg"))) {
	$Select.='<option value="ToBottom">'.get_text('ToBottom', 'BackNumbers').'</option>';
}
// Colored area
$Select.='<option value="ColoredArea">'.get_text('ColoredArea', 'BackNumbers').'</option>';
// Comp name
$Select.='<option value="CompName">'.get_text('CompName', 'BackNumbers').'</option>';
// Comp Details
$Select.='<option value="CompDetails">'.get_text('CompDetails', 'BackNumbers').'</option>';
if($CardType=='T') {
	// Team components
	$Select.='<option value="TeamComponents">'.get_text('TeamComponents').'</option>';
} else {
	// numero tessera
	$Select.='<option value="AthCode">'.get_text('AthCode', 'BackNumbers').'</option>';
	// numero tessera in barcode/Qrcode
	$Select.='<option value="AthBarCode">'.get_text('AthBarCode', 'BackNumbers').'</option>';
	// numero tessera in barcode/Qrcode
	$Select.='<option value="AthQrCode">'.get_text('AthQrCode', 'BackNumbers').'</option>';
	// Athlete
	$Select.='<option value="Athlete">'.get_text('Athlete', 'BackNumbers').'</option>';
}
// Picture
if($CardType=='A') {
	$Select.='<option value="Picture">'.get_text('Picture', 'BackNumbers').'</option>';
}
// Category
$Select.='<option value="Category">'.get_text('Category', 'BackNumbers').'</option>';
// Event
if(strstr('EIT', $CardType)) {
	$Select.='<option value="Event">'.get_text('Event', 'BackNumbers').'</option>';
	$Select.='<option value="Ranking">'.get_text('Ranking', 'BackNumbers').'</option>';
}
// Session
$Select.='<option value="Session">'.get_text('Session', 'BackNumbers').'</option>';
// Target
$Select.='<option value="Target">'.get_text('Target').'</option>';
// SessionTarget
$Select.='<option value="SessionTarget">'.get_text('SessionTarget', 'BackNumbers').'</option>';
// Club
$Select.='<option value="Club">'.get_text('Club', 'BackNumbers').'</option>';
// Flag
$Select.='<option value="Flag">'.get_text('Flag', 'BackNumbers').'</option>';
// Image
$Select.='<option value="Image">'.get_text('Image', 'BackNumbers').'</option>';
// Image
$Select.='<option value="ImageSvg">'.get_text('ImageSvg', 'BackNumbers').'</option>';
// RandomImage
$Select.='<option value="RandomImage">'.get_text('RandomImage', 'BackNumbers').'</option>';
// Line
$Select.='<option value="HLine">'.get_text('HLine', 'BackNumbers').'</option>';
// Target sequence
if($CardType=='I' or $CardType=='T') {
	$Select.='<option value="TgtSequence">'.get_text('TgtSequence', 'BackNumbers').'</option>';
}
// Diritti di accesso
if($CardType=='A') {
	$Select.='<option value="Access">'.get_text('Access', 'BackNumbers').'</option>';
	$Select .= '<option value="AccessGraphics">' . get_text('AccessGraphics', 'BackNumbers') . '</option>';

	// Diritti di pappa/transport/hotel
	$Select.='<option value="Accomodation">'.get_text('Accomodation', 'BackNumbers').'</option>';
}

// Schedule
// $Select.='<option value="Schedule">'.get_text('Schedule', 'BackNumbers').'</option>';
// regole di partecipazioni


echo '<tr><th>&nbsp;</th><th><input type="text" size="3" name="NewOrder" value="'.$NewOrder.'"></th>
	<th>&nbsp;</th>
	<td colspan="9"><select name="NewContent">' . $Select  . '</select> <input type="submit" value="'.get_text('CmdUpdate').'"></td>
	</tr>';
echo '</table>';
echo '</form>';


include('Common/Templates/tail.php');


function getFieldPos($r) {
	global $CFG, $CardType, $CardNumber, $CardFile;
	$ret='<tr icetype="'.$r->IceType.'" iceorder="'.$r->IceOrder.'">
		<th><a href="?delete='.$r->IceOrder.'&CardType='.$CardType.'&CardNumber='.$CardNumber.'" onclick="return(confirm(\''.get_text('MsgAreYouSure').'\'))"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png"></a></th>
		<th><input type="hidden" id="Content['.$r->IceOrder.'][Type]" value="'.$r->IceType.'">
			<input type="text" size="3" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Order]" value="'.$r->IceOrder.'"></th>
		<th>'.get_text($r->IceType, 'BackNumbers').'</th>';
	if($r->IceOptions) {
		$Options=unserialize($r->IceOptions);
	} else {
		$Options=array(
			'X' =>0,
			'Y' =>0,
			'W' =>0,
			'H' =>0,
			'Font'=>'arialbd',
			'Col'=>'#000000',
			'BackCol'=>'',
			'BackCat'=>'',
			'Size'=>12,
			'Just'=>0,
			);
	}
	switch($r->IceType) {
		case 'ToLeft':
			$im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToLeft.jpg";
		case 'ToRight':
			if(!isset($im)) $im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToRight.jpg";
		case 'ToBottom':
			if(!isset($im)) $im="TV/Photos/{$_SESSION['TourCodeSafe']}-ToBottom.jpg";
		case 'Picture':
			if(!isset($im)) $im="Common/Images/Photo.gif";
		case 'Flag':
			if(!isset($im)) $im='Common/Images/Flag.jpg';

			$ret.= '<td colspan="2"><img src="'.$CFG->ROOT_DIR.$im.'" height="50"></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>';
			break;

		case 'ColoredArea':
			$txt='<textarea onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Text]">'.$r->IceContent.'</textarea>';
        case 'AccessGraphics':
			if(!isset($txt)) $txt= '<img src="'.$CFG->ROOT_DIR.'Common/Images/AccessCodes.png" height="50">';
		case 'CompName':
			if(!isset($txt)) $txt= $_SESSION['TourName'];
		case 'CompDetails':
			if(!isset($txt)) $txt=$_SESSION['TourWhere'].' - '.TournamentDate2StringShort($_SESSION['TourRealWhenFrom'], $_SESSION['TourRealWhenTo']);
		case 'AthCode':
			if(!isset($txt)) $txt='Archer Code';
		case 'TeamComponents':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][TeamComponents]">
					<option value="OneLine"'   .($r->IceContent=='OneLine'   ?' selected':'').'>'.get_text('OneLine',    'BackNumbers').'</option>
					<option value="MultiLine"'  .($r->IceContent=='MultiLine'  ?' selected':'').'>'.get_text('MultiLine',   'BackNumbers').'</option>
					</select>';
			}
		case 'TgtSequence':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][TgtSequence]">
					<option value="BlackWhite"'  .($r->IceContent=='BlackWhite'  ?' selected':'').'>'.get_text('BlackWhite',   'BackNumbers').'</option>
					<option value="Coloured"'   .($r->IceContent=='Coloured'   ?' selected':'').'>'.get_text('Coloured',    'BackNumbers').'</option>
					</select>';
			}
		case 'Access':
			if(!isset($txt)) $txt='0/9*';
		case 'Session':
			if(!isset($txt)) $txt=get_text('Session');
		case 'Target':
			if(!isset($txt)) $txt=get_text('Target');
		case 'SessionTarget':
			if(!isset($txt)) $txt=get_text('SessionTarget','BackNumbers');
		case 'Event':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Event]">
					<option value="">--</option>
					<option value="EvCode"'   .($r->IceContent=='EvCode'   ?' selected':'').'>'.get_text('EvCode',    'BackNumbers').'</option>
					<option value="EvCode-EvDescr"'  .($r->IceContent=='EvCode-EvDescr'  ?' selected':'').'>'.get_text('EvCode-EvDescr',   'BackNumbers').'</option>
					<option value="EvDescr"'  .($r->IceContent=='EvDescr'  ?' selected':'').'>'.get_text('EvDescr',   'BackNumbers').'</option>
					</select>';
				if(!$r->IceOptions) $Options['BackCat']=1;
			}
		case 'Ranking':
			if(!isset($txt)) $txt=get_text('Rank');
		case 'Category':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Category]">
					<option value="">--</option>
					<option value="CatCode"'   .($r->IceContent=='CatCode'   ?' selected':'').'>'.get_text('EvCode',    'BackNumbers').'</option>
					<option value="CatCode-EvDescr"'  .($r->IceContent=='CatCode-EvDescr' ? ' selected':'').'>'.get_text('EvCode-EvDescr',   'BackNumbers').'</option>
					<option value="CatDescr"'  .($r->IceContent=='CatDescr'  ?' selected':'').'>'.get_text('EvDescr',   'BackNumbers').'</option>
					<option value="CatDescrUpper"' . ($r->IceContent == 'CatDescrUpper' ? ' selected' : '') . '>' . get_text('EvDescrUpper', 'BackNumbers') . '</option>
					</select>';
				if(!$r->IceOptions) $Options['BackCat']=1;
			}
		case 'Athlete':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Athlete]">
					<option value="">--</option>
					<option value="FamCaps"'   .($r->IceContent=='FamCaps'   ?' selected':'').'>'.get_text('FamCaps',    'BackNumbers').'</option>
					<option value="FamCaps-GAlone"'  .($r->IceContent=='FamCaps-GAlone'  ?' selected':'').'>'.get_text('FamCaps-GAlone',   'BackNumbers').'</option>
					<option value="FamCaps-GivCaps"'.($r->IceContent=='FamCaps-GivCaps'?' selected':'').'>'.get_text('FamCaps-GivCaps', 'BackNumbers').'</option>
					<option value="FamCaps-GivCamel"'.($r->IceContent=='FamCaps-GivCamel'?' selected':'').'>'.get_text('FamCaps-GivCamel', 'BackNumbers').'</option>
					<option value="FamCamel"'   .($r->IceContent=='FamCamel'   ?' selected':'').'>'.get_text('FamCamel',    'BackNumbers').'</option>
					<option value="FamCamel-GAlone"'  .($r->IceContent=='FamCamel-GAlone'  ?' selected':'').'>'.get_text('FamCamel-GAlone',   'BackNumbers').'</option>
					<option value="FamCamel-GivCamel"'.($r->IceContent=='FamCamel-GivCamel'?' selected':'').'>'.get_text('FamCamel-GivCamel', 'BackNumbers').'</option>
					<option value="GivCamel"'   .($r->IceContent=='GivCamel'   ?' selected':'').'>'.get_text('GivCamel',    'BackNumbers').'</option>
					<option value="GivCamel-FamCamel"'.($r->IceContent=='GivCamel-FamCamel'?' selected':'').'>'.get_text('GivCamel-FamCamel', 'BackNumbers').'</option>
					<option value="GivCamel-FamCaps"'.($r->IceContent=='GivCamel-FamCaps'?' selected':'').'>'.get_text('GivCamel-FamCaps', 'BackNumbers').'</option>
					<option value="GivCaps"'.($r->IceContent=='GivCaps'?' selected':'').'>'.get_text('GivCaps', 'BackNumbers').'</option>
					<option value="GivCaps-FamCaps"'.($r->IceContent=='GivCaps-FamCaps'?' selected':'').'>'.get_text('GivCaps-FamCaps', 'BackNumbers').'</option>
					<option value="GAlone-FamCaps"'  .($r->IceContent=='GAlone-FamCaps'  ?' selected':'').'>'.get_text('GAlone-FamCaps',   'BackNumbers').'</option>
					<option value="GAlone-FamCamel"'  .($r->IceContent=='GAlone-FamCamel'  ?' selected':'').'>'.get_text('GAlone-FamCamel',   'BackNumbers').'</option>
					</select>';
			}
		case 'Club':
			if(!isset($txt)) {
				$txt='<select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Club]">
					<option value="">--</option>
					<option value="NocCaps-ClubCamel"'.($r->IceContent=='NocCaps-ClubCamel'?' selected':'').'>'.get_text('NocCaps-ClubCamel','BackNumbers').'</option>
					<option value="NocCaps-ClubCaps"'.($r->IceContent=='NocCaps-ClubCaps'?' selected':'').'>'.get_text('NocCaps-ClubCaps','BackNumbers').'</option>
					<option value="NocCaps"'    .($r->IceContent=='NocCaps'    ?' selected':'').'>'.get_text('NocCaps',    'BackNumbers').'</option>
					<option value="ClubCamel"'   .($r->IceContent=='ClubCamel'   ?' selected':'').'>'.get_text('ClubCamel',   'BackNumbers').'</option>
					<option value="ClubCaps"'   .($r->IceContent=='ClubCaps'   ?' selected':'').'>'.get_text('ClubCaps',   'BackNumbers').'</option>
					</select>';
			}
			$ret.= '<td colspan="2">'.$txt.'</td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>
				<td nowrap="nowrap"><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][Col]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Col]" value="' . $Options['Col'] . '">
					<br/><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][BackCol]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCol]" value="' . $Options['BackCol'] . '"></td>
				<td><br/><input type="checkbox" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCat]"'.(empty($Options['BackCat']) ? '' : ' checked="checked"').'></td>
				<td><select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Font]">
					<option value="arial"' . ($Options['Font']=='arial' ? ' selected' : '') . '>Arial</option>
					<option value="arialbd"' . ($Options['Font']=='arialbd' ? ' selected' : '') . '>Arial Bold</option>
					<option value="ariali"' . ($Options['Font']=='ariali' ? ' selected' : '') . '>Arial Italic</option>
					<option value="arialbi"' . ($Options['Font']=='arialbi' ? ' selected' : '') . '>Arial Bold Italic</option>
					<option value="helveticaneueltpro"' . ($Options['Font'] == 'helveticaneueltpro' ? ' selected' : '') . '>Helvetica Neue LT Pro</option>
					<option value="helveticaneueltprob"' . ($Options['Font'] == 'helveticaneueltprob' ? ' selected' : '') . '>Helvetica Neue LT Pro Bold</option>
					<option value="helveticaneueltproi"' . ($Options['Font'] == 'helveticaneueltproi' ? ' selected' : '') . '>Helvetica Neue LT Pro Italic</option>
					<option value="helveticaneueltprobi"' . ($Options['Font'] == 'helveticaneueltprobi' ? ' selected' : '') . '>Helvetica Neue LT Pro Bold Italic</option>
					<option value="helveticaneueltprocn"' . ($Options['Font'] == 'helveticaneueltprocn' ? ' selected' : '') . '>Helvetica Neue LT Pro Condensed</option>
					<option value="helveticaneueltprocnb"' . ($Options['Font'] == 'helveticaneueltprocnb' ? ' selected' : '') . '>Helvetica Neue LT Pro Condensed Bold</option>
					<option value="helveticaneueltprocni"' . ($Options['Font'] == 'helveticaneueltprocni' ? ' selected' : '') . '>Helvetica Neue LT Pro Condensed Italic</option>
					<option value="helveticaneueltprocnbi"' . ($Options['Font'] == 'helveticaneueltprocnbi' ? ' selected' : '') . '>Helvetica Neue LT Pro Condensed Bold Italic</option>
					<option value="helveticacondensed"' . ($Options['Font']=='helveticacondensed' ? ' selected' : '') . '>Helvetica Condensed</option>
					<option value="helveticacondensedbold"' . ($Options['Font']=='helveticacondensedbold' ? ' selected' : '') . '>Helvetica Condensed Bold</option>
					<option value="times"' . ($Options['Font']=='times' ? ' selected' : '') . '>Times</option>
					<option value="timesbd"' . ($Options['Font']=='timesbd' ? ' selected' : '') . '>Times Bold</option>
					<option value="timesi"' . ($Options['Font']=='timesi' ? ' selected' : '') . '>Times Italic</option>
					<option value="timesbi"' . ($Options['Font']=='timesbi' ? ' selected' : '') . '>Times Bold Italic</option>
					<option value="cour"' . ($Options['Font']=='cour' ? ' selected' : '') . '>Courier</option>
					<option value="courbd"' . ($Options['Font']=='courbd' ? ' selected' : '') . '>Courier Bold</option>
					<option value="couri"' . ($Options['Font']=='couri' ? ' selected' : '') . '>Courier Italic</option>
					<option value="courbi"' . ($Options['Font']=='courbi' ? ' selected' : '') . '>Courier Bold Italic</option>
					<option value="FreeSans"' . ($Options['Font']=='FreeSans' ? ' selected' : '') . '>'.get_text('PrintCyrillic', 'Tournament').'</option>
					<option value="DroidSansFallback"' . ($Options['Font']=='DroidSansFallback' ? ' selected' : '') . '>'.get_text('PrintChinese', 'Tournament').'</option>
					<option value="arialuni"' . ($Options['Font']=='arialuni' ? ' selected' : '') . '>'.get_text('PrintJapanese', 'Tournament').'</option>
					</select></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Size]" value="' . $Options['Size'] . '"></td>
				<td><select onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Just]">
					<option value="0"' . ($Options['Just'] == 0 ? ' selected' : '') . '>' . get_text('AlignL', 'BackNumbers') . '</option>
					<option value="1"' . ($Options['Just'] == 1 ? ' selected' : '') . '>' . get_text('AlignC', 'BackNumbers') . '</option>
					<option value="2"' . ($Options['Just'] == 2 ? ' selected' : '') . '>' . get_text('AlignR', 'BackNumbers') . '</option>
					</select></td>';

			break;
		case 'HLine':
			if(!isset($txt)) $txt=get_text('HLine', 'BackNumbers');
			$ret.='<td colspan="2">'.$txt.'</td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>
				<td nowrap="nowrap"><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][Col]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Col]" value="' . $Options['Col'] . '">
					<br/><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][BackCol]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCol]" value="' . $Options['BackCol'] . '"></td>
				<td><br/><input type="checkbox" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCat]"'.(empty($Options['BackCat']) ? '' : ' checked="checked"').'></td>
				<td></td>
				<td></td>
				<td></td>';
			break;
		case 'AthBarCode':
			if(!isset($txt)) {
				$txt='<div style="float:Right;"><input type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][AthBarCode]" value="'.$r->IceContent.'" style="width:100%"><br/>'.get_text('BarCodeFields', 'BackNumbers').'</div>';
			}
			$im='Common/Images/edit-barcode.png';
		case 'AthQrCode':
			if(!isset($im)) $im='Common/Images/qrcode.jpg';
			if(!isset($txt)) {
				$txt='<div style="float:Right;"><input type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][AthQrCode]" value="'.$r->IceContent.'" style="width:100%"><br/>'.get_text('QrCodeFields', 'BackNumbers').'</div>';
			}
		case 'Accomodation':
			if(!isset($txt)) $txt="";
			if(!isset($im)) $im='Common/Images/Accomodations.png';
		case 'ImageSvg':
			if(!isset($txt)) $txt="";
			if(!isset($im)) {
				$im='';
				$imInput= '<input type="file" name="Content['.$r->IceOrder.'][ImageSvg]">';
				if(file_exists($CFG->DOCUMENT_PATH."TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".svg")) {
					$im="TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".svg";
				} elseif($r->IceContent) {
					if($r->IceContent) {
						file_put_contents($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$r->IceType.'-'.$CardFile.'-'.$r->IceOrder.'.svg', gzinflate($r->IceContent));
						$im="TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".svg";
					}
				}
			}
		case 'Image':
		case 'RandomImage':
			if(!isset($txt)) $txt="";
			if(!isset($im)) {
				$im='';
				if(file_exists($CFG->DOCUMENT_PATH."TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".jpg")) {
					$im="TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".jpg";
				} elseif($r->IceContent) {
					if($r->IceContent and $img=@imagecreatefromstring($r->IceContent)) {
						imagejpeg($img, $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-'.$r->IceType.'-'.$CardFile.'-'.$r->IceOrder.'.jpg', 90);
						$im="TV/Photos/{$_SESSION['TourCodeSafe']}-{$r->IceType}-".$CardFile.'-'.$r->IceOrder.".jpg";
					}
				}
			}

			if(empty($imInput)) {
				$imInput='';
				if(empty($txt)) {
					$imInput = '<input type="file" name="Content[' . $r->IceOrder . '][Image]"> <input type="submit" value="'.get_text('CmdUpdate').'">';
				}
			}
			$ret.= '<td>'.(empty($im) ? '' : '<img src="'.$CFG->ROOT_DIR.$im.'" height="50">').$txt.'</td>
				<td>'.$imInput.'</td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>
				<td nowrap="nowrap"><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][Col]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][Col]" value="' . $Options['Col'] . '">
					<br/><input size="6" type="text" class="jscolor {hash:true,required:false} jscolor-active" id="Content['.$r->IceOrder.'][Options][BackCol]" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCol]" value="' . $Options['BackCol'] . '"></td>
				<td><br/><input type="checkbox" onchange="UpdateRowContent(this)" id="Content['.$r->IceOrder.'][Options][BackCat]"'.(empty($Options['BackCat']) ? '' : ' checked="checked"').'></td>
				<td></td>
				<td></td>
				<td></td>';
			break;
		default:
	}
	$ret. '</tr>';

	return $ret;
}

