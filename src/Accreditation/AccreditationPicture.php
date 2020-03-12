<?php
require_once(dirname(__FILE__) . '/config.php');
checkACL(AclAccreditation, AclReadWrite);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Modules.php');

CheckTourSession(true);

$param = array("source"=>0, "minW"=>400, "minH" => 400);
if(file_exists($CFG->DOCUMENT_PATH."Modules/IanseoTeam/Accreditation/includeAccreditationPicture.php")) {
	require_once(dirname(dirname(__FILE__)) . '/Modules/IanseoTeam/Accreditation/includeAccreditationPicture.php');
}

// loads how many accreditation types there are
$Accreditations=array();
$q=safe_r_sql("select IcNumber, IcName from IdCards where IcTournament={$_SESSION['TourId']} and IcType='A' order by IcNumber");
while($r=safe_fetch($q)) $Accreditations[$r->IcNumber]=$r->IcNumber;


$PAGE_TITLE=get_text('TakePicture', 'Tournament');
$JS_SCRIPT[] = phpVars2js(array('ROOT_DIR' => $CFG->ROOT_DIR, 'AreYouSure'=>get_text('MsgAreYouSure'), 'msgPictureThere' => get_text('PictureThere', 'Tournament')));
$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/jQuery/jquery-2.1.4.min.js"></script>';
$JS_SCRIPT[] = '<script type="text/javascript" src="./Fun_AJAX_AccreditationPicture.js"></script>';
$JS_SCRIPT[] = phpVars2js($param);
if($param["source"]==0) {
	$JS_SCRIPT[] = '<script type="text/javascript" src="./TakePicture.js"></script>';
} else {
	$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/IanseoTeam/Accreditation/TakePicture.js"></script>';
}
$JS_SCRIPT[]='<style>.Reverse td {background-color:#ddd; color:black;}</style>';

$ONLOAD = ' onLoad="javascript:searchAthletes();'.($param["source"]==0 ? 'setupVideo();':'').'"';
include('Common/Templates/head' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');

?>
<table class="Tabella Speaker">
<tr onClick="showOptions();"><th class=Title colspan="4"><?php echo get_text('TakePicture', 'Tournament');?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tbody id="options">

<tr>
<th class="Title" width="50%"><?php echo get_text('Options', 'Tournament');?></th>
<th class="Title" width="50%"><?php echo get_text('FilterRules');?></th>
</tr>

<tr>
<td class="Center">

<?php
if($param["source"]==0) {
	echo get_text('Camera', 'Tournament');
	echo '<select id="videoSource"></select><br>';
}
echo '<input type="checkbox" id="showMenu" ' . (isset($_REQUEST["showMenu"]) ? 'checked' : '') .
	' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. (isset($_REQUEST["showMenu"]) ? '' : '?showMenu') . '\';">'.get_text('ShowIanseoMenu', 'Tournament');
if(file_exists($CFG->DOCUMENT_PATH."Modules/IanseoTeam/IanseoFeatures/AccreditationPictureParameters.php") and empty($_SESSION['ShortMenu']['ACCR'])) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$CFG->ROOT_DIR.'Modules/IanseoTeam/IanseoFeatures/AccreditationPictureParameters.php">'.get_text('AdvancedParams', 'Tournament'). '</a>';
}
?>
</td>
<td class="Center">
<input type="text" name="x_Search" id="x_Search" style="width: 80%;" maxlength="50" onBlur="searchAthletes();" onkeyup="searchAthletes();"><br>
<input type="checkbox" id="x_Country" name="x_Country" value="1" checked onChange="searchAthletes();"><?php echo get_text('Country') ?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="x_Athlete" name="x_Athlete" value="1" checked onChange="searchAthletes();"> <?php echo get_text('Athlete') ?><br>
<input type="checkbox" id="x_noPhoto" name="x_noPhoto" value="1" checked onChange="searchAthletes();"><?php echo get_text('OnlyWithoutPhoto','Tournament')?>
<?php

$TourId=$_SESSION['TourId'];
if($_SESSION['AccreditationTourIds']) {
	echo '<br/>';
	foreach(explode(',', $_SESSION['AccreditationTourIds']) as $id) {
		$Code=getCodeFromId($id);
		echo '<input type="checkbox" class="x_Tours" id="x_Tour['.$id.']" value="'.$Code.'" onChange="searchAthletes();">'.$Code.'&nbsp;&nbsp;&nbsp;';
	}
	$TourId=$_SESSION['AccreditationTourIds'];
}

echo '<br/>';
echo '<input type="checkbox" class="x_Sessions" id="x_Sessions[0]" onChange="searchAthletes();">'.get_text('Session').' 0&nbsp;&nbsp;&nbsp;';
$q=safe_r_sql("select distinct SesOrder from Session where SesTournament in ($TourId) and SesType='Q' order by SesOrder");
while($r=safe_fetch($q)) {
	echo '<input type="checkbox" class="x_Sessions" id="x_Sessions['.$r->SesOrder.']" onChange="searchAthletes();">'.get_text('Session').' '.$r->SesOrder.'&nbsp;&nbsp;&nbsp;';
}
echo '<input type="checkbox" class="x_NoPrint" id="x_NoPrint" onChange="searchAthletes();">No Printout';

