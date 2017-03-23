<?php

if(empty($_SESSION['BarCodeSeparator'])) {
	echo get_text('GetBarcodeSeparator', 'Help');

} else {
	echo get_text('ScoreBarCodeShortcuts', 'Help');

}

