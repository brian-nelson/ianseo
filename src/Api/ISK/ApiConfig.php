<?php
// this code-bit goes into the Tournament/index.php
$ISKServerUrl=getModuleParameter('ISK', 'ServerUrl', '');

$ConfigHtml='';
if(!empty($_REQUEST['api'])) {
    $ConfigHtml.='<tr>
        <th class="TitleLeft" width="25%">'.get_text('ISK-ServerUrl','Api').'</th>
        <td width="75%">
        <input type="text" style="width:100%" name="Module[ISK][ServerUrl]" value="'.$ISKServerUrl.'">
        </td>
        </tr>';
    $ISKServerUrlPIN=getModuleParameter('ISK', 'ServerUrlPin', '');
    $ConfigHtml.='<tr>
        <th class="TitleLeft" width="25%">'.get_text('ISK-ServerUrlPin','Api').'</th>
        <td width="75%">
        <input type="text" size="7" maxlength="4" name="Module[ISK][ServerUrlPin]" value="'.$ISKServerUrlPIN.'">
        '.get_text('ISK-ServerUrlPin','Help',$_SESSION["TourCode"].'|'.(empty($ISKServerUrlPIN) ? '____':$ISKServerUrlPIN)).'
        </td>
        </tr>';
    if ($_REQUEST['api']=='pro') {
        $ISKLicenseNumber=getModuleParameter('ISK', 'LicenseNumber', '');
        $ConfigHtml.='<tr>
            <th class="TitleLeft" width="15%">'.get_text('ISK-LicenseNumber','Api').'</th>
            <td width="75%">
            <input type="text" style="width:100%" name="Module[ISK][LicenseNumber]" value="'.$ISKLicenseNumber.'">
            </td>
            </tr>';
        /*
        if(!$ISKLicenseNumber) {
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
        */
    }
}
