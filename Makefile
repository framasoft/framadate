locales:
	scripts/locales.sh

push-locales: locales
	zanata-cli -q -B push --errors --project-version `git branch | grep \* | cut -d ' ' -f2-`

pull-locales:
	zanata-cli -q -B pull --min-doc-percent 50 --project-version `git branch | grep \* | cut -d ' ' -f2-`
	scripts/po2json.sh

stats-locales:
	zanata-cli -q stats --project-version `git branch | grep \* | cut -d ' ' -f2-`

push-trad-to-zanata:
	scripts/push-trad-to-zanata.sh $(filter-out $@,$(MAKECMDGOALS))

add-key-locales:
	scripts/locale-add-key.pl "$(subst ",\",$(filter-out $@,$(MAKECMDGOALS)))"

# empty targets to be able to use MAKECMDGOALS as arguments to scripts
%:
	@:
