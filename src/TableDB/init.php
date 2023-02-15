<?php

/**
 * Loads the $class when it is called if it is not loaded yet. Works with spl_autoload queue.
 * @param mixed $class
 */
function autoload($class)
{
    $dir = scandir(__DIR__); // scan the current directory to find the folders.
    $dir = array_splice($dir, 1); // remove the . from array (.. will use to go back so we need it)
    $folders = array_filter($dir, function ($item) {
        return is_dir(__DIR__ . '/' . $item); // check if the item is a folder if not remove it.
    });

    foreach ($folders as $folder) {
        $namespaced = strpos($class, '\\'); // if a class has "\" character it means it is in a namespace.

        if (!$namespaced) { // if it is not in a namespace.
            if ($folder !== "..") {
                $filename = __DIR__ . '/' . $folder . '/' . $class . '.php'; // filename is the path to the folder/file.
            } else {
                $filename = __DIR__ . '/' . $class . '.php';  // filename is the path to the file.
            }
        } else {
            $filename = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php'; // if namespaced, replace "\" with "/"
        }

        if (file_exists($filename)) {
            require_once($filename); // if file exists require it once.
        }
    }
}

spl_autoload_register('autoload'); // register the autoload function to spl_autoload queue.
