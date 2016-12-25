WORK_DIR=/var/www/html

.PHONY: update styling test setup
update:
	docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        php src/index.php
styling:
	docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        vendor/bin/php-cs-fixer fix -v
test:
	docker run --rm -it \
        -v $(PWD):$(WORK_DIR) \
        php:7.1-apache \
        vendor/bin/phpunit
setup:
	docker run --rm \
        -v $(PWD):/app \
        composer/composer:1.1-alpine install
