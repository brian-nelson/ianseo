<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

echo "var WebDir='$CFG->ROOT_DIR';\n\n";

require_once('Fun_AJAX.js');
?>