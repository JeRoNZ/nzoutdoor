<?php

if (isset($_SERVER['REQUEST_METHOD']))
    die('Shell only');

require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/securelink.php");
include("../inc/adodb5/adodb-exceptions.inc.php");
include("../inc/adodb5/adodb.inc.php");
require("../inc/Next.inc");
include("head.inc");
include("foot.inc");
require_once 'Mail/mime.php';

function sendEmail($row) {
    $array = array('eol' => "\n");
    $mime = new Mail_mime($array);

    $tpl = file_get_contents('templates/expired.xhtml');
    $tpl = str_replace('[NAME]', $row['forename'], $tpl);
    $tpl = str_replace('[SURNAME]', $row['surname'], $tpl);
    $tpl = str_replace('[SID]', $row['renewal_id'], $tpl);
    $unsub = SecureLink::setToken(array('renewal_id' => $row['renewal_id'], 'email' => $row['email']), true);
    $tpl = str_replace('[UNSUB]', $unsub, $tpl);
    $mime->setHTMLBody($tpl);
    $body = $mime->get();
    $hdrs = $mime->headers(array());
    $extra = '';
    foreach ($hdrs as $k => $v) {
	$extra .= $k . ': ' . $v . PHP_EOL;
    }
    $extra.='From: info@nzoutdoor.co.nz' . PHP_EOL;
    $res = @mail($row['email'], 'NZ Outdoor Hunting Magazine Subscription Reminder', $body, $extra, '-f info@nzoutdoor.co.nz');
    
}

if (!@$db) {
    try {
	$db = NewADOConnection('mysqli');
	if (($db->PConnect(HOST, USER, PASS, DB)) === false)
	    die('DB ERROR: ' . $db->ErrorNo());
    } catch (Exception $e) {
	die('Database connection failed!');
    }
}

$bob = new next_issue();
$year = $bob->current(); // 201312, so we what
$y = substr($year,0,4);
$m = substr($year,4,2);
$m -= 4;
// 201406 => 201402
// 201404 => 201312
// 201402 => 201310
// 201312 => 201308
if ($m <= 0) {
    $m+=12;
    $y--;
}
$first = $y.sprintf("%02d",$m);

$sql = "SELECT * FROM sub_subscribers
	WHERE country=153
	AND last_issue < $year
	AND last_issue >= $first
	AND free='N'
	AND email LIKE '%@%'
	AND reminderOptOut != 'Y'
	ORDER BY last_issue DESC";

$res = $db->Execute($sql);
while ($row = $res->FetchRow()) {
    if ($row['email']) {
	usleep(200000); // 0.2 seconds between each mail
	echo $row['last_issue'].' '.$row['email'];
	sendEmail($row);
    }
}