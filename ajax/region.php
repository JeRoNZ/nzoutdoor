<?php
if (! $_SERVER['REQUEST_METHOD']=='POST')
    die(':-(');
if(!array_key_exists('city',$_POST))
    die(':-((');
require("../inc/functions.inc");
send_nocache_headers();
require("../inc/connect.inc");

#$_POST['city']='Tauranga';

$sql="select * from sub_regions where city=?";
$stmt=$mysqli->prepare($sql);
$stmt->bind_param("s",trim($_POST['city']));
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 1){
    $stmt->bind_result($city,$region);
    $stmt->fetch();
    echo json_encode(array('status'=>1,'region'=>$region,'city'=>$city));
    exit();
}
echo json_encode(array('status'=>0));
?>
