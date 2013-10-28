<?php
session_start();
require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/securelink.php");
include("../inc/adodb5/adodb-exceptions.inc.php");
include("../inc/adodb5/adodb.inc.php");
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
if (!@$db) {
    try {
        $db = NewADOConnection('mysqli');
        if (($db->PConnect(HOST, USER, PASS, DB)) === false)
            die('DB ERROR: ' . $db->ErrorNo());
    }
    catch (Exception $e) {
        die ('Database connection failed!');
    }
}

    $bob = new next_issue();
    $year = $bob->current();

    $sql="SELECT * from sub_subscribers WHERE country=153 AND (last_issue < $year) AND free='N' order by last_issue desc";

    $res = $db->Execute($sql);
    while ($row = $res->FetchRow()) {
	if ($row['email']) echo $row['email'].PHP_EOL;
    }
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
echo HTMLSTART.page_header().'<h1>Email Expired Subscribers Last 12 Months</h1>'.do_form().page_footer().HTMLEND;
?>
</body>
</html>
