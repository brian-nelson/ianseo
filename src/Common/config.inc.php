<?php
// settings for the READ server
$CFG->R_HOST = $_ENV['IANSEO_R_HOST'];
$CFG->R_USER = $_ENV['IANSEO_R_USER'];
$CFG->R_PASS = $_ENV['IANSEO_R_PASS'];

// settings for the WRITE Server
$CFG->W_HOST = $_ENV['IANSEO_W_HOST'];
$CFG->W_USER = $_ENV['IANSEO_W_USER'];
$CFG->W_PASS = $_ENV['IANSEO_W_PASS'];

/* DB Name */
$CFG->DB_NAME = $_ENV['IANSEO_DB'];

// set the root directory
$CFG->ROOT_DIR = '/';


?>
