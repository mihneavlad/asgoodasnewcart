.PHONY: install run test migrate

install:
	composer install
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction

run:
	php -S localhost:8000 -t public/

migrate:
	php bin/console doctrine:migrations:migrate --no-interaction

test:
	php bin/phpunit --testdox
