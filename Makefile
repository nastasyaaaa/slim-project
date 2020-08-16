init: down build up api-init api-clear api-permissions api-migration-migrate
up:
	docker-compose pull
	docker-compose up -d
up-not-demon:
	docker-compose up
down:
	docker-compose down --remove-orphans
build:
	docker-compose build

api-init: api-composer-install

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/*'

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'chmod -R 777 var/cache var/log'


# Tests
api-test:
	docker-compose run --rm api-php-cli composer test
api-test-unit:
	 docker-compose run --rm api-php-cli composer test -- --filter=Unit
api-test-functional:
	 docker-compose run --rm api-php-cli composer test -- --filter=Functional
api-test-unit-coverage:
	 docker-compose run --rm api-php-cli composer test -- --filter=Unit --coverage-html var/coverage
api-test-functional-coverage:
	 docker-compose run --rm api-php-cli composer test -- --filter=Functional --coverage-html var/coverage

# Migrations
api-migration-diff:
	docker-compose run --rm api-php-cli composer console migrations:diff -- --check-database-platform=1
api-migration-migrate:
	docker-compose run --rm api-php-cli composer console migrations:migrate -- --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli composer console fixtures:load