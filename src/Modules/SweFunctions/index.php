<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/config.php');

CheckTourSession(true);

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
include('Common/Templates/head.php');
?>
<div align="center">
<style>
#sweDiv1 {
}
.SweTabel {
    width: 100%;
    padding: 0;
    margin: 0;
}
.SweTabel > thead > tr {
    background-color:#004488; 
    text-align:center;  
    font-weight:bold; 
    color: #F2F9ff; 
    font-size:120%;
}
.SweTabel > thead > tr > th {
    padding:5px; 
}

.SweTableRight {
    text-align: right;
    font-weight:bold; 
}
.SweInformation {
    background-color: rgba(255,255,255,0.5);
    border: 1px solid black;
    padding-top: 5px;
    padding-bottom: 5px;
}
.SweCenter {
    text-align: center;
}
.swewarning {
    background-color: rgba(255,0,0,0.25);
}
</style>
<div id="sweDiv1">
    
    <div style="font-size: 1.2em; width: 50%; text-align: left;" >
        <h1 style="text-align: center; background-color:#004488; text-align:center;  padding:5px; padding-left:0px; padding-right: 0px; font-weight:bold; color: #F2F9ff; font-size:120%;"><?= $swelang_supportinformation; ?></h1>
        <?= $swelang_supportinformationtext; ?>
    </div>
    <br /><hr style="width: 50%;"/><br />
    <div style="font-size: 1.2em; width: 50%; text-align: left;"><?= $swelang_moduleversion; ?>: <?= $swe_module_version; ?> </div>
    <div style="font-size: 1.2em; width: 50%; text-align: left;"><?= $swelang_sendto; ?>: <?= $swe_host; ?> </div>
    <br /><hr style="width: 50%;"/><br />
    <div style="font-size: 1.2em; width: 50%; text-align: left;">
        <?= $swelang_moduleinformation; ?>
        <table>
            <thead><tr><th width="20%"><?= $swelang_field; ?></th><th><?= $swelang_description; ?></th></tr></thead>
            <tbody>
                <tr><td><?= $swelang_eventnumber; ?></td><td><?= $swelang_eventnumber_desc; ?></td></tr>
                <tr><td><?= $swelang_password; ?></td><td><?= $swelang_password_desc; ?></td></tr>
                <tr><td><?= $swelang_qualification; ?></td><td><?= $swelang_qualification_desc; ?></td></tr>
                <tr><td><?= $swelang_final; ?></td><td><?= $swelang_final_desc; ?></td></tr>
            </tbody>
        </table>
        <br />
    </div>
</div>

<!--div style="width: 50%; background-color:#004488; text-align:center;  padding:5px; padding-left:0px; padding-right: 0px; font-weight:bold; color: #F2F9ff; font-size:120%;">Skicka in resultat</div -->
<br />
<div style="width: 50%; padding: 0;">
    <table id="ConfigurationSettings" class="SweTabel">
        <thead>
            <tr><th colspan="2"><?= $swelang_settings; ?></th></tr>
        </thead>
        <tbody>
            <tr><td class="SweTableRight" width="30%"><?= $swelang_eventnumber; ?></td><td><input type="text" name="s_competition_code" id="s_competition_code"></input></td></tr>
            <tr><td class="SweTableRight" width="30%"><?= $swelang_password; ?></td><td><input type="password" name="s_competition_password" id="s_competition_password"></input></td></tr>
            <tr><td colspan="2" class="SweCenter"><input type="submit" onClick="test_settings();" style="padding: 5px; margin: 5px;" value="<?= $swelang_testsettings; ?>" /></td></tr>
            <tr><td colspan="2" class="SweCenter">&nbsp;</td></tr>
            <tr><td colspan="2" class="SweInformation" >&nbsp;<span id="status_information"></span></td></tr>
        </tbody>
    </table>
    <br /><br />
    <table id="CompetitonSelector" class="SweTabel">
        <thead>
            <tr><th colspan="2"><?= $swelang_sendresult; ?></th></tr>
        </thead>
        <tbody>
            <tr><td class="SweTableRight" width="30%" rowspan=2><?= $swelang_individ; ?></td><td><input type="checkbox" name="r_select" value="ind_kval" /><?= $swelang_qualification; ?></input></td></tr>
            <tr><td><input type="checkbox" name="r_select" value="ind_final" /><?= $swelang_final; ?></input></td></tr>
            <tr><td colspan="2" class="SweCenter"><input type="submit" onClick="send_result();" style="padding: 5px; margin: 5px;" value="<?= $swelang_send; ?>" id="sendbutton" /></td></tr>
            <tr><td colspan="2" class="SweCenter">&nbsp;</td></tr>
            <tr><td colspan="2" class="SweInformation">&nbsp;<span id="status_result"></span></td></tr>
        </tbody>
    </table>
</div>
</div>
<script>
    window.onload = disableButton;
    function disableButton() {
        document.getElementById("sendbutton").disabled = true;
    };
    function test_settings() {
        var data = {"auth": {"competition": document.getElementById("s_competition_code").value, "password": document.getElementById("s_competition_password").value}};
        var xhr = new XMLHttpRequest();
        xhr.open("POST","testsetting.php",true);
        xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
        xhr.onreadystatechange = handler;
        xhr.send(JSON.stringify(data));
    };
    function send_result() {
        var data = {"auth": {"competition": document.getElementById("s_competition_code").value, "password": document.getElementById("s_competition_password").value}};
        data["option"] = {"ind_kval": document.getElementsByName("r_select")[0].checked, "ind_final": document.getElementsByName("r_select")[1].checked};
        var xhr = new XMLHttpRequest();
        xhr.open("POST","send.php",true);
        xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
        xhr.onreadystatechange = resulthandler;
        xhr.send(JSON.stringify(data));
    };
    function handler() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var resp = JSON.parse(this.responseText);
                if (resp['status'] == 'Ready') {
                    document.getElementById("sendbutton").disabled = false;
                } else {
                    document.getElementById("sendbutton").disabled = true;
                }
                document.getElementById("status_information").innerHTML = resp['message'];
            }
        }
    };
    function resulthandler() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                var data = JSON.parse(this.response);
                if (data['status'] == 'Ready') {
                    var xc;
                    document.getElementById("status_result").innerHTML = "";
                    for (xc in data['results']) {
                        document.getElementById("status_result").innerHTML += '<br />' + data['results'][xc]['className'] + '<br /><?= $swelang_exported ?>: ' + data['results'][xc]['imported'] + ' <?= $swelang_notexported ?>: ' + data['results'][xc]['failed'];
			            if (data['results'][xc]['warningMessage'] != null) {
                           document.getElementById("status_result").innerHTML +=  '<br /><?= $swelang_warnings ?>: (' + data['results'][xc]['warnings'] +')<br /><span class="swewarning"> ' + data['results'][xc]['warningMessage'] + '</span>';
                        }
                        document.getElementById("status_result").innerHTML += '<br />';
                        console.log(data['results']);
                    }
                    document.getElementById("status_result").innerHTML += "</tbody></table>";
                } else {
                    document.getElementById("status_result").innerHTML = data['message'];
                    console.log(data);
                }

            }
        }
    };
</script>
<?php
	include('Common/Templates/tail.php');
?>
