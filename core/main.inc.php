<?php
/* 
 * This files handles the includes. Every file in the picknick project should
 * include following line:
 * require_once dirname(__FILE__) . "/../core/main.inc.php";
 * 
 * or, if you developing a ILoggingObserver or a IDataProvider object:
 * require_once dirname(__FILE__) . "/../../core/main.inc.php";
 */

require_once dirname(__FILE__) . '/exceptions.php';
require_once dirname(__FILE__) . '/logging.php';
require_once dirname(__FILE__) . '/page.php';
require_once dirname(__FILE__) . '/kernel.php';
require_once dirname(__FILE__) . '/../etc/database.helper.php';

?>
