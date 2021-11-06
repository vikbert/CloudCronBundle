SHELL := /bin/bash

help:
	# shellcheck disable=SC2046
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$|(^#--)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m %-43s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m #-- /[33m/'

.PHONY: help
.DEFAULT_GOAL := help

#-- project
start: ## start the application
	composer install
	make db-migrate
	make db-fixtures
	php bin/console cron:watch

#-- db
db-reset: ## reset the db
	make db-migrate
	make db-fixtures

db-fixtures: ## load data fixtures
	php bin/console doctrine:fi:load

db-migrate: ## doctrine migrate
	php bin/console doctrine:migrations:migrate -n