?>
</td>
</tr>
</tbody>
</table>

<table class="Tabella Speaker">
	<tr>
		<th class="Title" width="35%"><?php echo get_text('Camera', 'Tournament');?></th>
		<th class="Title" width="35%"><?php echo get_text('Photo', 'Tournament');?></th>
		<th class="Title" width="30%"><?php echo get_text('MenuLM_Partecipant List');?> <span id="missingPhotos"></span></th>
	</tr>
<tbody id="tbody">
	<tr>
		<td style="vertical-align: top; text-align: center;">
			<input type="button" id="stop-button" value="<?php echo get_text('StopCamera', 'Tournament')?>" onClick="stopVideo();" style="display: none;">
			<input type="button" id="start-button" value="<?php echo get_text('StartCamera', 'Tournament')?>" onClick="startVideo();" style="display: none;">
			<br><input id="zoom" type="range" min="1" max="15" style="display:none;" value="1" onChange="changeZoom()"/><br>
			<div id="cameraContainer" style="position: relative;" onClick="takePicture();">
				<video id="CamVideo" crossOrigin="Anonymous" width="100%" autoplay style="position: absolute; top: 0px; left: 0px;" ></video>
 				<img id="ImgCamVideo" crossOrigin="Anonymous" width="100%" style="position: absolute; top: 0px; left: 0px; display:none;">
				<svg id="face" version="1.1" xmlns="http://www.w3.org/2000/svg" style="position: absolute; display:none; top: 0px; left: 0px; width:400px; height: 400px;" viewBox="0 0 400 400" >
					<rect width="400" height="400"  style="fill:none;stroke:orange;stroke-width:2"/>
					<rect x="50" width="300" height="400"  style="fill:none;stroke:yellow;stroke-width:2"/>
					<line x1="125" y1="172" x2="275" y2="172" style="stroke:rgb(255,0,0);stroke-width:1" />
					<line x1="200" y1="160" x2="200" y2="180" style="stroke:rgb(255,0,0);stroke-width:1" />
					<text font-size="10" fill="red" x="200" y="172" text-anchor="middle">eyes line</text>
					<line x1="145" y1="270" x2="255" y2="270" style="stroke:rgb(255,0,0);stroke-width:1" />
					<text font-size="10" fill="red" x="200" y="270" text-anchor="middle">lips line</text>
					<path d="M 50,400 L 150,355 l 0,-50 C 115,280 90,230 90,178 c 0,-75 50,-137 112,-137 c 62,0 112,60 112,137 c -0,50 -25,101 -62,124 L 250,355 L 350,400"  style="fill:none;stroke:yellow;stroke-width:2"/>
				</svg>
			</div>

		</td>
		<td style="vertical-align: top; text-align: center;">
			<input type="hidden" id="selId">
			<table class="Tabella">
				<tr><th id="selAth" style="width:40%;"></th><td id="selCat" style="width:20%;"></td><td id="selTeam" style="width:40%;"></td></tr>
			</table>
			<div id="loadingBar" class="blue LetteraGrande" style="display: none;"></div>
			<br><img id="athPic" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" width="150">
			<div id="ManBlock" style="display: none;">
			<input type="button" id="delete-button" value="<?php echo get_text('PhotoDelete', 'Tournament')?>" onClick="deletePicture();" ><br>
			<?php
			if($Accreditations) {
				if(count($Accreditations)>1) {
					echo '<select id="accreditation-number">';
					foreach($Accreditations as $k => $v) {
						echo '<option value="'.$k.'">'.$v.'</option>';
					}
					echo '</select>';
				} else {
					foreach($Accreditations as $k => $v) {
						echo '<input type="hidden" id="accreditation-number" value="'.$k.'">';
					}
				}
				echo '<input type="button" id="print-button" value="'.get_text('Print', 'Tournament').'" onClick="printAccreditation()" >';
			}

			?>

			&nbsp;&nbsp;<input type="button" id="confirm-button" value="<?php echo get_text('BadgeConfirmPrinted', 'Tournament')?>" onClick="ConfirmPrinted()" style="display: none;">
			</div>
			<canvas id="screenshot-canvas" style="display: none;"></canvas>
		</td>
		<td style="vertical-align: top;">
			<table class="Tabella" id="List">
			<thead><tr>
				<th colspan="2"><?php echo get_text('Athlete')?></th>
				<th><?php echo get_text('DivisionClass'); ?></th>
				<th><?php echo get_text('Country')?></th></tr></thead>
			<tbody id="ListBody"></tbody>
			</table>
		</td>
	</tr>
</tbody>
</table>

<?php

include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
?>