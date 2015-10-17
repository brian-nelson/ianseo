<?php

$terne=array();
$max=2;
$step=floor(128/$max);
$start=255-($step*$max);

for($r=0; $r<=$max; $r++) {
	for($g=0; $g<=$max; $g++) {
		for($b=0; $b<=$max; $b++) {
			$terne[]=array($start + ($step*$r), $start + ($step*$g), $start + ($step*$b));
		}
	}
}

shuffle($terne);

foreach($terne as $val){
	echo "<div style=\"background-color:rgb($val[0],$val[1],$val[2])\">array($val[0], $val[1], $val[2]),</div>";
}

?>