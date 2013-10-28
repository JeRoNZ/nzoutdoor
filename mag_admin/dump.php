<?php
session_start();
require("../inc/functions.inc");
require("../inc/connect.inc");
check_admin_cookie(TRUE);

header("Content-type: application/force-download");
header('Content-disposition: attachment; filename="DB_Dump.txt"');
passthru("mysqldump -u ".USER." --password=".PASS." -h ".HOST." ".DB);
?>
