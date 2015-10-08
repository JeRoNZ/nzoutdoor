<?php
require("../inc/connect.inc");
require("../inc/functions.inc");
require("../inc/Next.inc");
if (! isset($_GET['id']))
	die('NO');
if (! ctype_digit($_GET['id']))
	die('NO');

define('ID',$_GET['id']);

session_start();
check_admin_cookie(TRUE);


$sql="select data from sub_temp where id=".ID;

$res=$mysqli->query($sql);
$row=$res->fetch_assoc();

$_SESSION['POST']=unserialize($row['data']);
var_dump($_SESSION['POST']);

$_POST['responseCode']='PP';
$_POST['transactionID']='none';

$rid=0;
$renewal='N';
$old=array();

if (array_key_exists('cid',$_SESSION['POST'])){
    $renewal_id=$mysqli->real_escape_string($_SESSION['POST']['cid']);
    $sql="select * from sub_subscribers where renewal_id='{$renewal_id}'";
    $res=$mysqli->query($sql);
    if ($res->num_rows ==1){
        $old=$res->fetch_assoc();
        $rid=$old['id'];
        $renewal='Y';
    }
}

if (array_key_exists('gift',$_SESSION['POST'])){
    $gift       = 'Y';
    $email2     = $_SESSION['POST']['email2'];
    $name2      = $_SESSION['POST']['name2'];
    $address12  = $_SESSION['POST']['address12'];
    $address22  = $_SESSION['POST']['address22'];
    $city2      = $_SESSION['POST']['city2'];
    $country2   = $_SESSION['POST']['country2'];
    if ($country2==153)
        $region2= $_SESSION['POST']['region2'];
    else
        $region2= $_SESSION['POST']['regiono2'];
    $pcode2     = $_SESSION['POST']['pcode2'];
    $phone2     = $_SESSION['POST']['phone2'];
    $mobile2    = $_SESSION['POST']['mobile2'];
}

$email      = $_SESSION['POST']['email'];
$title      = $_SESSION['POST']['title'];
$company    = $_SESSION['POST']['company'];
$forename   = $_SESSION['POST']['forename'];
$surname    = $_SESSION['POST']['surname'];
$address1   = $_SESSION['POST']['address1'];
$address2   = $_SESSION['POST']['address2'];
$city       = $_SESSION['POST']['city'];
$country    = $_SESSION['POST']['country'];
if ($country==153)
    $region = $_SESSION['POST']['region'];
else
    $region = $_SESSION['POST']['regiono'];
$pcode      = $_SESSION['POST']['pcode'];
$phone      = $_SESSION['POST']['phone'];
$mobile     = $_SESSION['POST']['mobile'];
$notes      = $_SESSION['POST']['notes'];
$years      = $_SESSION['POST']['package'];
$dvd        = $_SESSION['POST']['dvd'];

if ($renewal == 'N'){
    $first_issue=$_SESSION['POST']['issue'];
    $tx_first_issue=$first_issue;
    $last_issue=last_issue($first_issue,$years);
}else{
    $iss = new next_issue();
    $current = $iss->current(); // next issue that will be sent "201102"
    if ($old['last_issue'] < $current){ // Late renewal - or even a resubscription a few months later
        $first_issue=$_SESSION['POST']['issue'];
        $tx_first_issue=$first_issue;
        $last_issue=last_issue($first_issue,$years);
    }
    else // renewal on or before expiry - move end date forward, ignore date requested
    {
        $last_issue=$old['last_issue']+($years*100);
        $first_issue=$old['first_issue'];
        $tx_first_issue=first_issue($last_issue,$years);
    }
}

foreach($_POST as $key => $value){
    $paymate_text.="{$key} = {$value}\n";
}

switch($_POST['responseCode']){
       case 'PP':
       case 'PA':     // even if accepted, a payment can be declined later on, so always assume pending
            $paymate_response='PP';
            break;
       default:
            $paymate_response=$_POST['responseCode'];
            break;
}

$paymate_txid=$_POST['transactionID'];

$stamp=time();

// date_subscribed set only on insert
$insert="insert into sub_subscribers(email,title,company,forename,surname,address1,address2,city,country,region,pcode,phone,mobile,notes,renewal,years,first_issue,last_issue,date_subscribed,method,paymate_response,paymate_txid,paymate_text,gift,email2,name2,address12,address22,city2,country2,region2,pcode2,phone2,mobile2,date_renewed,dvd) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$stamp,'P',?,?,?,?,?,?,?,?,?,?,?,?,?,?,$stamp,?)";

