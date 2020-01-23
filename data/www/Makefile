SYMFONY_CONSOLE=php bin/console

install: vendor doctrine
.PHONY: install

vendor:
	rm -rf vendor/
	composer install
.PHONY: vendor

doctrine:
	$(SYMFONY_CONSOLE) doctrine:schema:drop --full-database --force
	$(SYMFONY_CONSOLE) doctrine:migration:migrate --no-interaction
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction
.PHONY: doctrine
