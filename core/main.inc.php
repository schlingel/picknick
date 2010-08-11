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
        require_once $fileinfo->getPath();
    }
}



require_once dirname(__FILE__) . '/exceptions.php';
require_once dirname(__FILE__) . '/logging.php';
require_once dirname(__FILE__) . '/page.php';
require_once dirname(__FILE__) . '/kernel.php';
require_once dirname(__FILE__) . '/../etc/database.helper.php';

include_dir(dirname(__FILE__) . '/../etc/data.provider');
include_dir(dirname(__FILE__) . '/../etc/data.sink');
include_dir(dirname(__FILE__) . '/../page');

?>
