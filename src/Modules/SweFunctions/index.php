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
        <h1 style="text-align: center; background-color:#004488; text-align:center;  padding:5px; padding-left:0px; padding-right: 0px; font-weight:bold; color: #F2F9ff; font-size:120%;">Support information!</h1>
        Detta är en IANSEO modul som supportas av Fredrik Larsson (fredrik@larre.nu) <br />
        för Svenska Bågskytteförbundets räkning.<br /><br />
        Uppstår frågor eller problem med denna modul kontakta då Fredrik Larsson, <br />
        IANSEO's support-team kan inte hjälpa till med frågor gällande denna modul.
    </div>
    <br /><hr style="width: 50%;"/><br />
    <div style="font-size: 1.2em; width: 50%; text-align: left;">Modulversion: <?= $swe_module_version; ?> </div>
    <div style="font-size: 1.2em; width: 50%; text-align: left;">Skickar till: <?= $swe_host; ?> </div>
    <br /><hr style="width: 50%;"/><br />
    <div style="font-size: 1.2em; width: 50%; text-align: left;">
        Modulen låter dig rapportera in tävlingensresultatet direkt till den svenska resultatdatabasen.<br />
        Fyll i Tävlingsnummer och lösenord, klicka sedan på Testa inställningar. <br />
        Som svar kommer du få tävlingens namn till svar och om den tillåter att resultat kan skickas in.<br />
        Klicka i omgångarna som skall skickas in och klicka på Skicka knappen<br /><br />
        <table>
            <thead><tr><th width="20%">Fält</th><th>Beskrivning</th></tr></thead>
            <tbody>
                <tr><td>Tävlingsnummer</td><td>Är tävlingens nummer i den svenska tävlingsdatabasen.</td></tr>
                <tr><td>Lösenord</td><td>Ett lösenord satt på tävlingen, tillsvidare kan lösenord begäras av Fredrik Larsson.</td></tr>
                <tr><td>Grundomgångar</td><td>Skickar in grundomgångens resultat till resultat databasen.</td></tr>
                <tr><td>Finaler</td><td>Skickar in resultaten för final omgångarna. (kommer fungera senare).</td></tr>
            </tbody>
        </table>
        <br />
    </div>
</div>

<div style="width: 50%; background-color:#004488; text-align:center;  padding:5px; padding-left:0px; padding-right: 0px; font-weight:bold; color: #F2F9ff; font-size:120%;">Skicka in resultat</div>
<br />
<div style="width: 50%; padding: 0;">
    <table id="ConfigurationSettings" class="SweTabel">
        <thead>
            <tr><th colspan="2">Inställningar</th></tr>
        </thead>
        <tbody>
            <tr><td class="SweTableRight" width="30%">Tävlingsnummer</td><td><input type="text" name="s_competition_code" id="s_competition_code"></input></td></tr>
            <tr><td class="SweTableRight" width="30%">Lösenord</td><td><input type="password" name="s_competition_password" id="s_competition_password"></input></td></tr>
            <tr><td colspan="2" class="SweCenter"><input type="submit" onClick="test_settings();" style="padding: 5px; margin: 5px;" value="Testa inställningar" /></td></tr>
            <tr><td colspan="2" class="SweCenter">&nbsp;</td></tr>
            <tr><td colspan="2" class="SweInformation" >&nbsp;<span id="status_information"></span></td></tr>
        </tbody>
    </table>
    <br /><br />
    <table id="CompetitonSelector" class="SweTabel">
        <thead>
            <tr><th colspan="2">Rapportera</th></tr>
        </thead>
        <tbody>
            <tr><td class="SweTableRight" width="30%" rowspan=2>Individuella</td><td><input type="checkbox" name="r_select" value="ind_kval" />Grundomgångar</input></td></tr>
            <tr><td><input type="checkbox" name="r_select" value="ind_final" />Finaler</input></td></tr>
            <tr><td colspan="2" class="SweCenter"><input type="submit" onClick="send_result();" style="padding: 5px; margin: 5px;" value="Skicka" id="sendbutton" /></td></tr>
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
        console.log("Button disabled.");
    }
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
                console.log(resp['status']);
                if (resp['status'] == 'Ready') {
                    document.getElementById("sendbutton").disabled = false;
                    console.log("Button changed. aktivated");
                } else {
                    document.getElementById("sendbutton").disabled = true;
                    console.log("button changed. disabled");
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
                        document.getElementById("status_result").innerHTML += data['results'][xc]['className'] + ' Exporterade: ' + data['results'][xc]['imported'] + ' Ej exporterade: ' + data['results'][xc]['failed'];
			            if (data['results'][xc]['warningMessage'] != null) {
                           document.getElementById("status_result").innerHTML += ' Varningar: (' + data['results'][xc]['warnings'] +')<br /><span class="swewarning"> ' + data['results'][xc]['warningMessage'] + '</span>';
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
