<?php
require("../inc/securelink.php");

if (! array_key_exists('auth', $_GET)) {
	header('Location: /');
	die();
}

$value = SecureLink::getToken($_GET['auth']);
if (!$value) {
        header('Location: /');
        die();
}
if (!is_array($value)) {
        header('Location: /');
        die();
}


require("../inc/connect.inc");
include("../inc/adodb5/adodb-exceptions.inc.php");
include("../inc/adodb5/adodb.inc.php");

if (!@$db) {
    try {
        $db = NewADOConnection('mysqli');
        if (($db->PConnect(HOST, USER, PASS, DB)) === false)
            die('DB ERROR: ' . $db->ErrorNo());
    }
    catch (Exception $e) {
        die ('This site is currently offline. Please try again later.');
    }
}

$sql = 'SELECT id FROM sub_subscribers WHERE id=?';

$row = $db->getOne($sql,array($value['renewal_id']));

if ($row) {
        header('Location: /');
        die();
}

$sql = "UPDATE sub_subscribers SET reminderOptOut='N' WHERE id=?";

$db->execute($sql,array($row));
?>
<h1>You have been unsubscribed</h1>
