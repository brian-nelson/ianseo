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
    // get list from WA Countries
	$Flags=array(
        'AFG' => 'Afghanistan',
        'ALB' => 'Albania',
        'ALG' => 'Algeria',
        'AND' => 'Andorra',
        'ARG' => 'Argentina',
        'ARM' => 'Armenia',
        'ARU' => 'Aruba',
        'ASA' => 'American Samoa',
        'AUS' => 'Australia',
        'AUT' => 'Austria',
        'AZE' => 'Azerbaijan',
        'BAH' => 'Bahamas',
        'BAN' => 'Bangladesh',
        'BAR' => 'Barbados',
        'BEL' => 'Belgium',
        'BEN' => 'Benin',
        'BER' => 'Bermuda',
        'BHU' => 'Bhutan',
        'BIH' => 'Bosnia and Herzegovina',
        'BLR' => 'Belarus',
        'BOL' => 'Bolivia',
        'BRA' => 'Brazil',
        'BUL' => 'Bulgaria',
        'BUR' => 'Burkina Faso',
        'CAF' => 'Central African Republic',
        'CAM' => 'Cambodia',
        'CAN' => 'Canada',
        'CHA' => 'Chad',
        'CHI' => 'Chile',
        'CHN' => 'PR China',
        'CIV' => 'Cote d Ivoire',
        'CMR' => 'Cameroon',
        'COD' => 'DR Congo',
        'COL' => 'Colombia',
        'COM' => 'Comoros',
        'CRC' => 'Costa Rica',
        'CRO' => 'Croatia',
        'CUB' => 'Cuba',
        'CYP' => 'Cyprus',
        'CZE' => 'Czech Republic',
        'DEN' => 'Denmark',
        'DJI' => 'Djibouti',
        'DMA' => 'Dominica',
        'DOM' => 'Dominican Republic',
        'ECU' => 'Ecuador',
        'EGY' => 'Egypt',
        'ERI' => 'Eritrea',
        'ESA' => 'El Salvador',
        'ESP' => 'Spain',
        'EST' => 'Estonia',
        'FIJ' => 'Fiji',
        'FIN' => 'Finland',
        'FLK' => 'Falkland Island',
        'FPO' => 'Tahiti',
        'FRA' => 'France',
        'FRO' => 'Faroe Islands',
        'GAB' => 'Gabon',
        'GBR' => 'Great Britain',
        'GEO' => 'Georgia',
        'GER' => 'Germany',
        'GHA' => 'Ghana',
        'GLP' => 'Guadalupe',
        'GRE' => 'Greece',
        'GUA' => 'Guatemala',
        'GUI' => 'Guinea',
        'GUM' => 'Guam',
        'GUY' => 'Guyana',
        'HAI' => 'Haiti',
        'HKG' => 'Hong Kong, China',
        'HON' => 'Honduras',
        'HUN' => 'Hungary',
        'INA' => 'Indonesia',
        'IND' => 'India',
        'IOA' => 'Int. Olympic Archer',
        'IPA' => 'Int. Paralympic Archer',
        'IRI' => 'IR Iran',
        'IRL' => 'Ireland',
        'IRQ' => 'Iraq',
        'ISL' => 'Iceland',
        'ISR' => 'Israel',
        'ISV' => 'Virgin Islands, US',
        'ITA' => 'Italy',
        'IVB' => 'British Virgin Islands',
        'JOR' => 'Jordan',
        'JPN' => 'Japan',
        'KAZ' => 'Kazakhstan',
        'KEN' => 'Kenya',
        'KGZ' => 'Kyrgyzstan',
        'KIR' => 'Kiribati',
        'KOR' => 'Korea',
        'KOS' => 'Kosovo',
        'KSA' => 'Saudi Arabia',
        'KUW' => 'Kuwait',
        'LAO' => 'Laos',
        'LAT' => 'Latvia',
        'LBA' => 'Libya',
        'LBR' => 'Liberia',
        'LES' => 'Lesotho',
        'LIB' => 'Lebanon',
        'LIE' => 'Liechtenstein',
        'LTU' => 'Lithuania',
        'LUX' => 'Luxembourg',
        'MAC' => 'Macau, China',
        'MAD' => 'Madagascar',
        'MAR' => 'Morocco',
        'MAS' => 'Malaysia',
        'MAW' => 'Malawi',
        'MDA' => 'Moldova',
        'MEX' => 'Mexico',
        'MGL' => 'Mongolia',
        'MKD' => 'North Macedonia',
        'MLI' => 'Mali',
        'MLT' => 'Malta',
        'MNE' => 'Montenegro',
        'MON' => 'Monaco',
        'MRI' => 'Mauritius',
        'MTN' => 'Mauritania',
        'MTQ' => 'Martinique',
        'MYA' => 'Myanmar',
        'NAM' => 'Namibia',
        'NCA' => 'Nicaragua',
        'NCL' => 'New Caledonia',
        'NED' => 'Netherlands',
        'NEP' => 'Nepal',
        'NFK' => 'Norfolk Island',
        'NGR' => 'Nigeria',
        'NIG' => 'Niger',
        'NIU' => 'Niue',
        'NMI' => 'Northern Mariana Islands',
        'NOR' => 'Norway',
        'NZL' => 'New Zealand',
        'PAK' => 'Pakistan',
        'PAN' => 'Panama',
        'PAR' => 'Paraguay',
        'PER' => 'Peru',
        'PHI' => 'Philippines',
        'PLW' => 'Palau',
        'PNG' => 'Papua New Guinea',
        'POL' => 'Poland',
        'POR' => 'Portugal',
        'PRK' => 'DPR Korea',
        'PUR' => 'Puerto Rico',
        'QAT' => 'Qatar',
        'ROU' => 'Romania',
        'RSA' => 'South Africa',
        'RUS' => 'Russia',
        'RWA' => 'Rwanda',
        'SAM' => 'Samoa',
        'SCG' => 'Serbia and Montenegro',
        'SEN' => 'Senegal',
        'SGP' => 'Singapore',
        'SKN' => 'Saint Kitts and Nevis',
        'SLE' => 'Sierra Leone',
        'SLO' => 'Slovenia',
        'SMR' => 'San Marino',
        'SOL' => 'Solomon Islands',
        'SOM' => 'Somalia',
        'SRB' => 'Serbia',
        'SRI' => 'Sri Lanka',
        'SUD' => 'Sudan',
        'SUI' => 'Switzerland',
        'SUN' => 'USSR',
        'SUR' => 'Suriname',
        'SVK' => 'Slovakia',
        'SWE' => 'Sweden',
        'SYR' => 'Syria',
        'TGA' => 'Tonga',
        'THA' => 'Thailand',
        'TJK' => 'Tajikistan',
        'TKM' => 'Turkmenistan',
        'TOG' => 'Togo',
        'TPE' => 'Chinese Taipei',
        'TTO' => 'Trinidad and Tobago',
        'TUN' => 'Tunisia',
        'TUR' => 'Turkey',
        'UAE' => 'UAE',
        'UGA' => 'Uganda',
        'UKR' => 'Ukraine',
        'URU' => 'Uruguay',
        'USA' => 'USA',
        'UZB' => 'Uzbekistan',
        'VAN' => 'Vanuatu',
        'VEN' => 'Venezuela',
        'VIE' => 'Vietnam',
        'VIN' => 'St Vincent and the Grenadines',
        'YEM' => 'Yemen',
        'YUG' => 'Yugoslavia',
        'ZAM' => 'Zambia',
        'ZIM' => 'Zimbabwe',
	);
	return $Flags;
}

