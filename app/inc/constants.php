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

// FRAMADATE version
const VERSION = '1.1.19';

// PHP Needed version
const PHP_NEEDED_VERSION = '7.3';

// Config constants
const COMPILE_DIR = '/tpl_c/';

// Regex
const POLL_REGEX = '/^[a-z0-9-]*$/i';
const ADMIN_POLL_REGEX = '/^[a-z0-9]{24}$/i';
const CHOICE_REGEX = '/^[ 012]$/';
const BOOLEAN_REGEX = '/^(on|off|true|false|1|0)$/i';
const BOOLEAN_TRUE_REGEX = '/^(on|true|1)$/i';
const EDITABLE_CHOICE_REGEX = '/^[0-2]$/';
const BASE64_REGEX = '/^[A-Za-z0-9]+$/';
const MD5_REGEX = '/^[A-Fa-f0-9]{32}$/';

// Session constants
const SESSION_EDIT_LINK_TOKEN = 'EditLinkToken';
const SESSION_EDIT_LINK_TIME = "EditLinkMail";

// CSRF (300s = 5min)
const TOKEN_TIME = 300;

const ICAL_ENDING = ".ics";
const ICAL_PRODID = "-//Framasoft//Framadate//EN";
