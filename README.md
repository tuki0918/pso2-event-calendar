# scraper-pso2-event-calendar

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
