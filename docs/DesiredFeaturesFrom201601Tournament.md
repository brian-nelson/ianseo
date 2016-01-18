#Notes for new features from usage in January 2016 tournament#

1. Addition of the ability to delete all participants.  Sometimes the import would overwrite correctly, but in one case the 180ish participants had to be deleted row by row.
1. Add the ability to dump an Excel template of the participant load format.  This would assist new users loading data.
1. Determine the correct import settings for the Participant flags on import (T/F. Y/N, etc.)
1. The TV Output would fail periodically and need to be manually refreshed.  It is suspected that the problem is that there is a screen refresh on a timer, and that this fails randomly.  Suggestion - Change the user screen to use a Javascript ajax (type) call to allow the page to refresh without reloading.  This would allow the error to be caught and not leave the page with no contents.
1. Create a screen that allows quick manual entry of participants.   Suggestion - Create a table where user can tab and enter with text boxes and appropriate combo boxes.
1. Create a proper english language manual for the app, the existing manual is incomplete and poorly translated
1. Create a feature to allow for copying of prior tournament (with or without participants).
1. Add a hyperlink to jump from the Participant row on the Input score table to the Arrow by Arrow entry. 