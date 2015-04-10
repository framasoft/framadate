<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha?l DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est r?gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha?l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

function d($thing) {
    echo $thing . "\n";
}

function i($if, $thing, $copied = ' copied') {
    if ($if) {
        echo $thing . $copied . "\n";
    } else {
        echo '[fail] ' . $thing . "\n";
    }
}

function rcopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    $copied = true;
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                $copied &= rcopy($src . '/' . $file, $dst . '/' . $file);
            } else {
                $copied &= copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    return !!$copied;
}

function rrmdir($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? rrmdir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function copyDependencyToBuild($dirname) {
    return @mkdir(BUILD_VENDOR . $dirname, 755, true) && @rcopy(VENDOR . $dirname, BUILD_VENDOR . $dirname);
}

function copyFiles($files, &$result) {
    foreach ($files as $key => $file) {
        if (is_int($key)) {
            $key = $file;
        }

        if (is_dir(ROOT . '/' . $key)) {
            $result->$key = @rcopy(ROOT . '/' . $key, BUILD . '/' . $file);
        } elseif (is_file(ROOT . '/' . $key)) {
            $result->$key = @copy(ROOT . '/' . $key, BUILD . '/' . $file);
        }

        i($result->$key, $key);
    }
}

function zip($source, $destination) {
    if (extension_loaded('zip')) {
        if (file_exists($source)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
                $source = realpath($source);
                if (is_dir($source)) {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($files as $file) {
                        if (in_array(basename($file), array('.', '..'))) {
                            continue;
                        }
                        $file = realpath($file);
                        if ($file !== $source && is_dir($file)) {
                            $zip->addEmptyDir(str_replace($source . '\\', '', str_replace($source . '/', '', $file)));
                        } else if (is_file($file)) {
                            $zip->addFromString(str_replace($source . '\\', '', str_replace($source . '/', '', $file)), file_get_contents($file));
                        }
                    }
                } else if (is_file($source)) {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }
            return $zip->close();
        }
    }
    return false;
}

ini_set('max_execution_time', 600);
ini_set('memory_limit', '1024M');

define('ROOT', realpath(__DIR__ . '/..'));
define('VENDOR', ROOT . '/vendor');
define('DIST', ROOT . '/dist');
define('BUILD', ROOT . '/build');
define('BUILD_VENDOR', BUILD . '/vendor');

include ROOT . '/app/inc/constants.php';

$result = new stdClass();

echo '<pre>';

// Delete old dist>build directories

if (file_exists(DIST)) {
    $result->rmdirDist = rrmdir(DIST);
    i($result->rmdirDist, 'Dist', ' deleted');
}
if (file_exists(BUILD)) {
    $result->rmdirBuild = rrmdir(BUILD);
    i($result->rmdirBuild, 'Build', ' deleted');
}

// Create dist>build directories

$result->mkdirDist = mkdir(DIST, 755);
i($result->mkdirDist, 'Dist', ' created');
$result->mkdirBuild = mkdir(BUILD, 755);
i($result->mkdirBuild, 'Build', ' created');

// Copy dependencies files

d('# Dependencies');

$result->composer = copyDependencyToBuild('/composer');
i($result->composer, 'composer');

$result->o80 = copyDependencyToBuild('/o80/i18n/src');
i($result->o80, 'o80-i18n');

$result->smarty = copyDependencyToBuild('/smarty/smarty/libs');
i($result->smarty, 'smarty');

$result->autoload = @copy(VENDOR . '/autoload.php', BUILD_VENDOR . '/autoload.php');
i($result->autoload, 'autoload');

// Copy assets

d('# Assets');
copyFiles(array('css', 'fonts', 'images', 'js'), $result);

// Copy sources

d('# Source directories');
copyFiles(array('admin', 'app', 'locale', 'tpl'), $result);

d('# Source files');
$files = array(
    'adminstuds.php',
    'bandeaux.php',
    'create_classic_poll.php',
    'create_date_poll.php',
    'create_poll.php',
    'exportcsv.php',
    'favicon.ico',
    'htaccess.txt',
    'index.php',
    'INSTALL.md',
    'LICENCE.fr.txt',
    'LICENSE.en.txt',
    'maintenance.php',
    'php.ini',
    'README.md',
    'robots.txt',
    'studs.php'
);
copyFiles($files, $result);

// Zip Dist
$output = DIST . '/framadate-' . VERSION . '-' . date('Ymd') . '.zip';
zip(BUILD, $output);
rrmdir(BUILD);

if (isset($_GET['verbose'])) {
    var_dump($result);
}

d('--------');
d('Distribution file: ' . realpath($output));

$generatedIn = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 4);
d('========');
d('Generated in: ' . $generatedIn . ' secondes');
echo '</pre>';
