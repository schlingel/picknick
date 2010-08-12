<?php

include './core/main.inc.php';

$kernel = new Kernel();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title><?php echo $kernel->GetTitle(); ?></title>
    </head>
    <body>
        <?php
            $kernel->ShowPage();
        ?>
    </body>
</html>
