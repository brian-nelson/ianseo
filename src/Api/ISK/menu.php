<?php
/**
 * Created by PhpStorm.
 * User: deligant
 * Date: 05/04/17
 * Time: 8.07
 */

if($_SESSION['UseApi']==2) {
    if ($acl[AclISKServer] == AclReadWrite) {
        $ret['API'][] = get_text('ISK-Configuration') . '|' . $CFG->ROOT_DIR . 'Api/ISK/';
        $ret['API'][] = get_text('ISK-Results') . '|' . $CFG->ROOT_DIR . 'Api/ISK/Results.php';
    }
    if ($acl[AclISKServer] >= AclReadOnly) {
        $ret['API'][] = get_text('ISK-Anomalies', 'Api') . '|' . $CFG->ROOT_DIR . 'Api/ISK/Anomalies.php';
    }
    if ($acl[AclISKServer] == AclReadWrite) {
        $ret['API'][] = MENU_DIVIDER;
        $ret['API'][] = get_text('MenuLM_QrCodes') . '|' . $CFG->ROOT_DIR . 'Api/ISK/QRcodes.php';
        $ret['API'][] = get_text('ISK-GetQRData') . '|' . $CFG->ROOT_DIR . 'Api/ISK/GetQRData.php';
        $ret['API'][] = MENU_DIVIDER;
    }
}
if ($acl[AclISKServer] >= AclReadWrite) {
    $ret['API'][] = get_text('API-TargetGrouping', 'Api') . '|' . $CFG->ROOT_DIR . 'Api/ISK/ApiGrouping.php';
}