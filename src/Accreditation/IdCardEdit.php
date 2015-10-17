<?php
/*

=> IDcard can have a background
=> IDcard can have a back with schedule AND-OR a freetext
=> ALL elements are positional (negative x or y position means not printed at all)



*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('IdCardEmpty.php');
//print_r($_REQUEST);exit;

CheckTourSession(true);

if(isset($_GET['delete'])) {
	$IdOrder=intval($_GET['delete']);
	safe_w_sql("delete from IdCardElements where IceTournament={$_SESSION['TourId']} and IceOrder=$IdOrder");
	if(file_exists($File=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$IdOrder.'.jpg')) unlink($File);

	CD_redirect(basename(__FILE__));
}

$RowBn=NULL;
$Select
	= "SELECT IdCards.*, LENGTH(IcBackground) as ImgSize "
	. "FROM IdCards  "
	. "WHERE IcTournament=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($Select);

// debug_svela($_REQUEST);

if(!empty($_REQUEST['DeleteBgImage'])) {
	safe_w_sql("update IdCards set IcBackground='' where IcTournament={$_SESSION['TourId']} ");
	unlink($CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Accreditation.jpg');
}

if(!empty($_REQUEST['IdCardsSettings'])) {
	$sql="IcTournament={$_SESSION['TourId']}, IcSettings=".StrSafe_DB(serialize($_REQUEST["IdCardsSettings"]));
	safe_w_sql("INSERT INTO IdCards set $sql on duplicate key update $sql");
	$Rs=safe_r_sql($Select);
}

if(!empty($_REQUEST["NewContent"])) {
	$q=safe_r_sql("select max(IceOrder)+1 NewOrder from IdCardElements where IceTournament={$_SESSION['TourId']}");
	if($r=safe_fetch($q) and $r->NewOrder) {
		$NewOrder=$r->NewOrder;
	} else {
		$NewOrder=1;
	}
	$sql="IceTournament={$_SESSION['TourId']}, IceOrder=$NewOrder, IceType=".StrSafe_DB($_REQUEST["NewContent"]);
	safe_w_sql("INSERT INTO IdCardElements set $sql");
	$SetOrder=intval($_REQUEST["NewOrder"]);
	if(!empty($SetOrder)) {
		if($SetOrder < $NewOrder) {
			switchOrder($NewOrder, $SetOrder);
		}
	}
} elseif(!empty($_REQUEST["Content"])) {
	// ATTENTION!!!!
	// CANNOT MIX NEW CONTENT AND UPDATING OLD THINGS!!!
	foreach($_REQUEST["Content"] as $IdOrder => $Options) {
		$SQL=array();
		if(!empty($Options['File'])) {
			$SQL[]='IceMimeType='.StrSafe_DB($Options['File']);
		}
		if(!empty($Options['Text'])) {
			$SQL[]='IceContent='.StrSafe_DB($Options['Text']);
		}
		if(!empty($Options['Athlete'])) {
			$SQL[]='IceContent='.StrSafe_DB($Options['Athlete']);
		}
		if(!empty($Options['Club'])) {
			$SQL[]='IceContent='.StrSafe_DB($Options['Club']);
		}
		if(!empty($Options['Options'])) {
			if($Options['Type']=='Picture') {
				if($Options['Options']['W'] or $Options['Options']['H']) {
					if(empty($Options['Options']['W'])) {
						$Options['Options']['W']=intval($Options['Options']['H']*MAX_WIDTH/MAX_HEIGHT);
					} else {
						$Options['Options']['H']=intval($Options['Options']['W']*MAX_HEIGHT/MAX_WIDTH);
					}
				} else {
					$Options['Options']['W']=30;
					$Options['Options']['H']=40;
				}
			}
			$SQL[]='IceOptions='.StrSafe_DB(serialize($Options['Options']));
		}
		if(!empty($_FILES['Content']['size'][$IdOrder]['Image'])) {
			unset($img);
			switch($_FILES['Content']['type'][$IdOrder]['Image']) {
				case 'image/png':
					$img=imagecreatefrompng($_FILES['Content']['tmp_name'][$IdOrder]['Image']);
				case 'image/jpeg':
					if(!isset($img)) $img=imagecreatefromjpeg($_FILES['Content']['tmp_name'][$IdOrder]['Image']);
					break;
			}
			if(!empty($img)) {
				$tmpfile=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$IdOrder.'.jpg';
				if($_FILES['Content']['size'][$IdOrder]['Image']>250000) {
					// max dimension is a square of 640 pixel!
					$ratio=1;
					$srcW=imagesx($img);
					$srcH=imagesy($img);
					if($srcW>640) $ratio=640/$srcW;
					if($srcH>640) $ratio=min($ratio, 640/$srcH);
					$dstW=intval($srcW*$ratio);
					$dstH=intval($srcH*$ratio);
					$im2=imagecreatetruecolor($dstW, $dstH);
					imagecopyresampled($im2, $img, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
					imagejpeg($im2, $tmpfile, 85);
				} else {
					imagejpeg($img, $tmpfile, 85);
				}
				$SQL[]='IceContent='.StrSafe_DB(file_get_contents($tmpfile));
			}
// 			debug_svela($_FILES);
		}
		safe_w_sql("update IdCardElements set ".implode(',', $SQL)." where IceTournament={$_SESSION['TourId']} and IceOrder=$IdOrder");

		if(!empty($Options['Order'])) {
			switchOrder($IdOrder, intval($Options['Order']));
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
		$tmpfile=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Accreditation.jpg';
		if($_FILES['UploadedBgImage']['size']>640000) {
			// max dimension is a square of 640 pixel!
			$ratio=1;
			$srcW=imagesx($img);
			$srcH=imagesy($img);
			if($srcW>640) $ratio=1024/$srcW;
			if($srcH>640) $ratio=min($ratio, 1024/$srcH);
			$dstW=intval($srcW*$ratio);
			$dstH=intval($srcH*$ratio);
			$im2=imagecreatetruecolor($dstW, $dstH);
			imagecopyresampled($im2, $img, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			imagejpeg($im2, $tmpfile, 85);
		} else {
			imagejpeg($img, $tmpfile, 85);
		}
		$SQL='IcTournament='.$_SESSION['TourId'].', IcBackground='.StrSafe_DB(file_get_contents($tmpfile));
		safe_w_sql("INSERT INTO IdCards set $SQL on duplicate key update $SQL");
	}
}

$RowBn=emptyIdCard(safe_fetch($Rs));

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/Fun_JS.inc.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Accreditation/Fun_AJAX_IdCard.js.php"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ColorPicker/302pop.js"></script>',
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
			<th>'.get_text('IdCardOffsets', 'BackNumbers') . '</th>
		</tr>
		<tr align="center">
			<th>'.get_text('Width', 'BackNumbers') . '</th>
			<td><input type="text" name="IdCardsSettings[Width]" id="IdWidth" size="3" value="' . $RowBn->Settings["Width"] . '"></td>
			<td><input type="text" name="IdCardsSettings[OffsetX]" id="IdRepX" size="10" value="' . $RowBn->Settings["OffsetX"] . '"></td>
			<td><input type="text" name="IdCardsSettings[PaperWidth]" id="IdPaperWidth" size="10" value="' . $RowBn->Settings["PaperWidth"] . '"></td>
		</tr>
		<tr align="center">
			<th>'.get_text('Heigh', 'BackNumbers') . '</th>
			<td><input type="text" name="IdCardsSettings[Height]" id="IdHeight" size="3" value="' . $RowBn->Settings["Height"] . '"></td>
			<td><input type="text" name="IdCardsSettings[OffsetY]" id="IdRepY" size="10" value="' . $RowBn->Settings["OffsetY"] . '"></td>
			<td><input type="text" name="IdCardsSettings[PaperHeight]" id="IdPaperHeight" size="10" value="' . $RowBn->Settings["PaperHeight"] . '"></td>
		</tr>';

echo '</table></td>';

//Esempio...
echo '<td width="0%">';
echo '<img src="ImgIdCard.php"></td>';

//Sfondo
echo '<td width="50%"><br>';
if($RowBn->ImgSize>0) {
	echo '<img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Accreditation.jpg" style="max-height:250px; max-width:400px">';
	echo '<br/><input name="DeleteBgImage" type="checkbox" value="1"/>&nbsp;' . get_text('CmdDelete','Tournament') . "<br>&nbsp;<br>";
} else {
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><input name="UploadedBgImage" type="file" size="20" /><br>&nbsp;<br>';
}
echo  get_text('PosX', 'BackNumbers') . '&nbsp;<input type="text" name="IdCardsSettings[IdBgX]" id="BnBgX" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgX"] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo  get_text('PosY', 'BackNumbers') . '&nbsp;<input type="text" name="IdCardsSettings[IdBgY]" id="BnBgY" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgY"] . '"><br>&nbsp;<br>';
echo  get_text('Heigh', 'BackNumbers') . '&nbsp;<input type="text" name="IdCardsSettings[IdBgH]" id="BnBgH" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgH"] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo  get_text('Width', 'BackNumbers') . '&nbsp;<input type="text" name="IdCardsSettings[IdBgW]" id="BnBgW" size="7" maxlength="5" value="' . $RowBn->Settings["IdBgW"] . '"><br>&nbsp;<br>';
echo '</td>';
echo '</tr>';

echo '</table>';

echo '<table class="Tabella">';



echo '<tr><th>&nbsp;</th><th>' . get_text('Progr')  . '</th>
	<th colspan="2">' . get_text('Content', 'BackNumbers')  . '</th>
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
$SQL="select * from IdCardElements where IceTournament={$_SESSION['TourId']} order by IceOrder";
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
// numero tessera
$Select.='<option value="AthCode">'.get_text('AthCode', 'BackNumbers').'</option>';
// numero tessera in barcode/Qrcode
$Select.='<option value="AthBarCode">'.get_text('AthBarCode', 'BackNumbers').'</option>';
// numero tessera in barcode/Qrcode
$Select.='<option value="AthQrCode">'.get_text('AthQrCode', 'BackNumbers').'</option>';
// Athlete
$Select.='<option value="Athlete">'.get_text('Athlete', 'BackNumbers').'</option>';
// Picture
$Select.='<option value="Picture">'.get_text('Picture', 'BackNumbers').'</option>';
// Category
$Select.='<option value="Category">'.get_text('Category', 'BackNumbers').'</option>';
// Session
$Select.='<option value="Session">'.get_text('Session', 'BackNumbers').'</option>';
// Club
$Select.='<option value="Club">'.get_text('Club', 'BackNumbers').'</option>';
// Flag
$Select.='<option value="Flag">'.get_text('Flag', 'BackNumbers').'</option>';
// Image
$Select.='<option value="Image">'.get_text('Image', 'BackNumbers').'</option>';
// Diritti di accesso
$Select.='<option value="Access">'.get_text('Access', 'BackNumbers').'</option>';
// Diritti di pappa/transport/hotel
$Select.='<option value="Accomodation">'.get_text('Accomodation', 'BackNumbers').'</option>';
// Schedule
// $Select.='<option value="Schedule">'.get_text('Schedule', 'BackNumbers').'</option>';
// regole di partecipazioni


echo '<tr><th>&nbsp;</th><th><input type="text" size="3" name="NewOrder" value="'.$NewOrder.'"></th>
	<th colspan="2"><select name="NewContent">' . $Select  . '</select></th>
	<td colspan="5"><input type="submit" value="'.get_text('CmdUpdate').'"></td>
	</tr>';
echo '</table>';
echo '</form>';


include('Common/Templates/tail.php');


function getFieldPos($r) {
	global $CFG;
	$ret='<tr>
		<th><a href="?delete='.$r->IceOrder.'" onclick="return(confirm(\''.get_text('MsgAreYouSure').'\'))"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png"></a></th>
		<th><input type="hidden" name="Content['.$r->IceOrder.'][Type]" value="'.$r->IceType.'">
			<input type="text" size="3" name="Content['.$r->IceOrder.'][Order]" value="'.$r->IceOrder.'"></th>
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
			if(!isset($im)) $im="Common/Images/Photo.jpg";
		case 'Flag':
			if(!isset($im)) $im='Common/Images/Flag.jpg';

			$ret.= '<td><img src="'.$CFG->ROOT_DIR.$im.'" height="50"></td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>';
			break;

		case 'ColoredArea':
			$txt='<textarea name="Content['.$r->IceOrder.'][Text]">'.$r->IceContent.'</textarea>';
		case 'CompName':
			if(!isset($txt)) $txt= $_SESSION['TourName'];
		case 'CompDetails':
			if(!isset($txt)) $txt=$_SESSION['TourWhere'].' - '.TournamentDate2StringShort($_SESSION['TourWhenFrom'], $_SESSION['TourWhenTo']);
		case 'AthCode':
			if(!isset($txt)) $txt='Archer Code';
		case 'Access':
			if(!isset($txt)) $txt='0/9*';
		case 'Session':
			if(!isset($txt)) $txt=get_text('Session');
		case 'Category':
			if(!isset($txt)) {
				$txt=get_text('Category', 'BackNumbers');
				if(!$r->IceOptions) $Options['BackCat']=1;
			}
		case 'Athlete':
			if(!isset($txt)) {
				$txt='<select name="Content['.$r->IceOrder.'][Athlete]">
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
				$txt='<select name="Content['.$r->IceOrder.'][Club]">
					<option value="">--</option>
					<option value="NocCaps-ClubCamel"'.($r->IceContent=='NocCaps-ClubCamel'?' selected':'').'>'.get_text('NocCaps-ClubCamel','BackNumbers').'</option>
					<option value="NocCaps-ClubCaps"'.($r->IceContent=='NocCaps-ClubCaps'?' selected':'').'>'.get_text('NocCaps-ClubCaps','BackNumbers').'</option>
					<option value="NocCaps"'    .($r->IceContent=='NocCaps'    ?' selected':'').'>'.get_text('NocCaps',    'BackNumbers').'</option>
					<option value="ClubCamel"'   .($r->IceContent=='ClubCamel'   ?' selected':'').'>'.get_text('ClubCamel',   'BackNumbers').'</option>
					<option value="ClubCaps"'   .($r->IceContent=='ClubCaps'   ?' selected':'').'>'.get_text('ClubCaps',   'BackNumbers').'</option>
					</select>';
			}
			$ret.= '<td>'.$txt.'</td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>
				<td nowrap="nowrap"><input size="6" type="text" id="Content['.$r->IceOrder.'][Options][Col]" name="Content['.$r->IceOrder.'][Options][Col]" value="' . $Options['Col'] . '">'
					.'&nbsp;<input type="text" id="Ex_'.$r->IceOrder.'_Col" size="1" style="background-color:' . $Options['Col'] . '" readonly>'
					.'&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'Content['.$r->IceOrder.'][Options][Col]\',\'Ex_'.$r->IceOrder.'_Col\');">
					<br/><input size="6" type="text" id="Content['.$r->IceOrder.'][Options][BackCol]" name="Content['.$r->IceOrder.'][Options][BackCol]" value="' . $Options['BackCol'] . '">'
					.'&nbsp;<input type="text" id="Ex_'.$r->IceOrder.'_BackCol" size="1" style="background-color:' . $Options['BackCol'] . '" readonly>'
					.'&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'Content['.$r->IceOrder.'][Options][BackCol]\',\'Ex_'.$r->IceOrder.'_BackCol\');"></td>
				<td><br/><input type="checkbox" name="Content['.$r->IceOrder.'][Options][BackCat]"'.(empty($Options['BackCat']) ? '' : ' checked="checked"').'></td>
				<td><select name="Content['.$r->IceOrder.'][Options][Font]">
					<option value="arial"' . ($Options['Font']=='arial' ? ' selected' : '') . '>Arial</option>
					<option value="arialbd"' . ($Options['Font']=='arialbd' ? ' selected' : '') . '>Arial Bold</option>
					<option value="ariali"' . ($Options['Font']=='ariali' ? ' selected' : '') . '>Arial Italic</option>
					<option value="arialbi"' . ($Options['Font']=='arialbi' ? ' selected' : '') . '>Arial Bold Italic</option>
					<option value="times"' . ($Options['Font']=='times' ? ' selected' : '') . '>Times</option>
					<option value="timesbd"' . ($Options['Font']=='timesbd' ? ' selected' : '') . '>Times Bold</option>
					<option value="timesi"' . ($Options['Font']=='timesi' ? ' selected' : '') . '>Times Italic</option>
					<option value="timesbi"' . ($Options['Font']=='timesbi' ? ' selected' : '') . '>Times Bold Italic</option>
					<option value="cour"' . ($Options['Font']=='cour' ? ' selected' : '') . '>Courier</option>
					<option value="courbd"' . ($Options['Font']=='courbd' ? ' selected' : '') . '>Courier Bold</option>
					<option value="couri"' . ($Options['Font']=='couri' ? ' selected' : '') . '>Courier Italic</option>
					<option value="courbi"' . ($Options['Font']=='courbi' ? ' selected' : '') . '>Courier Bold Italic</option>
					</select></td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][Size]" value="' . $Options['Size'] . '"></td>
				<td><select name="Content['.$r->IceOrder.'][Options][Just]">
					<option value="0"' . ($Options['Just'] == 0 ? ' selected' : '') . '>' . get_text('AlignL', 'BackNumbers') . '</option>
					<option value="1"' . ($Options['Just'] == 1 ? ' selected' : '') . '>' . get_text('AlignC', 'BackNumbers') . '</option>
					<option value="2"' . ($Options['Just'] == 2 ? ' selected' : '') . '>' . get_text('AlignR', 'BackNumbers') . '</option>
					</select></td>';

			break;
		case 'AthBarCode':
			$im='Common/Images/edit-barcode.png';
		case 'AthQrCode':
			if(!isset($im)) $im='Common/Images/qrcode.jpg';
		case 'Accomodation':
			if(!isset($im)) $im='Common/Images/Accomodations.png';
		case 'Image':
			if(!isset($im)) {
				$im='';
				if(file_exists($CFG->DOCUMENT_PATH."TV/Photos/{$_SESSION['TourCodeSafe']}-Image-".$r->IceOrder.".jpg")) {
					$im="TV/Photos/{$_SESSION['TourCodeSafe']}-Image-".$r->IceOrder.".jpg";
				} elseif($r->IceContent) {
					if($r->IceContent and $img=@imagecreatefromstring($r->IceContent)) {
						imagejpeg($img, $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Image-'.$r->IceOrder.'.jpg', 90);
						$im="TV/Photos/{$_SESSION['TourCodeSafe']}-Image-".$r->IceOrder.".jpg";
					}
				}
			}

			$ret.= '<td>'.(empty($im)? '<input type="file" name="Content['.$r->IceOrder.'][Image]">' : '<img src="'.$CFG->ROOT_DIR.$im.'" height="50">').'</td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][X]" value="'.$Options['X'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][Y]" value="'.$Options['Y'].'"></td>
				<td><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][W]" value="'.$Options['W'].'">
					<br/><input size="3" type="text" name="Content['.$r->IceOrder.'][Options][H]" value="'.$Options['H'].'"></td>
				<td nowrap="nowrap"><input size="6" type="text" id="Content['.$r->IceOrder.'][Options][Col]" name="Content['.$r->IceOrder.'][Options][Col]" value="' . $Options['Col'] . '">'
					.'&nbsp;<input type="text" id="Ex_'.$r->IceOrder.'_Col" size="1" style="background-color:' . $Options['Col'] . '" readonly>'
					.'&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'Content['.$r->IceOrder.'][Options][Col]\',\'Ex_'.$r->IceOrder.'_Col\');">
					<br/><input size="6" type="text" id="Content['.$r->IceOrder.'][Options][BackCol]" name="Content['.$r->IceOrder.'][Options][BackCol]" value="' . $Options['BackCol'] . '">'
					.'&nbsp;<input type="text" id="Ex_'.$r->IceOrder.'_BackCol" size="1" style="background-color:' . $Options['BackCol'] . '" readonly>'
					.'&nbsp;<img src="../Common/Images/sel.gif" onclick="javascript:pickerPopup302(\'Content['.$r->IceOrder.'][Options][BackCol]\',\'Ex_'.$r->IceOrder.'_BackCol\');"></td>
				<td><br/><input type="checkbox" name="Content['.$r->IceOrder.'][Options][BackCat]"'.(empty($Options['BackCat']) ? '' : ' checked="checked"').'></td>
				<td></td>
				<td></td>
				<td></td>';
			break;
		default:
			debug_svela($r);
	}
	$ret. '</tr>';

	return $ret;
}

function switchOrder($Old, $New) {
	if($New==$Old or !$New) return;
	$min=min($New, $Old);
	$max=max($New, $Old);
	safe_w_sql("update IdCardElements set IceNewOrder=IceOrder where IceTournament={$_SESSION['TourId']}");
	if($New<$Old) {
		safe_w_sql("update IdCardElements set IceNewOrder=IceOrder+1 where IceTournament={$_SESSION['TourId']} and IceOrder between $min and $max");
	} else {
		safe_w_sql("update IdCardElements set IceNewOrder=IceOrder-1 where IceTournament={$_SESSION['TourId']} and IceOrder between $min and $max");
	}
	safe_w_sql("update IdCardElements set IceNewOrder=$New where IceTournament={$_SESSION['TourId']} and IceOrder=$Old");
	safe_w_sql("update IdCardElements set IceOrder=IceNewOrder where IceTournament={$_SESSION['TourId']}");
}