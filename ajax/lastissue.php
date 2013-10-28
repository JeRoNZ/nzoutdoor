<?php

require("../inc/functions.inc");
send_nocache_headers();

if ($_SERVER['REQUEST_METHOD'] != 'POST'){
	die(json_encode(array('status'=>1,'message'=>'bad')));
}

$array=array('status'=>1);
if (array_key_exists('last',$_POST))
	$array['first']=first_issue($_POST['last'],$_POST['year']);
else
	$array['last']=last_issue($_POST['first'],$_POST['year']);
echo json_encode($array);
?>
