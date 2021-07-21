<?php

$wiky=new wiky;

// Call for the function parse() on the variable You created and pass some unparsed text to it, it will return parsed HTML or false if the content was empty. In this example we are loading the file input.wiki, escaping all html characters with htmlspecialchars, running parse and echoing the output
$input=get_text('TV-ChannelSetup', 'Help');

//$input=htmlspecialchars($input);
echo $wiky->parse($input);

