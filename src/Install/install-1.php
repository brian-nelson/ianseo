<?php

/**

Step 1 dell'installazione: controllo della configurazione del PHP

  - memoria assegnata
  - tempo di esecuzione
  - peso dei file del post
  - peso massimo del singolo file
  - modulo mysqli
  - moduli CURL
  - moduli gd
  - moduli imagemagick
  - presentazione dello snippet ianseo.ini da mettere nella dir di
    configurazione del php con i dati che servono (memory, eccetera)

il mio EXT_SET è
[0] => zip
[1] => xmlwriter
[2] => libxml
[3] => xml
[4] => wddx
[5] => tokenizer
[6] => sysvshm
[7] => sysvsem
[8] => sysvmsg
[9] => session
[10] => SimpleXML
[11] => sockets
[12] => soap
[13] => SPL
[14] => shmop
[15] => standard
[16] => Reflection
[17] => posix
[18] => mime_magic
[19] => mbstring
[20] => json
[21] => iconv
[22] => hash
[23] => gettext
[24] => ftp
[25] => filter
[26] => exif
[27] => dom
[28] => dba
[29] => date
[30] => ctype
[31] => calendar
[32] => bz2
[33] => bcmath
[34] => zlib
[35] => pcre
[36] => openssl
[37] => xmlreader
[38] => cgi-fcgi
[39] => curl
[40] => gd
[41] => imagick
[42] => imap
[43] => ldap
[44] => mcrypt
[45] => mssql
[46] => mysql
[47] => mysqli
[48] => PDO
[49] => pdo_dblib
[50] => pdo_mysql
[51] => xmlrpc

ma dubito che siano tutti essenziali...
mettere in $ESS_EXT i moduli essenziali

**/



$INI_SET=ini_get_all(); // solo il dato attuale
$EXT_SET=get_loaded_extensions();
$ESS_EXT=array('mysqli', 'curl', 'gd', 'mbstring', 'iconv');

$INI_FAIL=false;
$MOD_FAIL=false;

echo '<tr><th class="Main" colspan="3">'.get_text('PHP settings','Install').'</th></tr>';
echo '<tr class="Divider"><td colspan="3"></td></tr>';

echo '<tr><th class="Title" colspan="3">'.get_text('php.ini file','Install').'</th></tr>';
echo '<tr class="head">';
echo '<th>'.get_text('Parameter','Install').'</th>';
echo '<th>'.get_text('Optimal value','Install').'</th>';
echo '<th>'.get_text('System value','Install').'</th>';
echo '</tr>';

// Check PHP version
print_row(PHP_VERSION, '5.0', '<b>'.get_text('PHP version','Install') . '</b>', intval(PHP_VERSION) < 5, true);

// se la versione è inferiore a 5 è inutile proseguire!!!
if(intval(PHP_VERSION) < 5) {
		echo '<tr><td colspan="3">'.get_text('PHP too old','Install').'<br/>'.get_text('Failing install','Install').'</td></tr>';
} else {

	// check della memoria assegnata
	$param=intval($INI_SET['memory_limit']['local_value']);
	switch(substr($INI_SET['memory_limit']['local_value'],-1)) {
		case 'G': $param*=1024;
		case 'M': $param*=1024;
		case 'K': $param*=1024;
	}
	print_row($INI_SET['memory_limit']['local_value'], '48M', 'memory_limit', $param<50331648);
	if($param<50331648) {
		$INI_FAIL=true;
		$_SESSION['INSTALL']['CFG']['MEMORY']='ini_set(\'memory_limit\',\'48M\');';
	}

	// check del tempo di esecuzione minimo
	$param=intval($INI_SET['max_execution_time']['local_value']);
	print_row($INI_SET['max_execution_time']['local_value'], 120, 'max_execution_time', $param < 120);
	if($param<120) {
		$INI_FAIL=true;
		$_SESSION['INSTALL']['CFG']['EXEC_TIME']='ini_set(\'max_execution_time\',\'120\');';
	}

	// check del peso massimo di upload
	$param=intval($INI_SET['post_max_size']['local_value']);
	switch(substr($INI_SET['post_max_size']['local_value'],-1)) {
		case 'G': $param*=1024;
		case 'M': $param*=1024;
		case 'K': $param*=1024;
	}
	print_row($INI_SET['post_max_size']['local_value'], '16M', 'post_max_size', $param<16777216);
	$INI_FAIL=($INI_FAIL or ($param<16777216));

	// check del peso massimo del singolo file
	$param=intval($INI_SET['upload_max_filesize']['local_value']);
	switch(substr($INI_SET['upload_max_filesize']['local_value'],-1)) {
		case 'G': $param*=1024;
		case 'M': $param*=1024;
		case 'K': $param*=1024;
	}
	print_row($INI_SET['upload_max_filesize']['local_value'], '16M', 'upload_max_filesize', $param<16777216);
	$INI_FAIL=($INI_FAIL or ($param<16777216));

	/**

	Sezione Moduli

	**/
	echo '<tr class="Divider"><td colspan="3"></td></tr>';
	// moduli essenziali: mysqli, curl, gd, imagick, mbstring, iconv
	echo '<tr><th class="Title" colspan="3">'.get_text('Loaded modules','Install').'</th></tr>';
	echo '<tr>';
	echo '<th>'.get_text('Module','Install').'</th>';
	echo '<th>'.get_text('Required','Install').'</th>';
	echo '<th>'.get_text('Status','Install').'</th>';
	echo '</tr>';
	foreach($ESS_EXT as $key) {
		print_row(in_array($key, $EXT_SET)?get_text('installed','Install'):get_text('missing','Install'), get_text('installed','Install'), $key, !in_array($key, $EXT_SET), true);
		$MOD_FAIL=($MOD_FAIL or !in_array($key, $EXT_SET));
	}

	if($MOD_FAIL) {
		// manca roba essenziale... non si prosegue
		echo '<tr><td colspan="3">'.get_text('Missing modules','Install').'<br/>'.get_text('Failing install','Install').'</td></tr>';
	} else {
		// si va avanti... ma magari si può sistemare il php.ini
		if($INI_FAIL) echo '<tr><td colspan="3">'.get_text('Suboptimal','Install').'</td></tr>';
		echo '<tr><th colspan="3"><a href="?step=2">'.get_text('Continue').'</a></th></tr>';
	}
}

function print_row($param, $min, $key, $test, $mandatory=false) {
	echo '<tr>';
	echo '<td class="Right">'.$key.'</td>';
	echo '<td class="Center">'.$min.'</td>';
	echo '<td style="background-color:'.($test?($mandatory?'#ffa0a0':'#ffd0a0'):'#a0ffa0').'"><b>'.$param.'</b></td>';
	echo '</tr>';
}
