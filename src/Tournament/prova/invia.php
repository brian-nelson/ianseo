<?php
// preparo l'array che conterrà i dati da inviare via POST
// in questo caso c'è solo il file da trasmettere
$dati_post['file_dati'] = "@daspedire";

// inizializzo la sessione CURL
$ch = curl_init();

// imposto l'URL dello script destinatario
curl_setopt($ch, CURLOPT_URL, "http://ianseo/Tournament/prova/ricevi.php" );

// indico il tipo di comunicazione da effettuare (POST)
curl_setopt($ch, CURLOPT_POST, true );

// indico i dati da inviare attraverso POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $dati_post);

// specifico che la funzione curl_exec dovrà restituire l'output
// prodotto dall'URL contattato (destinatario.php)
// invece di inviarlo direttamente al browser
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// eseguo la connessione e l'invio dei dati e salvo in
// $postResult l'output prodotto dall'URL contattato
$postResult = curl_exec($ch);

// se ci sono stati degli errori mostro un messaggio esplicativo
if (curl_errno($ch)) {
	print curl_error($ch);
}

// chiudo la sessione CURL
curl_close($ch);

// mostro l'output prodotto da destinatario.php
echo $postResult;
?>