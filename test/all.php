<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';


$h = new lime_harness(new lime_output_color());
$dir = new DirectoryIterator(BASE);
while ($dir->valid()) {
    if ($dir->isFile() && $dir->getFilename() !== 'all.php') {
        $h->register($dir->getPathname());
    }
    $dir->next();
}
$h->run();
