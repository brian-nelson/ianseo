<?php
require_once(dirname(__FILE__) . '/config.php');

$JSON=array('value' => '');

switch($_REQUEST['type']) {
	case '1':
		// Accreditation
		$JSON['value']='<table>
				<tr><th>'.get_text('Photo', 'Tournament').'</th><th>'.get_text('Payments', 'Tournament').'</th></tr>
				<tr>
					<td>
						<input type="radio" name="photo" value="" checked="checked">'.get_text('NoFilters', 'Tournament').'
						<br/><input type="radio" name="photo" value="1">'.get_text('OnlyWithPhoto', 'Tournament').'
					</td>
					<td>
						<input type="radio" name="payment" value="" checked="checked">'.get_text('NoFilters', 'Tournament').'
						<br/><input type="radio" name="payment" value="1">'.get_text('OnlyPaid', 'Tournament').'
						<br/><input type="radio" name="payment" value="2">'.get_text('ForcePayment', 'Tournament').'
					</td>
				</tr>
				</table>';
		break;
	case '2':
		// Equipment inspection
		$JSON['value']='<table>
				<tr><th>'.get_text('Photo', 'Tournament').'</th><th>'.get_text('Payments', 'Tournament').'</th><th>'.get_text('Accreditation', 'Tournament').'</th></tr>
				<tr>
					<td>
						<input type="radio" name="photo" value="" checked="checked">'.get_text('NoFilters', 'Tournament').'
						<br/><input type="radio" name="photo" value="1">'.get_text('OnlyWithPhoto', 'Tournament').'
					</td>
					<td>
						<input type="radio" name="payment" value="" checked="checked">'.get_text('NoFilters', 'Tournament').'
						<br/><input type="radio" name="payment" value="1">'.get_text('OnlyPaid', 'Tournament').'
						<br/><input type="radio" name="payment" value="2">'.get_text('ForcePayment', 'Tournament').'
					</td>
					<td>
						<input type="radio" name="accreditation" value="" checked="checked">'.get_text('NoFilters', 'Tournament').'
						<br/><input type="radio" name="accreditation" value="1">'.get_text('OnlyAccredited', 'Tournament').'
						<br/><input type="radio" name="accreditation" value="2">'.get_text('ForceAccreditation', 'Tournament').'
					</td>
				</tr>
				</table>';
		break;
}

JsonOut($JSON);