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
		'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
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

	$Select
		= "SELECT ToCode,ToName,ToCommitee,ToComDescr,ToWhere,ToWhenFrom,ToWhenTo "
		. "FROM Tournament  "
		. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

	$Rs=safe_r_sql($Select);
	$MyRow=safe_fetch($Rs);

	$mail=array(get_text('RequestHeader','Tournament'));
	$mail[]='--------------------';
	$mail[]="Code: " . $MyRow->ToCode;
	$mail[]='Name: ' . $MyRow->ToName;
	$mail[]='ComCode: ' . $MyRow->ToCommitee;
	$mail[]='ComName: ' . $MyRow->ToComDescr;
	$mail[]='Where: ' . $MyRow->ToWhere;
	$mail[]='From: ' . $MyRow->ToWhenFrom ;
	$mail[]='To: ' . $MyRow->ToWhenTo;
	$mail[]='Password: ' ;
	$mail[]='--------------------';
	$mail[]=get_text('RequestDisclaimer','Tournament');

	$mailBody=rawurlencode(implode("\n", $mail));
?>
			<tr><th colspan="2"><?php print get_text('GetCredentials','Tournament'); ?></th></tr>
			<tr><td colspan="2"><?php print get_text('GetCredentialsExplained','Tournament') . '<br/>' . nl2br(get_text('RequestDisclaimer','Tournament')); ?></td></tr>

<?php

echo '<tr><td class="Right Bold">'.get_text('TourCode','Tournament').'</td><td id="ToCode">'.$MyRow->ToCode.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('TourName','Tournament').'</td><td id="ToName">'.$MyRow->ToName.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('TourCommitee','Tournament').'</td><td id="ToCommitee">'.$MyRow->ToCommitee.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('TourCommitee','Tournament').'</td><td id="ToComDescr">'.$MyRow->ToComDescr.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('TourWhere','Tournament').'</td><td id="ToWhere">'.$MyRow->ToWhere.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('From','Tournament').'</td><td id="ToWhenFrom">'.$MyRow->ToWhenFrom.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('To','Tournament').'</td><td id="ToWhenTo">'.$MyRow->ToWhenTo.'</td></tr>';
echo '<tr><td class="Right Bold">'.get_text('Nation').'</td><td style="background-color:pink"><select name="ToNation" id="ToNation">';
	echo '<option value="">--</option>';
	foreach(get_flags() as $NOC => $Desc) {
		echo '<option value="'.$NOC.'">'.$NOC.'-'.$Desc.'</option>';
	}
	echo '</select></td></tr>';

echo '<tr><td class="Right Bold">'.get_text('RequestAuthCode','Tournament').'</td><td style="background-color:pink"><input type="text" name="Password" id="Password"></td></tr>';
echo '<tr><td class="Right Bold">'.get_text('RequestEmail','Tournament').'</td><td style="background-color:pink"><input type="email" name="Email" id="Email"></td></tr>';
echo '<tr><td class="Right Bold">'.get_text('GoogleMap','Tournament', 'http://maps.google.com/maps?q='.urlencode($MyRow->ToWhere).'&z=10').'</td><td><input type="text" name="GoogleMap" id="GoogleMap"> <a id="GpCheck" href="http://maps.google.com/maps?q='.urlencode($MyRow->ToWhere).'&z=10" target="GoogleMap">CHECK</a></td></tr>';
?>
			<tr><td colspan="2" class="Center"><input type="button" onclick="RequestCode()" value="<?php print get_text('ClickToRequestCode','Tournament'); ?>"></td></tr>
		</table>
	</form>
</div>
<?php
include('Common/Templates/tail.php');

