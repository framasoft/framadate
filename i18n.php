<?php
//$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

if (isset($_GET['lang']) && is_string($_GET['lang']) && in_array($_GET['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
  $mlocale = $_GET['lang'] ;
  setcookie('lang' , $_GET['lang'], time()+60*5);
} elseif ( isset($_COOKIE['lang']) && is_string($_COOKIE['lang']) && in_array($_COOKIE['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
  $mlocale = $_COOKIE['lang'] ;
} else {
  $mlocale = LANGUE ;
}

$locale = $mlocale . '.utf8';
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