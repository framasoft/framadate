locales:
	json2po -P -i locale/en.json -t locale/en.json -o po/framadate.pot

push-locales: locales
	zanata-cli -q -B push

pull-locales:
	zanata-cli -q -B pull --min-doc-percent 50
	scripts/po2json.sh

stats-locales:
	zanata-cli -q stats

push-trad-to-zanata:
	scripts/push-trad-to-zanata.sh $(filter-out $@,$(MAKECMDGOALS))

add-key-locales:
	scripts/locale-add-key.pl "$(subst ",\",$(filter-out $@,$(MAKECMDGOALS)))"

# empty targets to be able to use MAKECMDGOALS as arguments to scripts
%:
	@:
