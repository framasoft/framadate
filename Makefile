push-locales:
	json2po -P -i locale/en.json -t locale/en.json -o po/framadate.pot
	zanata-cli -B push

pull-locales:
	zanata-cli -B pull
	./.renest_po.sh
