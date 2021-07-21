<?php
/*
THIS FILE IS ESSENTIAL TO MAKE THE APIS TO GET RECOGNIZED BY IANSEO

* HOW IT WORKS
the "codename" of the API will be used in ianseo. The codename is the name of the directory containing the Api.
The essentials are:

* ApiConfig.php
this file gets included in the Competition Setup (Tournament/index.php)
* DrawQRCode.php
this file is used by the ScoreCard printout routines.
 */

require_once(__DIR__.'/config-ianseo.php');

if(ISK_PRO) {
	CD_redirect('./');
}

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);


if(!empty($_REQUEST['remove'])) {
	safe_w_sql("delete from IskData where IskDtDevice=".StrSafe_DB($_REQUEST['remove']));
	safe_w_sql("delete from IskDevices where IskDvDevice=".StrSafe_DB($_REQUEST['remove']));
	cd_redirect('./');
}

require_once('Common/Lib/CommonLib.php');

$ImportType=getModuleParameter('ISK', 'ImportType', 0, 0, true);
$ClDivIndCalc=getModuleParameter('ISK','CalcClDivInd',0, 0, true);
$ClDivTeamCalc=getModuleParameter('ISK','CalcClDivTeam',0, 0, true);
$FinIndCalc=getModuleParameter('ISK','CalcFinInd',0, 0, true);
$FinTeamCalc=getModuleParameter('ISK','CalcFinTeam',0, 0, true);

$PAGE_TITLE=get_text('AutoImportSettings', 'ISK');
$JS_SCRIPT=array(
	phpVars2js(array(
		'MsgConfirm'=>htmlspecialchars(get_text('MsgAreYouSure')),
		'ImportType'=>$ImportType,
        'CalcDivClI'=>$ClDivIndCalc,
        'CalcDivClT'=>$ClDivTeamCalc,
        'CalcFinI'=>$FinIndCalc,
        'CalcFinT'=>$FinTeamCalc
    )),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="./Lite.js"></script>',
	'<link href="ISK.css" rel="stylesheet" type="text/css">',
);


include('Common/Templates/head.php');

echo '<table class="Tabella LiteIndex">';
echo '<tr><th class="Main" colspan="6">' . get_text('AutoImportSettings', 'ISK') . '</th></tr>';
echo '<tr>'.
        '<th class="Title"></th>'.
        '<th class="Title">' . get_text('AfterEachArrow', 'ISK') . '</th>'.
        '<th class="Title">' . get_text('AfterEachEnd', 'ISK') . '</th>'.
        '<th class="Title">' . get_text('Manually', 'ISK') . '</th>'.
        '<th class="Title" colspan="2">&nbsp;</th>'.
    '</tr>';
echo '<tr>';
echo '<th class="Left">'.get_text('ImportType', 'ISK').'</th>';
echo '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="ImportType" value="0"'.($ImportType==0 ? ' checked="checked"' : '').'></td>';
echo '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="ImportType" value="1"'.($ImportType==1 ? ' checked="checked"' : '').'></td>';
echo '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="ImportType" value="2"'.($ImportType==2 ? ' checked="checked"' : '').'></td>';
echo '<td class="Center"><div class="Button Cl-ImportType" onclick="LiteButton(this)" id="ImportQualNow">'.get_text('ImportNow','ISK').'</div>
	<br/><div class="Button Cl-ImportType" onclick="LiteButton(this)" id="ImportMatchNow">'.get_text('ImportNowMatches','ISK').'</div></td>
	<td class="Center"><div class="Button Cl-ImportType" onclick="LiteDelete(this)" id="DeleteDataQual">'.get_text('TruncateQual','ISK').'</div>
	<br/><div class="Button Cl-ImportType" onclick="LiteDelete(this)" id="DeleteDataMatch">'.get_text('TruncateMatches','ISK').'</div></td>';
echo '</tr>';
echo '<tr class"divider"><td colspan="6"></td></tr>';
echo '<tr>'.
        '<th class="Title"></th>'.
        '<th class="Title" colspan="2">' . get_text('AfterEachImport', 'ISK') . '</th>'.
        '<th class="Title">' . get_text('Manually', 'ISK') . '</th>'.
        '<th class="Title" colspan="2">&nbsp;</th>'.
    '</tr>';
echo '<tr class="warning"><td colspan="6" class="">'.get_text('AutoImportSettings', 'Help').'</td></tr>';
echo '<tr>'.
        '<th class="Left">'.get_text('CalcClDivInd', 'ISK').'</th>'.
        '<td class="Center" colspan="2"><input type="radio" onclick="LiteAction(this)" name="CalcClDivInd" value="0"'.($ClDivIndCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivInd" value="1"'.($ClDivIndCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="LiteButton(this)" id="doCalcClDivInd">'.get_text('CalculateNow','ISK').'</div></td>'.
        '<td class="Center"></td>'.
    '</tr>';
echo '<tr>'.
        '<th class="Left">'.get_text('CalcFinInd', 'ISK').'</th>'.
        '<td class="Center" colspan="2"><input type="radio" onclick="LiteAction(this)" name="CalcFinInd" value="0"'.($FinIndCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinInd" value="1"'.($FinIndCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="LiteButton(this)" id="doCalcFinInd">'.get_text('CalculateNow','ISK').'</div></td>'.
        '<td class="Center"></td>'.
    '</tr>';
echo '<tr class"divider"><td colspan="6"></td></tr>';
echo '<tr>'.
        '<th class="Left">'.get_text('CalcClDivTeam', 'ISK').'</th>'.
        '<td class="Center" colspan="2"><input type="radio" onclick="LiteAction(this)" name="CalcClDivTeam" value="0"'.($ClDivTeamCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivTeam" value="1"'.($ClDivTeamCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="LiteButton(this)" id="doCalcClDivTeam">'.get_text('CalculateNow','ISK').'</div></td>'.
        '<td class="Center"></td>'.
    '</tr>';
echo '<tr>'.
        '<th class="Left">'.get_text('CalcFinTeam', 'ISK').'</th>'.
        '<td class="Center" colspan="2"><input type="radio" onclick="LiteAction(this)" name="CalcFinTeam" value="0"'.($FinTeamCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinTeam" value="1"'.($FinTeamCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="LiteButton(this)" id="doCalcFinTeam">'.get_text('CalculateNow','ISK').'</div></td>'.
        '<td class="Center"></td>'.
    '</tr>';
echo '</table>';

include('Common/Templates/tail.php');
