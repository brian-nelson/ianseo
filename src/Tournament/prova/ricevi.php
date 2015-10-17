<?php
// provo a salvare in "dati_ricevuti.txt" il file ricevuto
if (move_uploaded_file($_FILES['File']['tmp_name'], $_FILES['File']['name']))
{
	// se il salvataggio è andato a buon fine
	echo "Dati ricevuti con successo\n";
}
else
{
	// se c'è stato un probela
	echo "ERRORE! Problema nella ricezione dei dati";
}
?>