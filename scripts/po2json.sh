#!/bin/bash
po2json -i po/en.po -t locale/en.json --progress none -o po/default.json

for i in po/*.po
do
    j=$(echo $i | cut -d '.' -f 1 | cut -d '/' -f 2)

    # When there is a comma in a key, Zanata replaces it by a newline. And po2json doesn't like this.
    # Edit the po file, to restore split key names on a single line again.
    vim -E -c "while search('^\\(#: \\..*\\)\\(\\n\\_^#: \\([^.].*\\)\\)\\+') | s/\n#: /,/ | endwhile" -c 'x' -- $i

    # Convert the po file to json
    po2json -i $i -t po/default.json --progress none | scripts/renest_json.pl > po/$j.json
    mv po/$j.json locale/
done
