.PHONY: *

.DEFAULT_GOAL := help
docker := $(shell if [ `pwd` != "/app" ]; then echo 'docker compose exec php'; fi;)

HELP_FUNCTION = \
	%help; \
	while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^(.+)\s*:.*\#\#(?:@(\w+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; \
	for (keys %help) { \
		print "\033[33m $$_:\n"; \
		printf "  \033[32m%-30s\033[0m %s\n", $$_->[0], $$_->[1] for @{$$help{$$_}} \
	}

help: ##@develop Show this help.
	@perl -e '$(HELP_FUNCTION)' $(MAKEFILE_LIST)

build: ##@develop Build docker container and detach
	docker compose up --build -d

up: ##@develop Docker compose up
	docker compose up -d

down: ##@develop Docker compose down
	docker compose down

sh: ##@develop Open shell in container
	${docker} sh

test: ##@develop Run code tests
	${docker} ./vendor/bin/php-cs-fixer fix --diff --dry-run

fix-cs: ##@develop Fix code styling
	${docker} ./vendor/bin/php-cs-fixer fix

rector: ##@develop Run rector
	${docker} ./vendor/bin/rector process

rector-diff: ##@develop Run rector dry mode
	${docker} ./vendor/bin/rector process --dry-run
