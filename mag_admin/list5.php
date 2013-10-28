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
<fieldset>
<label for="clear">Mark DVD as sent</label>
<input type="checkbox" name="clear" id="clear" value="Y">
</fieldset>
<br/><input type="submit" value="Go"/>
<input type="submit" name="Quit" value="Quit"/>
</form>
HERE;
    return $form;
}

function do_extract(){
    global $mysqli;

    $sql="select * from sub_subscribers where DVD='Y'";

    $res=$mysqli->query($sql);
    if($res->num_rows==0)
        die('No records found! <a href="'.ADMIN_URL.'">Back</a>');

    header("Content-type: application/force-download");
    header('Content-disposition: attachment; filename="DVD_List.csv"');
    #header("Content-Length: ".filesize($absfile));

    echo '"Mailing Name","Company","Address1","Address2","Address3","Address4","Address5","Address6","Subscriber_id"'."\r\n";
# Use Address6 if Country is overseas
    while ($row=$res->fetch_assoc()){
        echo do_row($row);
    }
    if (array_key_exists('clear',$_POST)){
	$mysqli->query("update sub_subscribers set DVD='N'");
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
echo HTMLSTART.page_header().'<h1>Distribution List</h1>'.do_form().page_footer().HTMLEND;
?>
</body>
</html>
