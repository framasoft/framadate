#!/bin/bash
FILE=$1
if [[ ! -e locale/$FILE.json ]]
then
    echo "locale/$FILE.json does not exist. Exiting."
    exit 1
else
    LOCALE=$(echo $FILE | sed -e "s@_@-@g")
    json2po -i locale/$FILE.json -t locale/en.json -o po/$FILE.po
    zanata-cli -q -B push --push-type trans -l $LOCALE --project-version $(git branch | grep \* | cut -d ' ' -f2-)
fi
