<?php
include_once __DIR__ . '/app/inc/init.php';
?>
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body><pre><?php

    $goodLang = $_GET['good'];
    $otherLang = $_GET['other'];

    $good = json_decode(file_get_contents(__DIR__ . '/locale/' . $goodLang . '.json'), true, 512, JSON_THROW_ON_ERROR);
    $other = json_decode(file_get_contents(__DIR__ . '/locale/' . $otherLang . '.json'), true, 512, JSON_THROW_ON_ERROR);

    foreach ($good as $sectionName => $section) {
        foreach ($section as $key => $value) {
            $good[$sectionName][$key] = getFromOther($other, $key, $value, $otherLang);
        }
    }

    echo json_encode($good, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | ~(JSON_ERROR_UTF8 | JSON_HEX_QUOT | JSON_HEX_APOS));

    function getFromOther($other, $goodKey, $default, $otherLang): string {
        foreach ($other as $sectionName => $section) {
            foreach ($section as $key => $value) {
                if (
                    strtolower($key) === strtolower($goodKey) ||
                    stripos($key, strtolower($goodKey)) === 0 ||
                    strtolower(trim($key)) === strtolower($goodKey) ||
                    strtolower(trim(substr(trim($key), 0, strlen($key) - 1))) === strtolower($goodKey)
                ) {
                    return $value;
                }
            }
        }

        echo '[-]' . $goodKey . "\n";

        return strtoupper($otherLang) . '_' . $default;
    }

    ?>
</pre>
</body>
</html>
