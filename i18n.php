<?php

if (isset($_POST['lang']) && is_string($_POST['lang']) && in_array($_POST['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
  $mlocale = $_POST['lang'] ;
  setcookie('lang' , $_POST['lang'], time()+60*5);
} elseif ( isset($_COOKIE['lang']) && is_string($_COOKIE['lang']) && in_array($_COOKIE['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
  $mlocale = $_COOKIE['lang'] ;
} else {
  $mlocale = LANGUE ;
}

$locale = $mlocale . '.utf8';
putenv('LANGUAGE=');
setlocale(LC_ALL, $locale);
setlocale(LC_TIME, $locale);
setlocale(LC_MESSAGES, $locale);

$domain = 'Studs';
bindtextdomain($domain, 'locale');
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

/* temp, for compatibility :*/
$a = explode('_', $locale);
$_SESSION['langue'] = strtoupper($a[0]);

/* <html lang="$lang"> */
$lang = ($_SESSION['langue']!='') ? strtolower($_SESSION['langue']) : 'fr';

