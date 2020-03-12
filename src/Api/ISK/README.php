<?php

/**

************************
** BASE CONFIGURATION **
************************
File ApiConfig.php gets included in the competition details page
sets up 2 module variables:
- ServerUrl
- Mode


IskDvState will hold the following numbers and explanations
0 => device not used
1 => device is OK and configured
2 => device is waiting to send code
3 => code has been sent, waiting for the confirmation


*****************
** Results.php **
*****************
AUTOIMPORT
Stops the autoimport facility, when page is closed reactivates it automagically
the flag is defined in getModuleParameter('ISK', 'StopAutoImport').
if not present or false (default), the autoimport is ON
If true, the autoimport features is OFF (only manual import is aloud)

STICKY ENDS
While the page is loaded the operator can stick users to score only in certain ends:
getModuleParameter('ISK', 'StickyEnds', array('SeqCode'=>$Sequence, 'Distance'=>$Dist, 'Ends'=>array()));

Ends is an array 1-based of ends to stick to.
If the parameter is not there or Ends is an empty array then no sticky ends at all, otherwise ONLY the ends of that distance and that session


TODO: sistemare il conteggio delle frecce tra elim e fin basando sul ln(2) della fase... cercare dove è stato già usato

*/