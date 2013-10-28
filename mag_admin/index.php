<?php
require('../inc/connect.inc');
require('../inc/functions.inc');
send_nocache_headers();
include("head.inc");
include("foot.inc");
session_start();
check_admin_cookie();

function do_login_form() {
    $form=<<<HERE
<center>
<table>
<form method="post" action="{$_SERVER['PHP_SELF']}">
<tr><td>Username</td><td><input type='text' name='email' id='email' size=30></td></tr>
<tr><td>Password</td><td><input type='password' name='password' id='password' size=8></td></tr>
<tr><td align=center colspan=2><input type='submit' value="Login"></tr>
</form>
</table>
</center>
HERE;
    echo $form;
}

function do_links() {
    $links=<<<HERE
<h2>Admin menu</h2>
<ul>
<li class=maint><a href="subscribers.php">Manage subscribers</a>
<li class=maint><a href="list1.php">Distribution list</a>
<li class=maint><a href="list2.php">Renewal list</a>
<li class=maint><a href="list3.php">Expired list</a>
<li class=maint><a href="list4.php">Email renewals</a>
<li class=maint><a href="list5.php">DVD list</a>
<li class=maint><a href="list6.php">Complimentary list</a>
<li class=maint><a href="dump.php">Backup database</a>
<li class=maint><a href="report1.php">Subscription report</a>
<li class=maint><a href="plist.php">Paymate failures</a>
<li class=maint><a href="{$_SERVER['PHP_SELF']}?action=logout">Logout</a>
</ul>
HERE;
    echo $links;
}

$method=$_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (!array_key_exists('email',$_POST)){
        header("Location: ".ADMIN_URL);
        exit();
    }
    if (!array_key_exists('password',$_POST)){
        header("Location: ".ADMIN_URL);
        exit();
    }
    $email=$mysqli->real_escape_string($_POST['email']);
    $passw=$mysqli->real_escape_string($_POST['password']);
    $sql="select * from sub_admins where email='{$email}'";
    if(($res=$mysqli->query($sql))===FALSE) {
        header("Location: ".ADMIN_URL);
        exit();
    }
    if ($res->num_rows != 1) {
        header("Location: ".ADMIN_URL);
        exit();
    }
    $row=$res->fetch_assoc();
    if ($passw != $row['password']) {
        header("Location: ".ADMIN_URL);
        exit();
    }
    session_regenerate_id();
    $_SESSION['admin']=time();
    $_SESSION['row']=$row;
}
else {
    if (array_key_exists('action',$_GET)) {
        session_destroy();
        unset($_SESSION);
        setcookie('PHPSESSID','',1,'/');
        header("Location: ".ADMIN_URL);
        exit();
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>NZ Outdoor</title>
<link rel="stylesheet" type="text/css" media="all" href="style.css">
<link rel="stylesheet" type="text/css" media="all" href="/css/admin.css">
<title>NZ Outdoor Admin</title>
</head>
<body>
<?php
echo page_header();
if(!array_key_exists('row',$_SESSION))
    do_login_form();
else {
    do_links();
}
echo page_footer();
?>
</body>
</html>
