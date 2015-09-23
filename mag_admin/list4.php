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
<title>Email Renewals</title>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<link rel="stylesheet" type="text/css" media="all" href="/css/admin.css">
<script type='text/javascript' src='/js/jquery-1.4.4.min.js'></script>
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
    $stamp=time()-86400*62; // force back two months to allow for running after the 21st
    $bob = new next_issue($stamp);
    $sel = $bob->option(1);
    $form=<<<HERE
<form method="post" action="$self">
<select name="year">$sel</select>
<br/><input type="submit" value="Go"/>
<input type="submit" name="Quit" value="Quit"/>
</form>
HERE;
    return $form;
}

function do_extract(){
    global $mysqli;
    if (!ctype_digit($_POST["year"]))
        die('oops');
    $year=$_POST["year"];
    $sql="select * from sub_subscribers where
($year between first_issue and last_issue)
and email !=''
and last_issue = $year
and ((method='P' and paymate_response='PA') or method != 'P')
and free='N'";

    $res=$mysqli->query($sql);
    if($res->num_rows==0)
                die('No records found! <a href="'.ADMIN_URL.'">Back</a>');

    echo "<p>Sending email to:<br>";
    while ($row=$res->fetch_assoc()){
        echo do_email($row);
    }
    echo '<br>Done! <a href="'.ADMIN_URL.'">Back</a>';
}

function do_email ($row){
    $subject="NZ Outdoor Magazine Subscription Renewal";
    $headers='From: '.OWNER_NAME.' <'.OWNER_EMAIL.'>';

    $rid=$row['renewal_id'];
    $title=$row['title'];
    $surname=$row['surname'];
    $email=$row['email'];
    if ($email=='')
        return '<b>'.$rid.' '.$title.' '.$surname.' Not sent</b><br>';
    $url=BASE_URL.'?rid='.$rid;

	#$disc='25% ';
	$disc='up to $80';
	if ($row['country'] ==  '13') //Australia
		$disc = '';
	$mess = file_get_contents('templates/list4.txt');
	$mess = str_replace('<TITLE>', $title, $mess);
	$mess = str_replace('<SURNAME>', $surname, $mess);
	$mess = str_replace('<DISC>', $disc, $mess);
	$mess = str_replace('<URL>', $url, $mess);
	$mess = str_replace('<RID>', $rid, $mess);

	#file_put_contents('/tmp/nz',$mess,FILE_APPEND);
    @mail($email, $subject, $mess, $headers,'-f '.OWNER_EMAIL);
    return '<b>'.$email.'</b><br>';
}

$method=$_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    if (array_key_exists('Quit',$_POST)){
        header("Location: ".ADMIN_URL);
        exit();
    }
    do_extract();
    exit();
}
echo HTMLSTART.page_header().'<h1>Email Renewals</h1>'.do_form().page_footer().HTMLEND;
?>
</body>
</html>
