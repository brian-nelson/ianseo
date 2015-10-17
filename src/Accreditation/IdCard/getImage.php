<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	CheckTourSession(true);

	print_r($_COOKIE);

	$JS_SCRIPT=array(
		'<script type="text/javascript" src="Fun_AJAX_GetImage.js"></script>',
		'<script type="text/javascript" src="../Fun_JS.js"></script>',
		);

	$txtHeader="";
	$athId=(empty($_REQUEST['AthId']) || $_REQUEST['AthId']==0 ? null : $_REQUEST['AthId']);
	$sql="SELECT EnCode, EnFirstName, EnName FROM Entries WHERE EnId=" . StrSafe_DB($athId) . " AND EnTournament=" . StrSafe_DB($_SESSION['TourId']);

	$Rs=safe_r_sql($sql);
	if (safe_num_rows($Rs)==1)
	{
		$MyRow = safe_fetch($Rs);
		$txtHeader = $MyRow->EnCode . " - " . $MyRow->EnFirstName . " " . $MyRow->EnName;
	}

	$x=168;
	$y=78;
	$w=300;

	$camurl=(empty($_REQUEST['CamUrl']) ? "" : urldecode($_REQUEST['CamUrl']));
	if(!$camurl) $camurl=$_COOKIE['CamUrl'];
	$boundary="\n--";

	$f = @fopen($camurl,"r") ;

	if($camurl and $f) {
		$r="";
		$im = null;
		if(preg_match('/\.jpg$/i',$camurl) != 0) {
			while(!feof($f))
				$r .= fread($f,4096);
			$im=imagecreatefromstring($r);
		} else {
			while (substr_count($r,"Content-Length") != 2)
				$r.=fread($f,512);
			$start = strpos($r,chr(255));
			$end   = strpos($r,$boundary,$start)-1;

			$frame = substr("$r",$start,$end - $start);
			$im=imagecreatefromstring($frame);
		}

		$X=imagesx($im);
		$Y=imagesy($im);
		$w=intval($X*0.45);
		$x=($X-$w)-2;
		$y=$Y-intval($w*4/3)-2;
	}

	$ONLOAD=(' onLoad="javascript:reloadPicture()"');
	include('Common/Templates/head-popup.php');
?>

<table class="Tabella">
	<tr><th colspan="2"><?php  echo $txtHeader; ?></th></tr>
	<tr>
	<td width="80%" class="Center"><img id="imgGrabbed" src="./grabImage.php" alt="Grab" onClick="javascript:centerBox(event);"/></td>
	<script type="text/javascript">
	<!--
		var myImg = document.getElementById("imgGrabbed");
		myImg.onmousedown = GetCoordinates;
	//-->
	</script>
	<td width="20%" class="Center">
	<table class="Tabella">
		<tr>
			<td colspan="2" class="Center"><a href="javascript:moveBox('N');">Su</a></td>
		</tr>
		<tr>
			<td class="Center"><a href="javascript:moveBox('W');">Sx</a></td>
			<td class="Center"><a href="javascript:moveBox('E');">Dx</a></td>
		</tr>
		<tr>
			<td colspan="2" class="Center"><a href="javascript:moveBox('S');">Gi&ugrave;</a></td>
		</tr>
		<tr>
			<td class="Center"><a href="javascript:moveBox('I');">Zoom -</a></td>
			<td class="Center"><a href="javascript:moveBox('O');">Zoom +</a></td>
		</tr>
		<tr>
			<td class="Center"><a href="javascript:moveBox('II');">Zoom ---</a></td>
			<td class="Center"><a href="javascript:moveBox('OO');">Zoom +++</a></td>
		</tr>
		
		
		<tr>
			<td colspan="2" class="Center"><a href="javascript:moveBox('GET');">Scatta</a></td>
		</tr>
	</table>
	<br/>
	<input type="button" value="Chiudi" onClick="javascript:ReloadOpener(true);"/>
	</td>
	</tr>
</table>
<input type="hidden" id="valueX" value="<?php echo (empty($_COOKIE["getPhotoX"]) ? $x : $_COOKIE["getPhotoX"]) ?>" />
<input type="hidden" id="valueY" value="<?php echo (empty($_COOKIE["getPhotoY"]) ? $y : $_COOKIE["getPhotoY"]) ?>" />
<input type="hidden" id="valueW" value="<?php echo (empty($_COOKIE["getPhotoW"]) ? $w : $_COOKIE["getPhotoW"]) ?>" />
<input type="hidden" id="getSnap" value="0" />
<?php
echo '<input type="hidden" id="AthId" value="' . (!empty($_REQUEST["AthId"]) ? $_REQUEST["AthId"]:0) . '" />';

include('Common/Templates/tail-popup.php');
?>