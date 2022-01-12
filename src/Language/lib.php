<?php
function save_lang_files($file, $text) {
	if(file_put_contents($file . ".new", $text)) {
		// swap old and new
		chmod($file . ".new", 0666);
		@unlink($file . '.old');
		@rename($file, $file . '.old');
		@unlink($file);
		rename($file . '.new', $file);
		return true;
	}
	return false;
}
?>
