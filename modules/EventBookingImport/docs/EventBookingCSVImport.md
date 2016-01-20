#Event Booking CSV Import Module#

This module is designed to import the CSV Export from the Joomla Events Registration module that is published by <http://joomdonation.com/>.  The module is used to allow web based registration for tournaments.

###Documentation on performing the export
The vendor's support site details out conducting an export. The documentation is available at <http://eventbookingdoc.joomservices.com/registrants-management>.

The export will include custom fields that are defined in the Event Booking plugin.

###Format in current export
File contains a header row with column names.  Importer should be able to adjust to column order differences.

1. Event - string - Name of the event
1. "First Name" - string - First name of the archer
1. "Last Name" - string - Last name of the archer
1. "JOAD Club" - string - The club that the archer belongs to
1. NOTE: - string - 
1. Email - string - email address used to register
1. "Phone Number" - string - phone number used to register (format determined by registrant)
1. "Date Of Birth" - date in format yyyy-mm-dd - birth date of the archer
1. "USA Archery Membership #" - string - USA Archery membership number
1. Style - string - Style of shooting (Compound, Barebow, Recurve)
1. Gender - string - Gender of the archer (Male, Female)
1. "Age Division" - string - Age division that the archer believes that they are in
1. "Left or Right Handed" - string - Bow Hand (Right Handed, Left Handed)
1. "Target Preference" - string - (3Spot, Single40CM, Single60CM)
1. "Target Preference" - string - Unknown - null
1. "Subscribe to Colorado JOAD emails?" - string - Yes or No
1. Comment - string - 
1. #Registrants - integer - For our current purposes 1
1. Amount - double - Current tournament cost (25.00, likely not valid as we only allow one registrant per signup)
1. "Discount Amount" - double - Discount (0.00, likely not valid as we only use one registrant per signup)
1. "Gross amount" - double - Gross Amount (25.00, likely not valid as we only use one registrant per signup)
1. "Registration Date" - date in format mm-dd-yyyy - Date that the archer was registered
1. "Transaction ID" - string - Alpha numeric registration identifier (hash or other random sequence)
1. "Payment Status" - string - Payment Status (Not Paid)


The file is in a comma separated format.  If a text field has a space it will be in quotes.  Users can put spaces in their names and trimming would be recommended.


