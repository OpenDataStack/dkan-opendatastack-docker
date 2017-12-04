## Summary

A Dockerfile for DKAN

- With Apache + php-fpm
- Without a database

## Setup

```
git clone git@github.com:OpenDataStack/dkan-opendatastack-docker.git && cd dkan-opendatastack-docker
```

### Mac / OS X

```
brew install docker
brew cask install docker
unset ${!DOCKER_*}
gem install docker-sync
```

### Linux

TODO

## Usage

### Setup

```
vendor/bin/robo setup
```

## Running

Don't pull from docker hub and run as it requires a MySQL/MariaDB server

```
docker-compose up -f docker-compose.yml
```

### Running - Development

- $WWW_UID and $WWW_GID : Optional to change the UID/GID that nginx runs under to
solve permission issues if you are mounting a directory from your host

Download DKAN Source:

```
git clone git@github.com:OpenDataStack/dkan-opendatastack-docker.git \
&& cd dkan-opendatastack-docker
```

#### Mac / OS X

```
docker-sync start
docker-compose up -f docker-compose.yml -f docker-compose.dev.mac.yml
docker-sync stop
docker-sync clean
```

#### Linux

```
docker-compose up -f docker-compose.yml -f docker-compose.dev.yml
```

# Development & Test Cycle

## Edit Dockerfile

Edit the Dockerfile, then:

```
docker rm dkan;
docker build -t dkan .;
docker run \
-p 8787:80 \
--name dkan dkan:latest;
```

## Commit and push:

```
docker login
vendor/bin/robo docker:push
```

## Debugging

Login:

```
docker-compose exec dkan_apache_php /bin/bash
docker-compose exec --user=www-data dkan_apache_php /bin/bash
docker-compose exec --user=www-data dkan_apache_php drush
```

Drush:

```
docker-compose exec --user=www-data dkan_apache_php drush
```

Copy files from the container:

```
docker cp dkan_apache_php:/etc/php/7.0/fpm/php.ini /PATH/TO/FILE
```

# DKAN

## Install DKAN

```
vendor/bin/robo dkan:install
```

## Drush

Drush commands must be prefixed with a '--' to pass through arguments

```
vendor/bin/robo dkan:drush upwd admin -- --password='admin'
```

# Mac / OSX Docker Sync

Fix sync by allowing write

```
chmod u+w src/dkan-opendatastack/src/assets/sites/default
```

Check Sync logs

```
docker logs dkan-starter-sync
```

Log into unison sync container

```
docker exec -it dkan-starter-sync /bin/bash
```
