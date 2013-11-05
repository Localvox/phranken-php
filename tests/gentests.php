<?php

// used to generate tests from class files
// ***** DO NOT USE *****
// 
// this will overwrite existing test files
// use phpunit-skelgen manually instead


if (count($argv) > 1) {
    switch($argv[1]) {
        case '-h':
        case '--h':
        case '-help':
        case '--help':

            print('usage:'.PHP_EOL);
            print('php gentests.php [file_path]'.PHP_EOL);
            exit();
            break;
    }

    $path = $argv[1];
}

$exclude = array('/amazonsdk/');
$base = dirname(dirname(__FILE__));
$sources = array($base . '/src/MarketMeSuite');
$target = $base . '/tests/tests/';

if (isset($path)) {

    $namespace = getNameSpace(file($path));

    if (empty($namespace)) {
        return;
    }

    $namespace .= '\\' . basename($path, '.php');

    passthru('phpunit-skelgen --bootstrap '.$base.'/tests/autoload.php --test -- "'.$namespace.'" '.$path);
} else {
    foreach ($sources as $dir) {

        $lambaIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($lambaIterator as $path => $value) {

            // get the target path where the test will be generated
            $targetPath = str_replace('.php', 'Test', $path);

            if (is_dir($path)) {
                continue;
            }
            
            // filter excluded paths
            if (anyInArray($exclude, $path) !== false) {
                continue;
            }

            if (isInterface(file($path)) === true) {
                continue;
            }

            if (isException(file($path)) === true) {
                continue;
            }

            // get the namespace for the class
            $namespace = getNameSpace(file($path));

            // if no namepsace then skip
            if (empty($namespace)) {
                continue;
            }

            // append the class name to the namespace
            $namespace .= '\\' . str_replace('.php', '', $value->getFileName());


            if (!is_dir(dirname($targetPath))) {
                mkdir(dirname($targetPath), 0775, true);
            }

            // the location where the test will be moved from
            // phpunit-skelgen will generate the test along-side
            // the original class by default
            $targetFile = $targetPath . '.php';

            // Get the full name of the test class
            $testName = basename($targetPath);

            // Get the full file path of the test class
            $testpath = $base . '/tests/tests/' . $testName . '.php';

            // dont overwrite existing tests
            if (file_exists($testpath)) {
                echo 'Test exists: ' . $testpath . PHP_EOL;
                continue;
            }

            //var_dump('phpunit-skelgen --bootstrap '.$base.'/tests/autoload.php --test -- "'.$namespace.'" '.$path);
            passthru('phpunit-skelgen --bootstrap '.$base.'/tests/autoload.php --test -- "'.$namespace.'" '.$path);

            // move the test to its new location
            rename($targetFile, $testpath);
        }
    }
}

/**
 * FInds the namespace of the given php class
 * @param  string $file The contents of a php class file
 * @return string       The matched namespace path
 */
function getNameSpace($file)
{
    if (isset($file[1]) === false) {
        return false;
    }

    if (strpos($file[1], 'namespace') !== false) {
        preg_match("/(?<=namespace ).+(?= {|;)/", $file[1], $matches);

        if (isset($matches[0])) {
            return $matches[0];
        }
    }
}

/**
 * checks if the given file contains an interface definition
 * @param  array  $file An array of lines given from the file() function
 * @return boolean      true if an interface definition was found, false otherwise
 */
function isInterface($file)
{
    $file = implode(PHP_EOL, $file);
    if (strpos($file, 'interface') !== false) {
        preg_match("/^interface/m", $file, $matches);

        if (isset($matches[0])) {
            return true;
        }
    }

    return false;
}

/**
 * checks if the given file contains a class that extends Exception
 * @param  array  $file An array of lines given from the file() function
 * @return boolean      true if class extends Exception, false otherwise
 */
function isException($file)
{
    $file = implode(PHP_EOL, $file);
    if (strpos($file, 'extends Exception') !== false) {
        preg_match("/(extends Exception)/m", $file, $matches);

        if (isset($matches[0])) {
            return true;
        }
    }

    return false;
}

/**
 * Are any of these values in the $needles array also in the $haystack array
 * @param array $needles The values to check exist
 * @param array $haystack The array to check against
 */
function anyInArray($needles, $haystack)
{
    foreach ($needles as $value) {
        if (strpos($haystack, $value) !== false) {
            return $value;
        }
    }

    return false;
}
