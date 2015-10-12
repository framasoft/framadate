<?php
include_once __DIR__ . '/app/inc/init.php';
?>
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body><pre><?php
$lang = 'fr_FR';
$po = file_get_contents(__DIR__ . '/locale/' . $lang . '/LC_MESSAGES/Studs.po');
$converter = new \o80\convert\Po2JsonConverter();
$json = $converter->convert($po);
print_r($json);
?></pre></body>
</html>
