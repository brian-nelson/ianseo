</div>
<div class="modal"></div>

<?php
// 	$mid->printFooter();
	if(!empty($GLOBALS['ERROR_REPORT'])) {
		$deb="<div id=\"Debug\">\n\n";
		$deb.="<table align=left bgcolor=\"#e0e0e0\" border=1 cellspacing=0 class=\"print_debug\">\n<tr><th bgcolor=\"#d0d0d0\">DEBUG</th></tr>";
		$deb.="<tr>\n\t<td style=\"font-size:xx-small;font-family:arial,sans-serif;color:black\">\n";
		$deb.="pagina generata in ".round(getmicrotime()-$GLOBALS['tempo'],3)." secondi<br>\n";
		$deb.="memoria consumata dallo script: ".memory_get_usage(true)."<br>\n";
		$deb.="==== SAFE_SQL ====<br>\n";
		foreach($GLOBALS['safe_SQL'] as $key=>$val) {
			$deb.=deb_rec($key,$val);
		}
		$deb.="==== _SERVER ====<br>\n";
		foreach($_SERVER as $key=>$val) {
			$deb.=deb_rec($key,$val);
		}
		$deb.="==== _SESSION ====<br>\n";
		foreach($_SESSION as $key=>$val) {
			$deb.=deb_rec($key,$val);
		}
		$deb.="==== CFG ====<br>\n";
		foreach($CFG as $key=>$val) {
			$deb.=deb_rec($key,$val);
		}

		$deb.="</td>\n</tr>\n</table></div>";

		echo $deb;
	}

	if(!empty($POST_TAIL)) echo $POST_TAIL;
?>
</body>
</html>