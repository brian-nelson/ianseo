# IANSEO

Archery Tournament Results Management

This is an un-official repository for the ianseo software.  This software
is used for managing archery tournaments.  The official site for this
software is http://www.ianseo.net/index.php.  

##### NOTE - Licenses for each project are in their folders.

* IANSEO (src) - Licensed under Lesser GPLv3.
* LiveResultsPublisher (utilities/LiveResultsPublisher) - Licensed under
  MIT

At the time of the creation of this repository 10/17/2015 we were unable
to locate an repository from which to fork so this repo was created.

This repo has been updated with the 07/01/2019 IANSEO source.


### Goal of this Repository

This repo is currently focused on developing utilities and extensions to
the base IANSEO software.  See the utilities and modules section for the
extensions that are currently  being developed.

### Note by ecelis

My fork is a fork from `brian-nelson/ianseo` but mixed with the official
release, since there seem to be missing some files in Brian's
repository. So it will be a mess until I figure out why and somehow get
in sync with the original sources.

## Set up

In order to run ianseo in docker and keep secrets safe some
configuration values are read from environment variables on the host
running the software.

**IANSEO_R_HOST**, Read host
**IANSEO_R_USER**, Read user
**IANSEO_R_PASS**, Read password
**IANSEO_W_HOST**, Write host
**IANSEO_W_USER**, Read user
**IANSEO_W_PASS**, Read password
**IANSEO_DB**, The database name

You can copy the `env.sample` file to `.env` and edit it to suit your
environment.


### Docker

Docker setup is out of scope.

First launch a MariaDB (MySQL) container. We will mount a host's
directory to keep the database files and make available the install.sql
included with ianseo to the database engine so it gets executed upon
creation of the database.

```
docker run -d --name ianseodb \
    -e MYSQL_ROOT_PASSWORD=verysecret \
    -e MYSQL_DATABASE=ianseo \
    -e MYSQL_USER=ianseo \
    -e MYSQL_PASSWORD=ianseo \
    -v /srv/ianseo:/var/lib/mysql \
    -v "$(pwd)/src/Install":/docker-entrypoint-initdb.d \
    -p 3306:3306 \
    mariadb:5.5
```

Now launch the ianseo container linked to the database container. Some
environment variables are required, you can either pass them inline with
the -e flag or write them to a file, like in the example below.

```
docker run -it --name ianseo \
    --link ianseodb:mysql \
    --env-file=.env \
    -p 8080:80 arqueria/ianseo
```

Browse to http://127.0.0.1:8080/ianseo/
