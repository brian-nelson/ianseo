# IANSEO

Archery Tournament Results Management

This is an un-official repository for the ianseo software.  This software
is used for managing archery tournaments.  The official site for this
software is http://www.ianseo.net/index.php.  

##### NOTE - Licenses for each project are in their folders.

* IANSEO (`src`) - Licensed under Lesser GPLv3.
* LiveResultsPublisher (`utilities/LiveResultsPublisher`) - Licensed under
  MIT

At the time of the creation of this repository 10/17/2015 we were unable
to locate an repository from which to fork so this repo was created.

##### Notes by ecelis

Mine is a fork from `brian-nelson/ianseo` but mixed with the official
release, since there seem to be missing some files in Brian's
repository. So it will be a mess until I figure out why and somehow get
in sync with the original sources.

This fork is also focused on running ianseo in Docker.

## How to use this image

Docker setup is out of scope.

First launch a MariaDB (MySQL) container. We will mount a host's
directory to keep the database files.

```
docker run -d --name ianseodb -e MARIADB_ROOT_PASSWORD=my-secret-pw mariadb:10
```

Now launch the ianseo container linked to the database container. Some
environment variables are required, you can either pass them inline with
the -e flag or write them to a file, like in the example below.

```
docker run -d --name ianseo --link ianseodb:mysql -p 8080:80 arqueria/ianseo
```

Browse to http://127.0.0.1:8080/ianseo/

## Environment Variables

One of `MARIADB_ROOT_PASSWORD`, `MARIADB_ALLOW_EMPTY_ROOT_PASSWORD`, or `MARIADB_RANDOM_ROOT_PASSWORD` (or equivalents, including `*_FILE`), is required.

**MARIADB_ROOT_PASSWORD / MYSQL_ROOT_PASSWORD**

This specifies the password that will be set for the MariaDB `root` superuser account. In the above example, it was set to `my-secret-pw`.

To learn more about MariaDB optional environment variables go here https://hub.docker.com/_/mariadb. 


## Build the docker image

```
docker build -t arqueria/ianseo
```
