<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);

$PAGE_TITLE=get_text('GetCredentials','Tournament');
$JS_SCRIPT = array(
		phpVars2js(array(
			'IanseoRequestCodeURI' => $CFG->IanseoServer.'CodeRequest.php',
			'ErrCodeExists' => get_text('ErrCodeExists', 'Errors'),
			'ErrCommitteeError' => get_text('ErrCommitteeError', 'Errors'),
			'ErrEmptyFields' => get_text('ErrEmptyFields', 'Errors'),
			'ErrGenericError' => get_text('ErrGenericError', 'Errors'),
			'ErrInvalidCode' => get_text('ErrInvalidCode', 'Errors'),
			'ErrNoCode' => get_text('ErrNoCode', 'Errors'),
			'ErrNoEndDate' => get_text('ErrNoEndDate', 'Errors'),
			'ErrNoError' => get_text('ErrNoError', 'Errors'),
			'ErrNoLocation' => get_text('ErrNoLocation', 'Errors'),
			'ErrNoName' => get_text('ErrNoName', 'Errors'),
			'ErrNoNation' => get_text('ErrNoNation', 'Errors'),
			'ErrNoNewId' => get_text('ErrNoNewId', 'Errors'),
			'ErrNoStartDate' => get_text('ErrNoStartDate', 'Errors'),
			'ErrRedCard' => get_text('ErrRedCard', 'Errors'),
			'ErrUnknownNation' => get_text('ErrUnknownNation', 'Errors'),
			'ErrYellowCard' => get_text('ErrYellowCard', 'Errors'),
		)),
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_CodeRequest.js"></script>',
);

include('Common/Templates/head.php');

?>
<div align="center">
	<form name="Frm" method="POST" action="">
	<input type="hidden" name="Command" value="OK">
		<table class="Tabella" style="width:50%;">
<?php
if(!empty($ErrorMessage)) {
	echo '<tr class="error"><td colspan="2" align="center" style="padding:5px; font-size:200%; color:red; ">'.$ErrorMessage.'</td></tr>' ;
}

$Select = "SELECT ToCode,ToName,ToCommitee,ToComDescr,ToWhere,ToWhenFrom,ToWhenTo,ToCountry 
    FROM Tournament
    WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);

$Rs=safe_r_sql($Select);
$MyRow=safe_fetch($Rs);

	//$mail=array(get_text('RequestHeader','Tournament'));
	//$mail[]='--------------------';
	//$mail[]="Code: " . $MyRow->ToCode;
	//$mail[]='Name: ' . $MyRow->ToName;
	//$mail[]='ComCode: ' . $MyRow->ToCommitee;
	//$mail[]='ComName: ' . $MyRow->ToComDescr;
	//$mail[]='Where: ' . $MyRow->ToWhere;
	//$mail[]='From: ' . $MyRow->ToWhenFrom ;
	//$mail[]='To: ' . $MyRow->ToWhenTo;
	//$mail[]='Password: ' ;
	//$mail[]='--------------------';
	//$mail[]=get_text('RequestDisclaimer','Tournament');
    //
	//$mailBody=rawurlencode(implode("\n", $mail));

echo '<tr><th colspan="2">'.get_text('GetCredentials','Tournament').'</th></tr>
    <tr><td colspan="2">'.get_text('GetCredentialsExplained','Tournament') . '<br/>' . nl2br(get_text('RequestDisclaimer','Tournament')).'</td></tr>
    <tr><td class="Right Bold">'.get_text('TourCode','Tournament').'</td><td id="ToCode">'.$MyRow->ToCode.'</td></tr>
    <tr><td class="Right Bold">'.get_text('TourName','Tournament').'</td><td id="ToName">'.$MyRow->ToName.'</td></tr>
    <tr><td class="Right Bold">'.get_text('TourCommitee','Tournament').'</td><td id="ToCommitee">'.$MyRow->ToCommitee.'</td></tr>
    <tr><td class="Right Bold">'.get_text('TourCommitee','Tournament').'</td><td id="ToComDescr">'.$MyRow->ToComDescr.'</td></tr>
    <tr><td class="Right Bold">'.get_text('TourWhere','Tournament').'</td><td id="ToWhere">'.$MyRow->ToWhere.'</td></tr>
    <tr><td class="Right Bold">'.get_text('From','Tournament').'</td><td id="ToWhenFrom">'.$MyRow->ToWhenFrom.'</td></tr>
    <tr><td class="Right Bold">'.get_text('To','Tournament').'</td><td id="ToWhenTo">'.$MyRow->ToWhenTo.'</td></tr>';

$Countries=get_Countries();
echo '<tr><td class="Right Bold">'.get_text('Nation').'</td><td style="background-color:pink">';
if($MyRow->ToCountry && !empty($Countries[$MyRow->ToCountry])) {
    echo '<input type="hidden" name="ToNation" id="ToNation" value="'.$MyRow->ToCountry.'">'.$MyRow->ToCountry.'-'.$Countries[$MyRow->ToCountry].'';
} else {
    echo '<select name="ToNation" id="ToNation" onchange="UpdateNation(this)">
        <option value="">--</option>';
    foreach($Countries as $NOC => $Desc) {
        echo '<option value="'.$NOC.'" '.($NOC==$MyRow->ToCountry ? 'selected="selected"' : '').'>'.$NOC.'-'.$Desc.'</option>';
    }
    echo '</select>';
}
echo '</td></tr>';

echo '<tr><td class="Right Bold">'.get_text('RequestAuthCode','Tournament').'</td><td style="background-color:pink"><input type="text" name="Password" id="Password"></td></tr>
    <tr><td class="Right Bold">'.get_text('RequestEmail','Tournament').'</td><td style="background-color:pink"><input type="email" name="Email" id="Email"></td></tr>
    <tr><td class="Right Bold">'.get_text('GoogleMap','Tournament', 'http://maps.google.com/maps?q='.urlencode($MyRow->ToWhere).'&z=10').'</td><td><input type="text" name="GoogleMap" id="GoogleMap"> <a id="GpCheck" href="http://maps.google.com/maps?q='.urlencode($MyRow->ToWhere).'&z=10" target="GoogleMap">CHECK</a></td></tr>';


?>
			<tr><td colspan="2" class="Center"><input type="button" onclick="RequestCode()" value="<?php print get_text('ClickToRequestCode','Tournament'); ?>"></td></tr>
		</table>
	</form>
</div>
<?php
include('Common/Templates/tail.php');