$update="update sub_subscribers set email=?,title=?,company=?,forename=?,surname=?,address1=?,address2=?,city=?,country=?,region=?,pcode=?,phone=?,mobile=?,notes=?,renewal=?,years=?,first_issue=?,last_issue=?,method='P',paymate_response=?,paymate_txid=?,paymate_text=?,gift=?,email2=?,name2=?,address12=?,address22=?,city2=?,country2=?,region2=?,pcode2=?,phone2=?,mobile2=?,date_renewed={$stamp},dvd=? where id=?";

if ($renewal == 'N'){
    $stmt=$mysqli->prepare($insert);
    $stmt->bind_param("ssssssssdssssssdddsdsssssssdsssss",$email,$title,$company,$forename,$surname,$address1,$address2,$city,$country,$region,$pcode,$phone,$mobile,$notes,$renewal,$years,$first_issue,$last_issue,$paymate_response,$paymate_txid,$paymate_text,$gift,$email2,$name2,$address12,$address22,$city2,$country2,$region2,$pcode2,$phone2,$mobile2,$dvd);
} else {
    $stmt=$mysqli->prepare($update);
    $stmt->bind_param("ssssssssdssssssdddsdsssssssdsssssd",$email,$title,$company,$forename,$surname,$address1,$address2,$city,$country,$region,$pcode,$phone,$mobile,$notes,$renewal,$years,$first_issue,$last_issue,$paymate_response,$paymate_txid,$paymate_text,$gift,$email2,$name2,$address12,$address22,$city2,$country2,$region2,$pcode2,$phone2,$mobile2,$dvd,$rid);
}

if ($email != TESTEMAIL)
    $stmt->execute();

// Set the renewal ID code for new entries
if ($renewal == "N"){
    $rid=$stmt->insert_id;
    $renewal_id=make_code($rid);
    if ($email != TESTEMAIL)
        $mysqli->query("Update sub_subscribers set renewal_id='{$renewal_id}' where id=$rid");
}
$stmt->close();

// Add an audit transaction
$sql="insert into sub_renewals (id,first_issue,last_issue,years,method,date) values(?,?,?,?,'P',now())";
$stmt=$mysqli->prepare($sql);
$stmt->bind_param("dddd",$rid,$tx_first_issue,$last_issue,$years);

if ($email != TESTEMAIL)
    $stmt->execute();
$stmt->close();


// Remove debug dump from database;
$sql="delete from sub_temp where id=?";
$stmt=$mysqli->prepare($sql);
$id=ID;
$stmt->bind_param("d",$id);
$stmt->execute();

$Issue='';

$y=substr($first_issue,0,4);
$m=substr($first_issue,4,2);
$stamp=mktime(0,0,0,$m,1,$y);
$Issue=date('F',$stamp).'/';
if (++$m >12){
    $m=$m%12;
    $y++;
}
$stamp=mktime(0,0,0,$m,1,$y);
$Issue.=date('F',$stamp).' '.$y;

$subject="NZ Outdoor Hunting Magazine Subscription";
$headers='From: '.OWNER_NAME.' <'.OWNER_EMAIL.'>';
$mess=<<<HERE
Dear $title $surname,

Thank you for subscribing to NZ Outdoor Hunting Magazine.


HERE;

$amt=sprintf("%5.2f",$_SESSION['paymate']['amt']);
$mess.="You have requested a $years year subscription, which has been charged at NZ \${$amt}\n\n";

if ($dvd=='Y'){
	$mess.="Your subscription includes a copy of the Graf Boys DVD \"Roarin' in Reds\"\n\n";
}

switch ($paymate_response){
    case 'PA':
        $mess.="Your subscription will commence with the {$Issue} issue.\n\n";
        break;
    case 'PD':
        $mess.=<<<HERE
Unfortunately your credit card was declined. Please contact us on (+64) (0)7 577 9931
to discuss alternative payment methods.

HERE;
        break;
    case 'PP':
        $mess.=<<<HERE
Your card payment is currently being processed. Assuming that your payment is successful,
your subscription will commence with the $Issue issue. If for any reason
your payment is not successful, we will contact you to discuss payment options.

HERE;
        break;
}


$mess.=<<<HERE

Please quote your subscriber id, $renewal_id in any correspondence.

---
Kind regards

NZ Outdoor Hunting Magazine

www.nzoutdoor.co.nz
HERE;
###############@mail($email, $subject, $mess, $headers,'-f '.OWNER_EMAIL);

$mess="Copy of subscription message sent to {$title} {$forename} {$surname} {$email} {$renewal_id}\n\n".$mess;

/*if ($email != TESTEMAIL)
	@mail(OWNER_EMAIL, $subject, $mess, $headers,'-f '.OWNER_EMAIL);*/


session_destroy();
unset($_SESSION);

require("head.inc");
?>
<div id="subscribediv">
<p class="heading_large">Subscribe Online</p>
<p>Thank you for subscribing to NZ Outdoor Hunting Magazine!</p></div>
<? require("foot.inc");?>
