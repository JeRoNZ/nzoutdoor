<?php
define('HOST',"localhost");
define('USER',"nzoutdoor");
define('PASS',"mrtfg45rf");
define('DB',"nzoutdoor");
$mysqli = new mysqli(HOST,USER,PASS,DB);
echo $mysqli->connect_error;

define("ADMIN_URL","/mag_admin");
define("RETURN_URL","https://www.nzoutdoor.co.nz/subscribe/subscribed.php");
define("BASE_URL","https://www.nzoutdoor.co.nz/subscribe");
define("ADMIN_TIMEOUT",86400);
define("OWNER_NAME",'NZ Outdoor Hunting Magazine');
define("OWNER_EMAIL",'info@nzoutdoor.co.nz');

?>
