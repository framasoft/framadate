locales:
	json2po -P -i locale/en.json -t locale/en.json -o po/framadate.pot

push-locales: locales
	zanata-cli -q -B push

pull-locales:
	zanata-cli -q --min-doc-percent 50 -B pull
	./.po2json.sh

stats-locales:
	zanata-cli -q stats
