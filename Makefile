container=app

init:
	docker-compose up -d
	docker-compose exec $(container) composer install
	sh init.sh

up:
	docker-compose up -d

down:
	docker-compose down

composer:
	docker-compose exec $(container) composer $(CMD)

bref:
	docker-compose exec $(container) vendor/bin/bref $(CMD)

sls:
	docker-compose exec $(container) serverless $(CMD)

test:
	docker-compose exec $(container) vendor/phpunit/phpunit/phpunit tests