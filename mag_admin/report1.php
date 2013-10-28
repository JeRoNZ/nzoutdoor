<?php
session_start();
require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/Next.inc");
include("head.inc");
include("foot.inc");
check_admin_cookie(TRUE);

define("HTMLSTART", <<<HERE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<link rel="stylesheet" type="text/css" media="all" href="/css/admin.css">
<script type='text/javascript' src='/js/jquery-1.4.4.min.js'></script>
<style type="text/css">
table{border:1px solid #000;text-align:center;border-collapse:collapse}
th{text-align:center;border-left:1px solid #000;padding:3px;}
table tbody tr td {text-align:center;border-left:1px solid #000;padding:3px}
</style>
</head>
<body>
HERE
);
define("HTMLEND", <<<HERE
</body>
</html>
HERE
);

function do_form(){
    $self=$_SERVER['PHP_SELF'];
    $form=<<<HERE
<form method="post" action="$self">
<br/><input type="submit" value="Go"/>
<input type="submit" name="Quit" value="Quit"/>
</form>
HERE;
    return $form;
}

function do_extract(){
    global $mysqli;
$Y = date('Y');
$M = date('m');


	$data = array();

	for ($i=24;$i>=1;$i--){
		$first = mktime(0,0,0,$M,1,$Y);
		$last = mktime(23,59,59,$M,date('t',$first),$Y);
		$first = date ('Y-m-d H:i:s',$first);
		$last = date ('Y-m-d H:i:s',$last);
		$sql = "SELECT COUNT(*) FROM sub_subscribers WHERE FROM_UNIXTIME(date_subscribed) BETWEEN '{$first}' AND '{$last}'";
		#$sql2 = "SELECT COUNT(*) FROM sub_subscribers WHERE FROM_UNIXTIME(date_renewed) BETWEEN '{$first}' AND '{$last}'";
		$sql2 = "SELECT COUNT(*) FROM sub_renewals WHERE date BETWEEN '{$first}' AND '{$last}'";

		$res=$mysqli->query($sql);
		if($res->num_rows==0) {
			$data[$i] = array ('M'=>$M,'Y'=>$Y,'S'=>0);
		} else {
			$row=$res->fetch_row();
			$data[$i] = array ('M'=>$M,'Y'=>$Y,'S'=>$row[0]);
		}
		$res=$mysqli->query($sql2);
		if($res->num_rows==0) {
			
			$data[$i]['R']=0;
		} else {
			$row=$res->fetch_row();
			$data[$i]['R']=$row[0];
		}
		$M--;
		if ($M==0){
			$Y-=1;$M=12;
		}
		
	}

	array_reverse($data);

	?><table><tr><th></th><?php

	foreach ($data as $v) {
		?><th><?php echo $v['M'].'<br/>'.$v['Y']?></th><?php
	}
	?></tr><tr><td>Subscriptions</td><?php
	foreach ($data as $v) {
		?><td><?php echo $v['S']?></td><?php
	}
	?></tr><tr><td>Renewals</td><?php
	foreach ($data as $v) {
		?><td><?php echo $v['R']-$v['S']?></td><?php
	}
	?></tr><tr><td>Total</td><?php
	foreach ($data as $v) {
		?><td><?php echo $v['R']?></td><?php
	}
	?></tr></table><?php
}

$method=$_SERVER['REQUEST_METHOD'];
if ($method == "POST") {
    if (array_key_exists('Quit',$_POST)){
        header("Location: ".ADMIN_URL);
        exit();
    }
}
echo HTMLSTART.page_header().'<h1>Subscription Activity last 24 months</h1>';
if ($method == "POST") {
    do_extract();
} 
echo do_form();
echo page_footer().HTMLEND;
?>
</body>
</html>
