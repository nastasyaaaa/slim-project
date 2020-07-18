init: down up api-init api-clear
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

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/*'


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
