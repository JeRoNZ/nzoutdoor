<?php
session_start();
require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/Next.inc");
check_admin_cookie(TRUE);

$sql="select * from sub_temp order by id desc";

$res=$mysqli->query($sql);

header("Content-type: text/plain");

while ($row=$res->fetch_assoc()){
	echo $row['timestamp'].PHP_EOL;
	$a=unserialize($row['data']);
	echo 'id => '.$row['id'].PHP_EOL;
	foreach ($a as $key => $value){
		echo $key.' => '.$value.PHP_EOL;
	}
	echo "===========================================\n";
};
