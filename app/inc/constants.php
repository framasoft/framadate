<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
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
const VERSION = '0.9';

// Regex
const POLL_REGEX = '/^[a-zA-Z0-9]+$/';
const CHOICE_REGEX = '/^[012]$/';
const NAME_REGEX = '/^[áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœa-z0-9_ -]+$/i';
const BOOLEAN_REGEX = '/^(on|off|true|false|1|0)$/';
const EDITABLE_CHOICE_REGEX = '/^[0-2]$/';

// CSRF (300s = 5min)
const TOKEN_TIME = 300;

// Errors
const COMMENT_EMPTY         = 0x0000000001;
const COMMENT_USER_EMPTY    = 0x0000000010;
const COMMENT_INSERT_FAILED = 0x0000000100;
const NAME_EMPTY            = 0x0000001000;
const NAME_TAKEN            = 0x0000010000;
const NO_POLL               = 0x0000100000;
const NO_POLL_ID            = 0x0001000000;
const INVALID_EMAIL         = 0x0010000000;
const TITLE_EMPTY           = 0x0100000000;
const INVALID_DATE          = 0x1000000000;
