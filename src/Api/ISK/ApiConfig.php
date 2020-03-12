<?php
// this code-bit goes into the Tournament/index.php
$ISKServerUrl=getModuleParameter('ISK', 'ServerUrl', '');

$ConfigHtml='<tr>
	<th class="TitleLeft" width="25%">'.get_text('ISK-ServerUrl','Api').'</th>
	<td width="75%">
	<input type="text" style="width:100%" name="Module[ISK][ServerUrl]" value="'.$ISKServerUrl.'">
	</td>
	</tr>';

if(!empty($_REQUEST['api']) and $_REQUEST['api']=='pro') {
	//$ISKLicenseEmail=getModuleParameter('ISK', 'LicenseEmail', '');
	//$ConfigHtml.='<tr>
	//			<th class="TitleLeft" width="15%">'.get_text('ISK-LicenseEmail','Api').'</th>
	//			<td width="75%">
	//			<input type="text" style="width:100%" name="Module[ISK][LicenseEmail]" value="'.$ISKLicenseEmail.'">
	//			</td>
	//			</tr>';

	$ISKLicenseNumber=getModuleParameter('ISK', 'LicenseNumber', '');
	$ConfigHtml.='<tr>
		<th class="TitleLeft" width="15%">'.get_text('ISK-LicenseNumber','Api').'</th>
		<td width="75%">
		<input type="text" style="width:100%" name="Module[ISK][LicenseNumber]" value="'.$ISKLicenseNumber.'">
		</td>
		</tr>';
	if(!$ISKLicenseNumber and $CFG->ISK_PRO_DEBUG) {
		$ISKLicenseVoucher=getModuleParameter('ISK', 'LicenseVoucher', '');
		$ConfigHtml.='<tr>
			<th class="TitleLeft" width="15%"><div>'.get_text('ISK-LicenseVoucher','Api').'</div>
				<div>'.get_text('ISK-LicenseVoucherTip', 'Api').'</div>
			</th>
			<td width="75%">
			<input type="text" style="width:100%" name="Module[ISK][LicenseVoucher]" value="'.$ISKLicenseVoucher.'">
			</td>
			</tr>';

	}
}

