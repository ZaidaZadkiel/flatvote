#!/usr/bin/make

SCRIPT_VERSION=v1.0
SCRIPT_AUTHOR=ZaidaZadkiel
#based on the work by Antonio Miguel Morillo Chica

RELEASE_DIR = ./dist/ #local
DOCKER_REACT_PATH = /app/cms/ #path within the docker for running npm
LOCAL_REACT_PATH = ./website/cms/build/* #local build output
LOCAL_API_PATH = ./website/backend/api #local build output

HELP_FUN = \
    %help; while(<>){ \
   		push@{$$help{$$2//'options'}},[$$1,$$3] \
    	if/^([\w-_]+)\s*:.*\#\#(?:@(\w+))?\s(.*)$$/ \
	}; \
    print"$$_:\n", map"  $$_->[0]".(" "x(20-length($$_->[0])))."$$_->[1]\n",\
    @{$$help{$$_}},"\n" for keys %help; \

help: ##@Miscellaneous Show this help
	@echo "Usage: make [target] ...\n"
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
	@echo "Written by $(SCRIPT_AUTHOR), version $(SCRIPT_VERSION)"
	@echo "Please report any bug or error to the author."

stopallcontainers: ##@Container stop all running docker containers
	docker stop $(docker ps -a -q)

run: ##@Container Build and run php container
	docker compose -f ./docker-compose.dev.yml up -d --build

build: ##@Container Build php container
	docker compose -f ./docker-compose.dev.yml build

stop: ##@Container Stop php container
	docker compose -f ./docker-compose.dev.yml down

destroy: ##@Container Remove all data related with php container
	docker compose -f ./docker-compose.dev.yml down --rmi all

dist: ##@Container SHH in container
	mkdir -p $(RELEASE_DIR)
	docker exec cms sh -c "cd $(DOCKER_REACT_PATH) && npm run build"
	cp $(LOCAL_REACT_PATH) $(RELEASE_DIR) -r
	cp $(LOCAL_API_PATH) $(RELEASE_DIR) -r
	cp misc/.htaccess dist/

shell: ##@Container SHH in container
	docker exec -ti ${name} "/bin/bash"

logs: ##@Container Show logs in container
	docker compose -f ./docker-compose.dev.yml logs --follow

lint: ##@Style Show style errors
	docker compose -f ./docker-compose.dev.yml exec php composer lint

lint-fix: ##@Style Fix style errors
	docker compose -f ./docker-compose.dev.yml exec php composer lint:fix

test: ##@Tests Execute tests
	docker compose -f ./docker-compose.dev.yml exec php composer test

test-coverage: ##@Tests Execute tests with coverage
	docker compose -f ./docker-compose.dev.yml exec php composer test:coverage

exec: ##@Code Execute the code index
	docker compose -f ./docker-compose.dev.yml exec php composer exec

include .env
mysql: ##@Code Run mysql client for the projects database (password set in .env)
	docker exec -ti fvdatabase mysql -u ${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE}

include .env
dumpdb: ##@Code Dump all databases schema and data from docker (pass in .env)
	@docker exec fvdatabase mysqldump -d -u ${MYSQL_USER} -p${MYSQL_PASSWORD} --databases ${MYSQL_DATABASE}
