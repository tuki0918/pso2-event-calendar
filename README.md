# scraper-pso2-event-calendar

[Google Calendar](https://calendar.google.com/calendar/embed?src=am384g4913d514u6lgdmcv8ces%40group.calendar.google.com&ctz=Asia/Tokyo)

### Requires

+ php: >= 7.1
+ service-account.json

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
