<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

$ALLOWED_LANGUAGES = [
    'fr' => 'Français',
    'en' => 'English',
    'oc' => 'Occitan',
    'es' => 'Español',
    'de' => 'Deutsch',
    'nl' => 'Dutch',
    'it' => 'Italiano',
    'br' => 'Brezhoneg',
];

const ARG_CREATE_NEW_KEY = "--create-new-key";

exec("pwd", $content);
$directory = $content[0];

$valid = FALSE;
$createNewKey = FALSE;

if (	(4 === $argc)
	&&	(ARG_CREATE_NEW_KEY === $argv[1])
) {
	$valid = TRUE;
	$createNewKey = TRUE;
	
	$key = $argv[2];
	$text = $argv[3];
} elseif (3 === $argc) {
	$valid = TRUE;
	
	$key = $argv[1];
	$text = $argv[2];
}

if (!$valid) {
	o("Usage :");
	o("php {$argv[0]} \"key\" \"text string\"");
	
	exit();
}

foreach ($ALLOWED_LANGUAGES as $language_code => $language_name) {
	$file = "$directory/locale/$language_code.json";
	
	$content = file_get_contents($file);
	$translations_groups = json_decode($content);
	
	// add string
	
	if (!isset($translations_groups->$key)) {
		if ($createNewKey) {
			$translations_groups->$key = new stdClass();
		} else {
			o("the key \"$key\" doesn't exist");
			o("run this to create the new key : ");
			o("php {$argv[0]} " . ARG_CREATE_NEW_KEY . " \"$key\" \"$text\"");
			
			exit();
		}
	}
	
	$translations_groups->$key->$text = $text;
	
	// save file
	$content = json_encode($translations_groups, JSON_UNESCAPED_UNICODE);
	
	$content = str_replace(":{", ":\n{\n      ", $content);
	$content = str_replace("\":\"", "\": \"", $content);
	$content = str_replace("{\"", "{\n\"", $content);
	$content = str_replace("}", "\n}\n", $content);
	$content = str_replace("\",\"", "\",\n      \"", $content);
	
	file_put_contents($file, $content);
	
	o($file);
}

function o($text) {
	echo "$text\n";
}

