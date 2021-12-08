init: down \
	api-clear frontend-clear cucumber-clear \
	docker-build up \
	frontend-init cucumber-init
up:
	docker-compose up -d
up-not-demon:
	docker-compose up
down:
	docker-compose down --remove-orphans
docker-build:
	docker-compose build --pull

api-init: api-permissions api-composer-install api-wait-db api-migration-migrate api-fixtures
validate-schema: api-validate-schema

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* || true'

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'chmod -R 777 var/cache var/log var/doctrine/ || true'

api-wait-db:
	docker-compose run --rm api-php-cli wait-for-it api-postgres:5432 -t 30

api-validate-schema:
	docker-compose run --rm api-php-cli composer console orm:validate-schema

# production
build-buildx: build-gateway-buildx build-api-buildx build-frontend-buildx

build-gateway-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-gateway:${IMAGE_TAG} gateway/docker
build-api-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=api/docker/production/postgres/Dockerfile --tag=${REGISTRY}/auction-api-postgres:${IMAGE_TAG} api
build-frontend-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

build: build-gateway build-api build-frontend

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-gateway:${IMAGE_TAG} gateway/docker
build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/auction-api-php-cli:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/postgres/Dockerfile --tag=${REGISTRY}/auction-api-postgres:${IMAGE_TAG} api
build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/nginx/Dockerfile --tag=${REGISTRY}/auction-frontend:${IMAGE_TAG} frontend

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

# Push production images to registry
push: push-gateway push-api push-frontend

push-gateway:
	docker push ${REGISTRY}/auction-gateway:${IMAGE_TAG}
push-api:
	docker push ${REGISTRY}/auction-api:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY}/auction-api-postgres:${IMAGE_TAG}
push-frontend:
	docker push ${REGISTRY}/auction-frontend:${IMAGE_TAG}

# Testing environment
testing-build-buildx: testing-build-gateway-buildx testing-build-testing-api-php-cli-buildx testing-build-testing-cucumber-buildx
testing-build: testing-build-gateway testing-build-testing-api-php-cli testing-build-testing-cucumber
try-testing: try-build try-testing-build try-testing-init try-testing-smoke try-testing-e2e try-testing-down-clear

testing-build-gateway-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=gateway/docker/testing/nginx/Dockerfile --tag=${REGISTRY}/auction-testing-gateway:${IMAGE_TAG} gateway/docker
testing-build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/testing/nginx/Dockerfile --tag=${REGISTRY}/auction-testing-gateway:${IMAGE_TAG} gateway/docker

testing-build-testing-api-php-cli-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=api/docker/testing/php-cli/Dockerfile --tag=${REGISTRY}/auction-testing-api-php-cli:${IMAGE_TAG} api
testing-build-testing-api-php-cli:
	docker --log-level=debug build --pull --file=api/docker/testing/php-cli/Dockerfile --tag=${REGISTRY}/auction-testing-api-php-cli:${IMAGE_TAG} api

testing-build-testing-cucumber-buildx:
	docker --log-level=debug buildx build --platform linux/x86_64 --pull --file=cucumber/docker/testing/node/Dockerfile --tag=${REGISTRY}/auction-testing-cucumber-node-cli:${IMAGE_TAG} cucumber
testing-build-testing-cucumber:
	docker --log-level=debug build --pull --file=cucumber/docker/testing/node/Dockerfile --tag=${REGISTRY}/auction-testing-cucumber-node-cli:${IMAGE_TAG} cucumber

testing-init:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml up --build -d
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml run --rm api-php-cli wait-for-it api-postgres:5432 -t 60
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml run --rm testing-api-php-cli composer console migrations:migrate -- --no-interaction
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml run --rm testing-api-php-cli composer console fixtures:load -- --no-interaction

testing-smoke:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml run --rm cucumber-node-cli yarn smoke
testing-e2e:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml run --rm cucumber-node-cli yarn e2e


testing-down-clear:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yaml down --remove-orphans

try-testing-build:
	REGISTRY=localhost IMAGE_TAG=0 make testing-build-buildx

try-testing-init:
	REGISTRY=localhost IMAGE_TAG=0 make testing-init

try-testing-smoke:
	REGISTRY=localhost IMAGE_TAG=0 make testing-smoke

try-testing-e2e:
	REGISTRY=localhost IMAGE_TAG=0 make testing-e2e

try-testing-down-clear:
	REGISTRY=localhost IMAGE_TAG=0 make testing-down-clear

# Tests
test: api-test api-fixtures
test-functional: api-test-functional api-fixtures
test-unit: api-test-unit
test-smoke: api-fixtures cucumber-clear cucumber-smoke
test-e2e:
	make api-fixtures
	make cucumber-clear
	# ignore errors, see makefile specification
	- make cucumber-e2e
	make cucumber-report

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

# Cucumber
cucumber-e2e:
	docker-compose run --rm cucumber-node-cli yarn e2e
cucumber-smoke:
	docker-compose run --rm cucumber-node-cli yarn smoke
cucumber-clear:
	docker run --rm -v ${PWD}/cucumber:/app -w /app alpine sh -c 'rm -rf var/*'
cucumber-report:
	docker-compose run --rm cucumber-node-cli yarn report

# Migrations
api-migration-diff:
	docker-compose run --rm api-php-cli composer console migrations:diff -- --check-database-platform=1
api-migration-migrate:
	docker-compose run --rm api-php-cli composer console migrations:migrate -- --no-interaction
# Fixtures
api-fixtures:
	docker-compose run --rm api-php-cli composer console fixtures:load


# frontend
frontend-init: frontend-yarn-install frontend-ready

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install
frontend-ready:
	docker-compose run --rm frontend-node-cli touch .ready
frontend-clear:
	docker-compose run --rm frontend-node-cli rm -rf .ready build

# jenkins
validate-jenkins:
	curl -u ${USER} -X POST -F "jenkinsfile=<Jenkinsfile" ${HOST}/pipeline-model-converter/validate

# cucumber:
cucumber-init: cucumber-yarn-install

cucumber-yarn-install:
	docker-compose run --rm cucumber-node-cli yarn install

deploy:
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'mkdir site_${BUILD_NUMBER}'
	scp -P ${PROD_SSH_PORT} docker-compose-production.yaml deploy@${PROD_HOST}:site_${BUILD_NUMBER}/docker-compose.yaml
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=auction" >> .env'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'cd site_${BUILD_NUMBER} && docker-compose pull'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'cd site_${BUILD_NUMBER} && docker-compose up --build --remove-orphans -d'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'rm -f site'
	ssh deploy@${PROD_HOST} -p ${PROD_SSH_PORT} 'ln -sr site_${BUILD_NUMBER} site'