function get_flags() {
	$Flags=array(
			'AFG'=>'Afghanistan',
			'AHO'=>'Netherlands Antilles',
			'ALB'=>'Albania',
			'ALG'=>'Algeria',
			'AND'=>'Andorra',
			'ANG'=>'Angola',
			'ANT'=>'Antigua and Barbuda',
			'ARG'=>'Argentina',
			'ARM'=>'Armenia',
			'ARU'=>'Aruba',
			'ASA'=>'American Samoa',
			'AUS'=>'Australia',
			'AUT'=>'Austria',
			'AZE'=>'Azerbaidjan',
			'BAH'=>'Bahamas',
			'BAN'=>'Bangladesh',
			'BAR'=>'Barbados',
			'BDI'=>'Burundi',
			'BEL'=>'Belgium',
			'BEN'=>'Benin',
			'BER'=>'Bermuda',
			'BHU'=>'Bhutan',
			'BIH'=>'Bosnia and Herzegovina',
			'BIZ'=>'Belize',
			'BLR'=>'Belarus',
			'BOL'=>'Bolivia',
			'BOT'=>'Botswana',
			'BRA'=>'Brazil',
			'BRN'=>'Bahrain',
			'BRU'=>'Brunei',
			'BUL'=>'Bulgaria',
			'BUR'=>'Burkina Faso',
			'CAF'=>'Central African Republic',
			'CAM'=>'Cambodia',
			'CAN'=>'Canada',
			'CAY'=>'Cayman Islands',
			'CGO'=>'Congo',
			'CHA'=>'Chad',
			'CHI'=>'Chile',
			'CHN'=>'China',
			'CIV'=>'Côte d\'Ivoire',
			'CMR'=>'Cameroon',
			'COD'=>'DR Congo',
			'COK'=>'Cook Islands',
			'COL'=>'Colombia',
			'COM'=>'Comoros',
			'CPV'=>'Cape Verde',
			'CRC'=>'Costa Rica',
			'CRO'=>'Croatia',
			'CUB'=>'Cuba',
			'CYP'=>'Cyprus',
			'CZE'=>'Czech Republic',
			'DEN'=>'Denmark',
			'DJI'=>'Djibouti',
			'DMA'=>'Dominica',
			'DOM'=>'Dominican Republic',
			'ECU'=>'Ecuador',
			'EGY'=>'Egypt',
			'ERI'=>'Eritrea',
			'ESA'=>'El Salvador',
			'ESP'=>'Spain',
			'EST'=>'Estonia',
			'ETH'=>'Ethiopia',
			'FIJ'=>'Fiji',
			'FIN'=>'Finland',
			'FRA'=>'France',
			'FSM'=>'Micronesia',
			'GAB'=>'Gabon',
			'GAM'=>'Gambia',
			'GBR'=>'Great Britain',
			'GBS'=>'Guinea-Bissau',
			'GEO'=>'Georgia',
			'GEQ'=>'Equatorial Guinea',
			'GER'=>'Germany',
			'GHA'=>'Ghana',
			'GRE'=>'Greece',
			'GRN'=>'Grenada',
			'GUA'=>'Guatemala',
			'GUI'=>'Guinea',
			'GUM'=>'Guam',
			'GUY'=>'Guyana',
			'HAI'=>'Haiti',
			'HKG'=>'Hong Kong',
			'HON'=>'Honduras',
			'HUN'=>'Hungary',
			'INA'=>'Indonesia',
			'IND'=>'India',
			'IRI'=>'Iran',
			'IRL'=>'Ireland',
			'IRQ'=>'Iraq',
			'ISL'=>'Iceland',
			'ISR'=>'Israel',
			'ISV'=>'Virgin Islands',
			'ITA'=>'Italy',
			'IVB'=>'British Virgin Islands',
			'JAM'=>'Jamaica',
			'JOR'=>'Jordan',
			'JPN'=>'Japan',
			'KAZ'=>'Kazakhstan',
			'KEN'=>'Kenya',
			'KGZ'=>'Kyrgyzstan',
			'KIR'=>'Kiribati',
			'KOR'=>'South Korea',
			'KSA'=>'Saudi Arabia',
			'KUW'=>'Kuwait',
			'LAO'=>'Laos',
			'LAT'=>'Latvia',
			'LBA'=>'Libya',
			'LBR'=>'Liberia',
			'LCA'=>'Saint Lucia',
			'LES'=>'Lesotho',
			'LIB'=>'Lebanon',
			'LIE'=>'Liechtenstein',
			'LTU'=>'Lithuania',
			'LUX'=>'Luxembourg',
			'MAC'=>'Macao',
			'MAD'=>'Madagascar',
			'MAR'=>'Morocco',
			'MAS'=>'Malaysia',
			'MAW'=>'Malawi',
			'MDA'=>'Moldova',
			'MDV'=>'Maldives',
			'MEX'=>'Mexico',
			'MGL'=>'Mongolia',
			'MHL'=>'Marshall Islands',
			'MKD'=>'Macedonia',
			'MLI'=>'Mali',
			'MLT'=>'Malta',
			'MNE'=>'Montenegro',
			'MON'=>'Monaco',
			'MOZ'=>'Mozambique',
			'MRI'=>'Mauritius',
			'MTN'=>'Mauritania',
			'MYA'=>'Myanmar',
			'NAM'=>'Namibia',
			'NCA'=>'Nicaragua',
			'NED'=>'Netherlands',
			'NEP'=>'Nepal',
			'NGR'=>'Nigeria',
			'NIG'=>'Niger',
			'NOR'=>'Norway',
			'NRU'=>'Nauru',
			'NZL'=>'New Zealand',
			'OMA'=>'Oman',
			'PAK'=>'Pakistan',
			'PAN'=>'Panama',
			'PAR'=>'Paraguay',
			'PER'=>'Peru',
			'PHI'=>'Philippines',
			'PLE'=>'Palestine',
			'PLW'=>'Palau',
			'PNG'=>'Papua New Guinea',
			'POL'=>'Poland',
			'POR'=>'Portugal',
			'PRK'=>'North Korea',
			'PUR'=>'Puerto Rico',
			'QAT'=>'Qatar',
			'ROU'=>'Romania',
			'RSA'=>'South Africa',
			'RUS'=>'Russia',
			'RWA'=>'Rwanda',
			'SAM'=>'Samoa',
			'SEN'=>'Senegal',
			'SEY'=>'Seychelles',
			'SIN'=>'Singapore',
			'SKN'=>'Saint Kitts and Nevis',
			'SLE'=>'Sierra Leone',
			'SLO'=>'Slovenia',
			'SMR'=>'San Marino',
			'SOL'=>'Solomon Islands',
			'SOM'=>'Somalia',
			'SRB'=>'Serbia',
			'SRI'=>'Sri Lanka',
			'STP'=>'São Tomé and Príncipe',
			'SUD'=>'Sudan',
			'SUI'=>'Switzerland',
			'SUR'=>'Suriname',
			'SVK'=>'Slovakia',
			'SWE'=>'Sweden',
			'SWZ'=>'Swaziland',
			'SYR'=>'Syria',
			'TAN'=>'Tanzania',
			'TGA'=>'Tonga',
			'THA'=>'Thailand',
			'TJK'=>'Tajikistan',
			'TKM'=>'Turkmenistan',
			'TLS'=>'Timor-Leste',
			'TOG'=>'Togo',
			'TPE'=>'Chinese Taipei',
			'TRI'=>'Trinidad and Tobago',
			'TUN'=>'Tunisia',
			'TUR'=>'Turkey',
			'TUV'=>'Tuvalu',
			'UAE'=>'United Arab Emirates',
			'UGA'=>'Uganda',
			'UKR'=>'Ukraine',
			'URU'=>'Uruguay',
			'USA'=>'United States',
			'UZB'=>'Uzbekistan',
			'VAN'=>'Vanuatu',
			'VEN'=>'Venezuela',
			'VIE'=>'Vietnam',
			'VIN'=>'Saint Vincent and the Grenadines',
			'YEM'=>'Yemen',
			'ZAM'=>'Zambia',
			'ZIM'=>'Zimbabwe',
	);
	return $Flags;
}

