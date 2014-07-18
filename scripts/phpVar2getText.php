<?php
/*
 raphael.droz@gmail.com, dwtfywwi licence , 2010 :
 
 choppe un fichier $vara="traduction"; dans <lang>.inc
 build un assoc array
 preg_replace les appels wrappés dans gettext() dans le php principal
 generate un .po
 
 (
 echo -e "<?php\n\$l = array (";
 sed -n '/^$.*$/s/^$\([^=]\+\)=.\(.*\).;/"\1"=>\x27\2\x27,/p' lang/en.inc;
 echo '); ?>'
 ) > /tmp/lang.mod
 (manually tweak 2 double quotes)
*/

// drop a $l which contain the array
if(isset($_SERVER['PHP_SELF'])) {
  die(); // die if not run with php-cli
}

require_once('/tmp/lang.mod');
$mypath = '/var/www/studs';

/* Language, country need to be adapted */
$header = 'msgid ""
msgstr ""
"Project-Id-Version: Studs 0.6.4\n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: 2010-05-01 18:32+0100\n"
"PO-Revision-Date: 2010-05-01 18:32+0100\n"
"Last-Translator: Raphaël Droz <raphael.droz@gmail.com>\n"
"Language-Team: Guilhem Borghesi, Raphaël Droz\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Poedit-Language: FR\n"
"X-Poedit-Country: FRANCE\n"
"X-Poedit-SourceCharset: utf-8\n"
"X-Poedit-KeywordsList: _\n"
"X-Poedit-Basepath: /var/www/studs\n"
"X-Poedit-SearchPath-0: .\n"

';

/* helpers */
function stripN($a)
{
  return preg_replace("/\n/","\\n", $a);
}

function addDQ($a)
{
  return addcslashes($a,'"');
}

/* low priority for weak regexps (small variable length) at the end, please */
function cmp($a, $b)
{
  return (mb_strlen($a) < mb_strlen($b));
}

uksort($l, 'cmp');

/*
 0: surely direct, like in: echo 'text ' . $var;
 1: wrap in, like in: echo "$var";
 2: direct, like in: echo $var . "text";
*/
$match0 = $repl0 = $match1 = $repl1 = $match2 = $repl2 = $match3 = $repl3 = array();
foreach($l as $k => $v) {
  $match0[] = ';([\'"][ \.]|echo|print) *\$' . $k . ' *([\. ][\'"]|\;);' ;
  $repl0[] = '\1 _("' . stripN(stripcslashes($v)) . '") \2' ;

  $match1[] = ';(["\'])(.*?)\$' . $k . ';' ;
  $repl1[] = '\1\2" . _("' . stripN(addcslashes(stripcslashes($v),'"')) . '") . \1' ;

  $match2[] = ';\$' . $k . ';' ;
  $repl2[] = '_("' . stripN(stripcslashes($v)) . '")' ;

  $match3[] = ';\. *\$GLOBALS\["' . $k . '"\] *\.;' ;
  $repl3[] = '. _("' . stripN(stripcslashes($v)) . '") .' ;
}

foreach (new DirectoryIterator('.') as $fileInfo) {
  if($fileInfo->isDot()) {
    continue;
  }
  
  $name = $fileInfo->getFilename();
  // process php files
  if(!preg_match('/\.php$/' , $name) || preg_match('/phpVar2getText/', $name)) {
    continue;
  }

  $orig = file_get_contents($name);
  $a = preg_replace($match0, $repl0, $orig, -1, $b);
  $a = preg_replace($match1, $repl1, $a, -1, $c);
  $a = preg_replace($match2, $repl2, $a, -1, $d);
  $a = preg_replace($match3, $repl3, $a, -1, $e);
  $tot = $b + $c + $e;
  echo $name . ' --- ' . $tot . " (match1: $c)" . "\n";
  if($tot > 0) {
    file_put_contents($name . '.save', $orig);
    file_put_contents($name, $a);
  }
}


foreach(array('fr_FR','es_ES','de_DE', 'en_GB') as $i) {
  $ii = explode('_', $i);
  $f = $ii[0]; $g = $ii[1];

  // de.inc corrupted the whole process !
  unset($tt_adminstuds_mail_corps_changemail);
  // now define each of the strings with a new langague
  require_once($mypath . '/lang/' . $f . '.inc');
  $a = '';

  /* duplicates are fatal to poedit */
  foreach(array_unique($l) as $k => $v) {
    /* poedit is strict with its syntax */
    $po_ready_v = stripN(addDQ($v));
    if($f == 'en') {
      $a .= 'msgid "' . $po_ready_v . '"' . "\n" . 'msgstr "' . $po_ready_v . '"' . "\n\n";
    } else {
      $a .= 'msgid "' . $po_ready_v . '"' . "\n" . 
	/* ${$k} the key (var name) in the orig (EN) array
	 to look for as a raw $var while the <lang>.inc is included in the context */
	'msgstr "' .  stripN(addDQ(${$k})) . '"' . "\n\n";
    }
  }
  file_put_contents('locale/' . $f . '_' . $g . '/LC_MESSAGES/Studs.po', $header . $a);
}