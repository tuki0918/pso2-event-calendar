WORK_DIR=/var/www/html

.PHONY: update styling test setup
update:
	/usr/local/bin/docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        php src/index.php
styling:
	/usr/local/bin/docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        vendor/bin/php-cs-fixer fix -v
test:
	/usr/local/bin/docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        vendor/bin/phpunit
setup:
	/usr/local/bin/docker run --rm \
        -v $(PWD):/app \
        composer/composer:1.1-alpine install
