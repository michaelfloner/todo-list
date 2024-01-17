init:
	cp -n .env.dist .env
	docker-compose up -d --build
	docker compose exec php-fpm composer install
	docker compose exec php-fpm composer migrate

up:
	docker-compose up -d
	docker compose exec php-fpm bash

down:
	docker compose stop

phpstan:
	docker compose exec php-fpm composer phpstan

test:
	docker compose exec php-fpm composer test-local
