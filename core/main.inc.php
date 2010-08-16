<?php
/* 
 * This files handles the includes. Every file in the picknick project should
 * include following line:
 * require_once dirname(__FILE__) . "/../core/main.inc.php";
 * 
 * or, if you developing a ILoggingObserver or a IDataProvider object:
 * require_once dirname(__FILE__) . "/../../core/main.inc.php";
 */

/**
 * Includes every file in the given path via require_once
 * @param string $dirpath
 */
function include_dir($dirpath) {
    $dir = new DirectoryIterator($dirpath);

    foreach($dir as $fileinfo) {
        $fileName = $fileinfo->getFilename();

        if($fileName == '.' || $fileName == '..' || !IsPHP($fileName))
            continue;

        if(is_dir($fileinfo->getRealPath())) {
            include_dir ($fileinfo->getRealPath());
        }
        else {
            require_once "{$fileinfo->getRealPath()}";
        }
    }
}

/**
 * Checks if the given file is a php file.
 */
function IsPHP($file) {
    if(strlen($file) <= 4)
        return false;

    $substring = substr($file, strlen($file) - 4);
    return ($substring === '.php');
}

/*
 * CONFIGURE!!!
 *
 * This is the only bit of configuration which must be done. Just enter the
 * directory hierachy.
 *
 * For example. If the project lies direct in the root directory of the web
 * server __PROJECT__ must be empty:
 *  define('__PROJECT__', '');
 *
 * If there are the directory hierachy:
 * project
 *   |
 *   +-page
 *      |
 *      +-system
 *
 * then it is:
 * server __PROJECT__ must be empty:
 *  define('__PROJECT__', 'project/page/system/');
 * 
 * The last slash is needed in a directory hierachy!
 */
define('__PROJECT__', 'Picknick/');

define('__URL__', "http://{$_SERVER['SERVER_NAME']}/" . __PROJECT__);

require_once dirname(__FILE__) . '/exceptions.php';
require_once dirname(__FILE__) . '/logging.php';
require_once dirname(__FILE__) . '/page.php';

include_dir(dirname(__FILE__) . '/kernel.additions');

require_once dirname(__FILE__) . '/kernel.php';
require_once dirname(__FILE__) . '/../etc/database.helper.php';

include_dir(dirname(__FILE__) . '/../etc/data.provider');
include_dir(dirname(__FILE__) . '/../etc/data.sink');
include_dir(dirname(__FILE__) . '/../etc/html.helper');
include_dir(dirname(__FILE__) . '/../page');

?>
