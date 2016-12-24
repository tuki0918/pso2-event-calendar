# scraper-pso2-event-calendar

[![Build Status](https://travis-ci.org/tuki0918/scraper-pso2-event-calendar.svg?branch=master)](https://travis-ci.org/tuki0918/scraper-pso2-event-calendar)
[![Coverage Status](https://coveralls.io/repos/github/tuki0918/scraper-pso2-event-calendar/badge.svg)](https://coveralls.io/github/tuki0918/scraper-pso2-event-calendar)

[Google Calendar](https://goo.gl/JWExl7)

### Requires

+ php: >= 7.1
+ src/.env
+ src/service-account.json

### Usage

+ Calendar Update

```
docker run --rm -it \
  -v $(pwd):/var/www/html \
  php:7.1-apache \
  php src/index.php
```

+ Fix Coding Style

```
docker run --rm -it \
  -v $(pwd):/var/www/html \
  php:7.1-apache \
  vendor/bin/php-cs-fixer fix -v
```

+ Unit Test

```
docker run --rm -it \
  -v $(pwd):/var/www/html \
  php:7.1-apache \
  vendor/bin/phpunit
```

### Composer

+ Packages Install

:mag: PHP Version in Image.

```
docker run --rm \
  -v $(pwd):/app \
  composer/composer:1.1-alpine install
```
