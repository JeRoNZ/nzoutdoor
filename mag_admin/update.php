<?php
die();


$handle =fopen('update.csv','r');
require("../inc/connect.inc");
require("../inc/functions.inc");

$sql="update sub_subscribers set address1='ADD1', address2='ADD2',city='CITY',region='REGION',company='COMPANY',pcode='PCODE' where renewal_id='ID'";

$headers=fgetcsv($handle);
$headers=array_flip($headers);

while ($rows = fgetcsv($handle)){
	$q = str_replace('ADD1',$mysqli->escape_string($rows[$headers['address1']]),$sql);
	$q = str_replace('ADD2',$mysqli->escape_string($rows[$headers['address2']]),$q);
	$q = str_replace('COMPANY',$mysqli->escape_string($rows[$headers['company']]),$q);
	$q = str_replace('CITY',$mysqli->escape_string($rows[$headers['city']]),$q);
	$q = str_replace('REGION',$mysqli->escape_string($rows[$headers['region']]),$q);
	$q = str_replace('PCODE',sprintf("%04d",$rows[$headers['pcode']]),$q);
	$q = str_replace('ID',$rows[$headers['renewal_id']],$q);
	#echo $q.PHP_EOL;
	$mysqli->query($q);
	echo $mysqli->error;
}
