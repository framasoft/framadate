#!/usr/bin/env python

import json
import glob

def get_all_keys(jsons_dict):
    all_keys = set()

    for json_dict in jsons_dict.values():
        all_keys |= flatten_keys(json_dict)

    return all_keys

def flatten_keys(json_dict):
    all_keys = set()

    for key in json_dict.keys():
        el = json_dict[key]
        if isinstance(el, dict):
            flatten = flatten_keys(el)
            for flat_key in flatten:
                all_keys.add("%s.%s" % (key, flat_key))
        else:
            all_keys.add(key)

    return all_keys

def check_files_share_same_keys(all_keys, jsons):
    exit_code = 0

    for locale in jsons.keys():
        difference = all_keys - flatten_keys(jsons[locale])
        if bool(difference):
            print("%s has missing translation keys:" % (locale))
            for el in difference:
                print("  - %s" % (el))
            exit_code = 1

    return exit_code

def main():
    locales_dir = "locale/*.json"

    jsons = {}
    for json_file in glob.iglob(locales_dir):
        with open(json_file) as f:
            jsons[json_file] = json.load(f)

    all_keys = get_all_keys(jsons)

    return check_files_share_same_keys(all_keys, jsons)

main()
