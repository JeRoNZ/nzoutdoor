<?php
session_start();
require("../inc/connect.inc");
define("TESTEMAIL",'info@jero.co.nz');
define("TESTEMAIL2",'debug@jero.co.nz');

// Hide the initial $_POST values in a session for use later
unset($_SESSION['POST']);
$_POST['package'] = '1';
$_SESSION['POST']=$_POST;

/*
NZ Renewals
1 Year - $44.90
2 Years - $89.90
5 Years $202

as of 22/09/15:
1 Year - $49.90
2 Years - $95
5 Years $220

e.g. no renewal discount

*/

define('NZ',153);
define('AUS',13);


// NZ New Subs
$array[NZ][1]=49.90;
$array[NZ][2]=95;
$array[NZ][5]=220;

// NZ New subscriptions with DVD
$array[NZ][60] = 95; // Two years + free 75 Year Birthday Issue
$array[NZ][70] = 220;    //Five years + free TV Wild DVD and 75 Year Birthday Issue

//NZ Renewals
## 22/09/15 $array[NZ][11]=44.90;
$array[NZ][11]=49.90;
# 22/09/15 $array[NZ][21]=89.90;
$array[NZ][21]=95;
# 22/09/15 $array[NZ][51]=202;
$array[NZ][51]=220;

// NZ Renewals with DVD
//Two years + free 75 Year Birthday Issue
//Five years + free TV Wild DVD and 75 Year Birthday Issue
# 22/09/15 $array[NZ][61] = 89.90;
$array[NZ][61] = 95;
# 22/09/15 $array[NZ][71] = 202;
$array[NZ][71] = 220;

// AU
$array[AUS][1]=104;
#$array[AUS][11]=99;
# email 16/12/15 renewals same price as subs.
$array[AUS][11]=104;


// Foreigners
$array[0][1]=165;

$country=$_POST['country'];
if (array_key_exists('gift',$_POST)){
        $country=$_POST['country2'];
}

if (!ctype_digit($country)){
    header("Location: /");
    die('Skullduggery!');
}

$row=$mysqli->query("select iso from sub_countries where id={$country}")->fetch_assoc();
switch ($country) {
    case AUS:
    case NZ:
        break;
    default:
        $country=0;
}

if (!ctype_digit($_POST['package'])) {
	die('Package');
}

$amt=0;
switch($_POST['package']){
	case '6':
	case '7':
		$_SESSION['POST']['dvd']='Y';
		if ($_POST['cid'] == '')
			$package=$_POST['package'].'0';
		else
			$package=$_POST['package'].'1';
		$_SESSION['POST']['package']= $_POST['package'] == 6 ? 2 : 5;
		break;
	default:
		$_SESSION['POST']['dvd']='N';
		$package=$_POST['package'];
		if ($_POST['cid'] != '') { // renewal
			if ($country==NZ)
				$package=$package.'1';
			if ($country==AUS)
				$package=$package.'1';
		}
		break;
}

$amt=$array[$country][$package];

if ($amt==0)
    die ('Amt');

if ($_POST['email'] == TESTEMAIL){
    $amt=1;
}

$paymate=array('mid'=>'nzoutdoor43',
'currency' => 'NZD',
'amt_editable' => 'N',
'pmt_country' => $row['iso'],
'return' => urlencode(RETURN_URL),
'amt' => $amt,
'ref' => $_POST['email'],
'pmt_sender_email' => $_POST['email'],
'pmt_contact_firstname' => $_POST['forename'],
'pmt_contact_surname' => $_POST['surname'],
'pmt_contact_phone' => $_POST['phone'],
'regindi_address1' => $_POST['address1'],
'regindi_address2' => $_POST['address2'],
'regindi_pcode' =>$_POST['pcode'],
'regindi_state' => $_POST['region'],
'regindi_sub' => $_POST['city']);

$_SESSION['paymate']=$paymate;

$url='https://www.paymate.com/PayMate/ExpressPayment';

// for testing by me
if ($_POST['email'] == TESTEMAIL)
    $url='fakeit.php';
if ($_POST['email'] == TESTEMAIL2)
    $url='fakeit.php';

// Save session into DB for debug purposes - in case they don't return from Paymate
$tempsql='insert into sub_temp (data,IP,useragent) values (?,?,?)';
$sess=serialize($_SESSION['POST']);
$stmt=$mysqli->prepare($tempsql);
$stmt->bind_param("sss",$sess,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT']);
$stmt->execute();
$_SESSION['POST']['temp_id']=$stmt->insert_id;


require("head.inc");
?>
<div id="subscribediv">
<p class="heading_large">Subscribe Online</p>
<p>You are about to purchase a subscription to NZ Outdoor Hunting Magazine<?php
switch ($_POST['package']) {
	case 6:
		echo 'and a free 75th Birthday Issue';
		break;
	case 7:
		#echo ', a free copy of the &quot;TV Wild&quot; DVD and a free 75 Year Birthday Issue';
		echo 'and a free 75th Birthday Issue';
		break;
}
?>
.</p>
<p>Your subscription will cost NZ $<?php echo sprintf("%5.2f",$amt)?>.</p>
<p>Please ensure that you complete the payment process, which will return you to our website.</p>
<p>You should receive an email confirmation from us when the process is complete. If you don't receive the email, please notify info@nzoutdoor.co.nz</p>
<p>Please click the button below to continue.</p>
<form method=post action="<?php echo $url ?>">
<?php
foreach ($_SESSION['paymate'] as $key=>$value){
        echo "<input type=hidden name={$key} value=\"{$value}\">";
}
?>
<button type=submit>
<img src="https://www.paymate.com/images/Paymate-Express-Banner-190x60.gif" border="0" alt="Pay with Paymate Express">
</button>
</form>
</div>
<?php require("foot.inc")?>
