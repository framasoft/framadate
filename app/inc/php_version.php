<?php


function php_version_id_to_string($versionId) {
    $major = substr($versionId, 0, 2);
    $minor = substr($versionId, 2, 2);
    $release = substr($versionId, 4, 2);
    return $major.'.'.$minor.'.'.$release;
}
function php_version_to_version_id($major, $minor, $release) {
    return ($major * 10000 +$minor * 100 + $release);
}
function php_string_to_version_id($version) {
    $version = explode('.', $version);
    return php_version_to_version_id($version[0], $version[1], $version[2]);
}


if (!defined('PHP_VERSION_ID')) {
    $version = explode('.',PHP_VERSION);
    define('PHP_VERSION_ID', php_version_to_version_id($version[0], $version[1], $version[2]));
}
if (PHP_VERSION_ID < 50207) { // This constants do not exists before 5.2.7
    define('PHP_MAJOR_VERSION',   $version[0]);
    define('PHP_MINOR_VERSION',   $version[1]);
    define('PHP_RELEASE_VERSION', $version[2]);
}

?>