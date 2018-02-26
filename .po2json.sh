#!/bin/bash
for i in po/*.po
do
    j=$(echo $i | cut -d '.' -f 1 | cut -d '/' -f 2)
    po2json -i $i -t locale/en.json --progress none | ./.renest_json.pl > po/$j.json
    mv po/$j.json locale/$j.json
done
