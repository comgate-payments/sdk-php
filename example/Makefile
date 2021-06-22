.PHONY: qa dev cs cfx phpstan publish

qa: cs phpstan

cs:
	vendor/bin/codesniffer app

cfx:
	vendor/bin/codefixer app

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon --memory-limit=512M app

#####################
# LOCAL DEVELOPMENT #
#####################

dev:
	NETTE_DEBUG=1 NETTE_ENV=dev php -S 0.0.0.0:8000 -t www

##############
# DEPLOYMENT #
##############

publish:
	git push heroku master --force
