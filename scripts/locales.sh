#!/bin/bash
python -m json.tool < locale/en.json
if [[ $? == 0 ]];
then
    json2po -P -i locale/en.json -t locale/en.json -o po/framadate.pot
else
    echo "Can't convert json files to po, the json file is incorrect"
    exit 1
fi
