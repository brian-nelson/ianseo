# I@nseo

i@nseo is a software for managing archery tournaments results

This is an un-official repository, the official site for this software
is http://www.ianseo.net/

##### Brian Nelson's NOTE - Licenses for each project are in their
folders.

* IANSEO (`src`) - Licensed under Lesser GPLv3.
* LiveResultsPublisher (`utilities/LiveResultsPublisher`) - Licensed
  under MIT

At the time of the creation of this repository 10/17/2015 we were unable
to locate an repository from which to fork so this repo was created.

##### Ernesto Celis notes

Mine is a fork from Brian Nelson's `brian-nelson/ianseo` repository but
mixed with the official release, since Brian's repository seems
unmaintained.

This fork target is running I@nseo in Docker.

## How to use this image

Docker setup is out of scope.

First launch a MariaDB container.

```
docker run -d --name ianseodb -e MARIADB_USER=ianseo \
  -e MARIADB_DATABASE=ianseo -e MARIADB_PASSWORD=ianseo \
  -e MARIADB_ROOT_PASSWORD=ianseo mariadb:10
```

Now launch the ianseo container linked to the database container.

```
docker run -d --name ianseo --link ianseodb:mysql -p 8080:80 arqueria/ianseo
```

Browse to http://127.0.0.1:8080/ianseo/ and follow the instructions to
finish the installation.

In the **Step 2: Database connection data** of I@nseo has a default of
`localhost` for Database host, change it for the name of the MariaDB
container, `ianseodb` in the example above.

Fill the field for the **ADMIN Password to create users and databases**
with the value of the variable `MARIADB_ROOT_PASSWORD` of the MAriaDB
container, `ianseo` in the example above.

## Environment Variables

One of `MARIADB_ROOT_PASSWORD`, `MARIADB_ALLOW_EMPTY_ROOT_PASSWORD`, or
`MARIADB_RANDOM_ROOT_PASSWORD`, is required. The other environment
variables are optional.

**MARIADB_ROOT_PASSWORD / MYSQL_ROOT_PASSWORD**

This specifies the password that will be set for the MariaDB root
superuser account. In the above example, it was set to my-secret-pw.

**MARIADB_ALLOW_EMPTY_ROOT_PASSWORD / MYSQL_ALLOW_EMPTY_PASSWORD**

Set to a non-empty value, like yes, to allow the container to be started
with a blank password for the root user. NOTE: Setting this variable to
yes is not recommended unless you really know what you are doing, since
this will leave your MariaDB instance completely unprotected, allowing
anyone to gain complete superuser access.

**MARIADB_RANDOM_ROOT_PASSWORD / MYSQL_RANDOM_ROOT_PASSWORD**

Set to a non-empty value, like yes, to generate a random initial
password for the root user. The generated root password will be printed
to stdout (`GENERATED ROOT PASSWORD: .....`).

**MARIADB_DATABASE / MYSQL_DATABASE**

This variable allows you to specify the name of a database to be created
on image startup.

**MARIADB_USER / MYSQL_USER, MARIADB_PASSWORD / MYSQL_PASSWORD**

These are used in conjunction to create a new user and to set that
user's password. Both user and password variables are required for a
user to be created. This user will be granted all access (corresponding
to `GRANT ALL`) to the `MARIADB_DATABASE` database.

Do note that there is no need to use this mechanism to create the root
superuser, that user gets created by default with the password specified
by the `MARIADB_ROOT_PASSWORD` / `MYSQL_ROOT_PASSWORD` variable.

Refer to the MariaDB official repository for deeper information about
variable environments https://hub.docker.com/_/mariadb


## Build the docker image

```
docker build -t arqueria/ianseo .
```

