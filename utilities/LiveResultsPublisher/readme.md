# IANSEO Live Results Publisher

This windows application is designed to run on windows and push the results from the IANSEO MySql database to a S3 Bucket that is setup for static website hosting.

Since the IANSEO server has limited if any security it must be isolated on a independent network.  We would like parents and participants to be able to view the electronic results on their phones during the tournament.  This setup provides a simple low cost solution to publishing results to a public network.

The setup of the IANSEO tournament is assumed to be in compliance with the [setup document](https://github.com/brian-nelson/ianseo/blob/master/docs/ColoradoJOAD_Overview_of_IANSEO.docx).

## Overview of Operation

### Publishing Data
* SQL query is run against the database
* Results are transformed into a Hierarchical Json Document
* The document is pushed to a folder in a S3 Bucket. 

### Viewing Output
* Users navigate to the bucket and folder in their web browser
* The index.html page uses Bootstrap, JQuery, and Mustache templates to render the results to the screen.
* The page can pull down an update json file automatically or at a users request.
* Designed to be responsive for large and small screens
* Users can filter the results by division to limit results to shooters that they desire.  This is done locally on the json results.
* Auto-refresh automatically is disabled after 30 minutes from the last publication of results.  This is an attempt to not impact users when there is a break in publication, but to also stop web browsers from continuously pulling the file after the tournament has stopped publishing (is over). 


## Testing
On 04/22/2017 we ran a test of the publisher with a local tournament.  It worked very well.  One problem appeared in filters, but the problem ended up being that the class didn't have the correct gender assigned.  The publishing is very specific to the JOAD setup that we utilize.