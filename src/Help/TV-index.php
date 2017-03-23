<?php

switch(true) {
	case (!empty($_REQUEST['ChainType'])):
		echo get_text('TV-RotEdit-'.$_REQUEST['ChainType'], 'Help');
		break;
	case (!empty($_REQUEST['edit'])):
		echo get_text('TV-RotEdit', 'Help');
		break;
	default:
		echo get_text('TV-RotList', 'Help');
		break;
}