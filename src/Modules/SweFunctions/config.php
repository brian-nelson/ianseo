<?php
$swe_module_version = "20210405";

if ($_SESSION['TourLocRule'] == 'SE') {
    // Config
    $swe_host = "https://resultat.bagskytte.se";
    // Language
    $swelang_menutext = "* Exportera svenska resultat";
    $swelang_supportinformation = "Support information!";
    $swelang_supportinformationtext = "Detta är en IANSEO modul som supportas av Fredrik Larsson (fredrik@larre.nu) <br />
            för Svenska Bågskytteförbundets räkning.<br /><br />
            Uppstår frågor eller problem med denna modul kontakta då Fredrik Larsson, <br />
            IANSEO's support-team kan inte hjälpa till med frågor gällande denna modul.";
    $swelang_moduleversion = "Modulversion";
    $swelang_sendto = "Skickar till";
    $swelang_moduleinformation = "
            Modulen låter dig rapportera in tävlingensresultatet direkt till den svenska resultatdatabasen.<br />
            Fyll i Tävlingsnummer och lösenord, klicka sedan på Testa inställningar. <br />
            Som svar kommer du få tävlingens namn till svar och om den tillåter att resultat kan skickas in.<br />
            Klicka i omgångarna som skall skickas in och klicka på Skicka knappen<br /><br />";
    $swelang_field = "Fält";
    $swelang_description = "Beskrivning";
    $swelang_eventnumber = "Tävlingsnummer";
    $swelang_eventnumber_desc = "Är tävlingens nummer angivet i tävlingsdatabasen.";
    $swelang_password = "Lösenord";
    $swelang_password_desc = "Lösenordet som är satt på tävlingen i tävlingsdatabasen.";
    $swelang_qualification = "Grundomgång";
    $swelang_qualification_desc = "Skickar in grundomgångens resultat.";
    $swelang_final = "Final";
    $swelang_final_desc = "Skickar in finalernas resultat.";
    $swelang_sendresult = "Skicka in resultat";
    $swelang_testsettings = "Testa inställningar";
    $swelang_settings = "Inställningar";
    $swelang_individ = "Individuella";
    $swelang_send = "Skicka";
    $swelang_noinformation_sent = "Ingen data att skicka.";
    $swelang_notexported = "Ej exporterade";
    $swelang_exported = "Exporterade";
    $swelang_warnings = "Varningar";
}
elseif ($_SESSION['TourLocRule'] == 'NO') {
    // Config
    $swe_host = "https://resultat.bueskyting.no";
    // Language
    $swelang_menutext = "* Exportere norske resultat";
    $swelang_supportinformation = "Support information!";
    $swelang_supportinformationtext = "Dette er en IANSEO modul som supporteres av Jon-Arne Storelv (jon-arne@bueskyting.no) <br />
            for Norges Bueskytterforbunds regning. <br /><br />
            Om du har spørsmål eller problemer med denne modulen, kontakt da Jon-Arne  Storelv,<br />
            IANSEO's support-team kan ikke hjelpe til med spørsmål vedrørende denne modul.";
    $swelang_moduleversion = "Modulversion";
    $swelang_sendto = "Sender til";
    $swelang_moduleinformation = "
            Modulen lar deg rapportere in stevneresultat direkte til den norske resultatdatabasen.<br />
            Fyll inn stevnenummer og passord, klikk deretter på Test innstillinger.<br />
            Som svar kommer du til å få stevnets navn og om den tillatter at resultat sendes inn.<br />
            Velg om det er Kvalifisering og/eller finaler den skal sendes inn resultater og klikk på Last opp knappen<br /><br />";
    $swelang_field = "Felt";
    $swelang_description = "Beskrivelse";
    $swelang_eventnumber = "Stevnenummer";
    $swelang_eventnumber_desc = "Stevnets nummer i den norske resultat-databasen.";
    $swelang_password = "Passord";
    $swelang_password_desc = "Ett passord satt på stevnet.";
    $swelang_qualification = "Kvalifiseringsrunde";
    $swelang_qualification_desc = "Sender inn resultat for kvalifiseringsrunder til resultat-databasen.";
    $swelang_final = "Finaler";
    $swelang_final_desc = "Sender inn resultater for finalerundene..";
    $swelang_sendresult = "Sender inn resultat";
    $swelang_testsettings = "Teste innstillinger";
    $swelang_settings = "Instillinger";
    $swelang_individ = "Individuelle";
    $swelang_send = "Last opp";
    $swelang_noinformation_sent = "Ikke noe data å sende.";

    $swelang_notexported = "Not exported";
    $swelang_exported = "Exported";
    $swelang_warnings = "Warnings";

} 
elseif ($_SESSION['TourLocRule'] == 'IS') {
    // Config
    $swe_host = "https://mot.bogfimi.is";
    // Language
    $swelang_menutext = "* Senda íslensk úrslit";
    $swelang_supportinformation = "Support information!";
    $swelang_supportinformationtext = "Þetta er ianseo viðbót sem hægt er að fá stuðning við á bogfimi@bogfimi.is <br />
            fyrir hönd Bogfimisambands Íslands.<br /><br />
            Ef þú hefur spurningar eða lendir í vandamálum með þessa viðbót hafðu samband við Bogfimisamband Íslands, <br />
            IANSEO's support-team getur ekki aðstoðað þig með spurningar tengt þessari viðbót.";
    $swelang_moduleversion = "Module version";
    $swelang_sendto = "Senda til";
    $swelang_moduleinformation = "
            Viðbótin gerir þér kleift að senda úrslit beint í íslenska úrslita gagnagrunninn.<br />
            Fylltu inn viðburðarnúmer og lykilorð og veldu svo prufa stillingar.<br />
            Sem svar færðu nafn viðburðarins og hvort hann leyfir innsendingu úrslita.<br />
            Veldu hvort senda á inn niðurstöður um undankeppni og/eða útsláttarkeppni og smelltu á senda hnappinn<br /><br />";
    $swelang_field = "Field";
    $swelang_description = "Lýsing";
    $swelang_eventnumber = "Viðburðarnúmer";
    $swelang_eventnumber_desc = "Númer viðburðar í íslenska úrslita gagnagrunninum";
    $swelang_password = "Lykilorð";
    $swelang_password_desc = "Lykilorð fyrir viðkomandi viðburð";
    $swelang_qualification = "Undankeppni";
    $swelang_qualification_desc = "Senda inn upplýsingar um undankeppni í úrslita gagnagrunn.";
    $swelang_final = "Úrslit";
    $swelang_final_desc = "Senda inn upplýsingar um útsláttarkeppni";
    $swelang_sendresult = "Senda inn úrslit";
    $swelang_testsettings = "Prufa stillingar";
    $swelang_settings = "Stillingar";
    $swelang_individ = "Einstaklinga";
    $swelang_send = "Senda";
    $swelang_noinformation_sent = "Engin gögn til þess að senda.";

    $swelang_notexported = "Not exported";
    $swelang_exported = "Exported";
    $swelang_warnings = "Warnings";

}	

$swe_url_test = $swe_host . "/api/CompetitionStatus";
$swe_url_result = $swe_host . "/api/CompetitionResults";

?>

