init: down up api-init
up:
	docker-compose pull
	docker-compose up -d --build
up-not-demon:
	docker-compose up --build
down:
	docker-compose down --remove-orphans

api-init: api-composer-install

api-composer-install:
	docker-compose run --rm api-php-cli composer install