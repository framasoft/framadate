push-locales:
	json2po -P -i locale/en.json -t locale/en.json -o po/framadate.pot
	zanata-cli -q -B push

pull-locales:
	zanata-cli -q -B pull
	./.po2json.sh
