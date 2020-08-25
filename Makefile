init: down \
	api-clear \
	pull build up \
	api-init \
	frontend-init
up:
	docker-compose up -d
up-not-demon:
	docker-compose up
down:
	docker-compose down --remove-orphans
pull:
	docker-compose pull
build:
	docker-compose build

api-init: api-permissions api-composer-install api-wait-db api-migration-migrate api-fixtures

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/*'

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'chmod -R 777 var/cache var/log var/doctrine/'

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

# Tests
test: api-test api-fixtures
test-functional: api-test-functional api-fixtures
test-unit: api-test-unit

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
# Fixtures
api-fixtures:
	docker-compose run --rm api-php-cli composer console fixtures:load


# frontend
frontend-init: frontend-yarn-install

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install
