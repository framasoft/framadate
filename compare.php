<?php
include_once __DIR__ . '/app/inc/init.php';
?>
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body><pre><?php

    $goodLang = $_GET['good'];
    $testLang = $_GET['test'];

    $good = json_decode(file_get_contents(__DIR__ . '/locale/' . $goodLang . '.json'), true, 512, JSON_THROW_ON_ERROR);
    $test = json_decode(file_get_contents(__DIR__ . '/locale/' . $testLang . '.json'), true, 512, JSON_THROW_ON_ERROR);

    $diffSection = false;

    foreach ($good as $sectionName => $section) {
        if (!isset($test[$sectionName])) {
            echo '- section: ' . $sectionName . "\n";
            $diffSection = true;
        }
    }
    foreach ($test as $sectionName => $section) {
        if (!isset($good[$sectionName])) {
            echo '+ section: ' . $sectionName . "\n";
            $diffSection = true;
        }
    }

    if (!$diffSection and array_keys($good)!==array_keys($test)) {
        var_dump(array_keys($good));
        var_dump(array_keys($test));
    } else {
        echo 'All sections are in two langs.' . "\n";
    }

    $diff = [];

    foreach ($good as $sectionName => $section) {
        $diffSection = false;
        foreach($section as $key=>$value) {
            if (!isset($test[$sectionName][$key])) {
                $diff[$sectionName]['-'][] = $key;
                $diffSection = true;
            }
        }

        if (!$diffSection and array_keys($section) !== array_keys($test[$sectionName])) {
            $diff[$sectionName]['order_good'] = array_keys($section);
            $diff[$sectionName]['order_test'] = array_keys($test[$sectionName]);
        }
    }

    foreach ($test as $sectionName => $section) {
        foreach($section as $key=>$value) {
            if (!isset($good[$sectionName][$key])) {
                $diff[$sectionName]['+'][] = $key;
            }
        }
    }
    if (count($diff) > 0) {
        var_dump($diff);
    }
    ?>
</pre>
</body>
</html>
