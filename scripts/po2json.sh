#!/bin/bash
po2json -i po/en.po -t locale/en.json --progress none -o po/default.json

for i in po/*.po
do
    j=$(echo $i | cut -d '.' -f 1 | cut -d '/' -f 2)
    vim -E -c "while search('^\\(#: \\..*\\)\\(\\n\\_^#: \\([^.].*\\)\\)\\+') | execute 'normal J' | s/ #: /,/ | endwhile" -c 'x' -- $i
    po2json -i $i -t po/default.json --progress none | scripts/renest_json.pl > po/$j.json
    mv po/$j.json locale/
done
