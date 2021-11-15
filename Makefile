init: down \
	api-clear frontend-clear \
	docker-build up \
	api-init frontend-init
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
