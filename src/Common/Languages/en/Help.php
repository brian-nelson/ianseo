<?php
$lang['AutoImportSettings']='<b>Only for Expert Users</b><br>Changing the default behavior should result in inaccurate results.<br>
It is important to recalculate all the ranks that has been setup as "manually" BEFORE sending to ianseo.net or printing  results and in general before every distribution of any kind.';
$lang['ChangeComponents']='<p>To proceed with a change first remove the athletes that is not in the team any more in order to activate the possible options.</p>
&#9654&nbsp;Score included in the total of team qualification round<br>
&#9655&nbsp;Score not included in the total of team qualification round';
$lang['GetBarcodeSeparator']='After printing the barcodes reference sheet, read the «SEPARATOR» barcode in order to activate the correct reader items.';
$lang['HomePage']='This is the page where you can select or create a tournament.';
$lang['ISK-LockedSessionHelp']='{$a} icons show if the app can score or not in that session.';
$lang['ISK-ServerUrlPin']='<b>DO NOT SHARE THIS NUMBER</b><br>Use a PIN of your choice (4 Numeric Digits) to be used to access your competition.<br>
Devices can score in your competition only reading the QR-Code printed by IANSEO.<br>
In case of manual input in Ianseo Scorekeeper LITE app, the Competition code to use is <b>{$a}</b>';
$lang['ScoreBarCodeShortcuts']='Read the barcode printed on the scorecard.<br/>
Inserting manually a # followed by the name of the athlete searches the database to find that athlete<br/>
Inserting a @ followed by a target number searches for that target. Distance MUST be set. Session should be specified (first digit) and target is 0-padded to 3 digits.';
$lang['TV-ChannelSetup']='= Channels Setup =
After setting up your channels as desired, connect the browser of the device you want to link to a channel to

<code>http://IP.OF.IANSEO/tv.php?id=CHANNEL</code>

where \'\'\'IP.OF.IANSEO\'\'\' is the IP where ianseo is running (including the directory if any) and \'\'\'CHANNEL\'\'\' is the ID of the channel';
$lang['TV-RotEdit']='<div>A presentation page is made of at least one content page.</div>
<div>The content pages will then be shown one after the other and start over again.</div>
<div><b>NOTE:</b> in regular and light version engines, the first content is again shown as last, so it is wise to insert as first content an image (logo of the competition for example).</div>
<div>Contents can be either competition-based (start list, qualification, matches...) or "multimedia" (images, HTML messages,...).</div>';
$lang['TV-RotEdit-DB']='<h2>CSS3 management (Advanced engine)</h2>
<h3>Length Units</h3>
<ul>
<li><b>rem:</b> heigth of the root character</li>
<li><b>em:</b> heigth of the current character</li>
<li><b>ex:</b> heigth of the lowercase "x"</li>
<li><b>ch:</b> width of the number "0"</li>
<li><b>vh:</b> 1/100th of the height of the screen</li>
<li><b>vw:</b> 1/100th of the width of the screen</li>
<li><b>vmin:</b> 1/100th of the minimum value between the height and the width of the screen</li>
<li><b>vmax:</b> 1/100th of the maximum value between the height and the width of the screen</li>
</ul>
<h3>Flexible Boxes</h3>
<li><b>flex A B C:</b>
  <ul>
  <li><b>A</b>: if 0 means the box can not expand; if >1 means the box can expand at that "speed" compared to other boxes (if box 1 has 2 and box 2 has 3, box 2 will expand 1.5 more than box 1 which in turn will expan double as much as a box with this value set to 1)</li>
  <li><b>B:</b> if 0 the box cannot shrink; if 1 box can shrink</li>
  <li><b>C:</b> initial dimention of the box</li>
  </ul>
  </li>
<h3>CSS reference</h3>
<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/Reference">https://developer.mozilla.org/en-US/docs/Web/CSS/Reference</a>';
$lang['TV-RotList']='<div>This is the list of available presentation pages to send to videowall, moitors or broadcast.</div>
<div>3 different engines are provided, click on the link to activate:</div>
<ul>
<li>a regular engine compatible with most browsers</li>
<li>a light version engine compatible with most browsers but uses less resources</li>
<li>an advanced version that uses modern browsers HTML5 capabilities</li>
</ul>
<div>To create a new content, enter a name for it and press the button.</div>';
?>