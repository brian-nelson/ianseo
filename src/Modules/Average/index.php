<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

if(!is_writable(dirname(__FILE__))) {
	die(get_text('DirectoryNotWriteable', 'Errors', dirname(__FILE__)));
}
checkACL(AclModules, AclReadOnly);

$Archers=array();
$Bonus=array();
$Coeffs=array('qual' => 0.4, 'arrow' => 0.2, 'rank' => 0.4);

if(file_exists('./conf.php')) {
	list($Archers, $Bonus, $Coeffs)=json_decode(file_get_contents('./conf.php'), true);
}
if(!$Coeffs) {
	$Coeffs=array('qual' => 0.4, 'arrow' => 0.2, 'rank' => 0.4);
}

if(!empty($_REQUEST['del'])) {
	if(in_array($_REQUEST['del'], $Archers)) unset($Archers[array_search($_REQUEST['del'], $Archers)]);
	unset($Bonus[$_REQUEST['del']]);

	file_put_contents('./conf.php', json_encode(array($Archers, $Bonus, $Coeffs)));
	cd_redirect();
}

if($_POST) {
	if(!empty($_POST['EnCode'])) {
		foreach($_POST['EnCode'] as $EnCode) {
			$Archers[]=$EnCode;
		}
	}
	if(!empty($_POST['bonus'])) {
		$Bonus=array();
		foreach($_POST['bonus'] as $EnCode => $EnBonus) {
			if($EnBonus) $Bonus[$EnCode]=$EnBonus;
		}
	}
	if(!empty($_POST['coeff'])) {
		$Coeffs=array();
		foreach($_POST['coeff'] as $Type => $Coef) {
			$Coeffs[$Type]=str_replace(',', '.', $Coef);
		}
	}

	file_put_contents('./conf.php', json_encode(array($Archers, $Bonus, $Coeffs)));
	cd_redirect();
}

include('Common/Templates/head.php');

echo '<form method="POST"><table class="Tabella" style="width:auto; margin:auto;">';
echo '<tr>
		<th colspan="3">'.get_text('QualRound').'</th>
		<th colspan="3">'.get_text('Arrows', 'Tournament').'</th>
		<th colspan="3">'.get_text('Finals', 'Tournament').'</th>
		</tr>';
echo '<tr>
		<td colspan="3"><input type="text" name="coeff[qual]" value="'.$Coeffs['qual'].'"></td>
		<td colspan="3"><input type="text" name="coeff[arrow]" value="'.$Coeffs['arrow'].'"></td>
		<td colspan="3"><input type="text" name="coeff[rank]" value="'.$Coeffs['rank'].'"></td>
		</tr>';

echo '<tr><th></th>
		<th>'.get_text('Code', 'Tournament').'</th>
		<th>'.get_text('FamilyName', 'Tournament').'</th>
		<th>'.get_text('Name', 'Tournament').'</th>
		<th>'.get_text('Division').'</th>
		<th>'.get_text('Class').'</th>
		<th>'.get_text('CountryCode').'</th>
		<th>'.get_text('Country').'</th>
		<th>'.get_text('Bonus', 'Tournament').'</th></tr>';

$q=safe_r_sql("select * from Entries
	inner join Countries on CoId=EnCountry
	where EnTournament={$_SESSION['TourId']} and EnCode in ('".implode("','", $Archers)."') order by EnDivision, EnClass, EnFirstName");
while($r=safe_fetch($q)) {
	echo '<tr><td><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onclick="if(confirm(\'Remove this entry?\')) {window.location=\''.basename(__FILE__).'?del='.$r->EnCode.'\'}"></td>
		<td>'.$r->EnCode.'</td>
		<td>'.$r->EnFirstName.'</td>
		<td>'.$r->EnName.'</td>
		<td>'.$r->EnDivision.'</td>
		<td>'.$r->EnClass.'</td>
		<td>'.$r->CoCode.'</td>
		<td>'.$r->CoName.'</td>
		<td><input type="text" name="bonus['.$r->EnCode.']" value="'.(empty($Bonus[$r->EnCode]) ? 0 : $Bonus[$r->EnCode]).'"></td></tr>';
}
if(safe_num_rows($q)) {
	echo '<tr><td colspan="7"><input type="submit">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="document.location=\'PDF.php\'" value="Print Rank"></td></tr>';
}

$q=safe_r_sql("select * from Entries
	inner join Countries on CoId=EnCountry
	where EnTournament={$_SESSION['TourId']} and EnCode not in ('".implode("','", $Archers)."')
	order by EnDivision, EnClass, EnFirstname, EnName");
while($r=safe_fetch($q)) {
	echo '<tr><td><input type="checkbox" name="EnCode[]" value="'.$r->EnCode.'"></td>
		<td>'.$r->EnCode.'</td>
		<td>'.$r->EnFirstName.'</td>
		<td>'.$r->EnName.'</td>
		<td>'.$r->EnDivision.'</td>
		<td>'.$r->EnClass.'</td>
		<td>'.$r->CoCode.'</td>
		<td colspan="2">'.$r->CoName.'</td></tr>';
}

echo '<tr><td colspan="7"><input type="submit"></td></tr>';

echo '</table></form>';
include('Common/Templates/tail.php');