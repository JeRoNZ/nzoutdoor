<?php

require("../inc/functions.inc");
send_nocache_headers();
require("../inc/connect.inc");
require("../inc/Next.inc");

if (array_key_exists('search',$_POST)){
	$search=trim($_POST['search']);
	$search=preg_replace('/\s\s+/', ' ', $search);
#Aaron Baumber 214 High Street Masterton 5810 

	# Must enter 6 words
	if (!preg_match("/^(\S+\s){5}/",$search)){
		echo json_encode(array('status'=>0,'message'=>"Please enter 6 words from your name and address\nin the order in which they are printed on your magazine flyer."));
		exit();
	}
	$search=$mysqli->real_escape_string($search);
	$sql="select * from sub_subscribers where address_search like '%{$search}%'";
	$res=$mysqli->query($sql);
	$rows=$res->num_rows;
	switch ($rows){
		case "0":
			echo json_encode(array('status'=>0,'message'=>'Your details were not found'));
			exit();
			break;
		case "1":
			break;
		default:
			echo json_encode(array('status'=>0,'message'=>"Your details were not found.\nPlease contact us on 07 577 9931"));
			exit();
			break;
	}
	$row=$res->fetch_assoc();
	echo json_encode(array('status'=>1,'rid'=>$row['renewal_id']));
	exit();
}

$code=$mysqli->real_escape_string(trim($_POST['cid']));

$sql="select * from sub_subscribers where renewal_id='{$code}'";

$res=$mysqli->query($sql);

if ($res->num_rows != 1){
        echo json_encode(array('status'=>0,'message'=>'Your details were not found'));
        exit();
}

$row=$res->fetch_assoc();

foreach ($row as &$value){
	if (is_null($value))
		$value='';
}

$bob = new next_issue();
$current = $bob->current();

if ($row['last_issue'] > $current){
	echo json_encode(array('status'=>0,'message'=>'Your subscription is not due for renewal'));
	exit();
}

$array=array(
	'email'=>$row['email'],
	'title'=>$row['title'],
	'company'=>$row['company'],
	'forename'=>$row['forename'],
	'surname'=>$row['surname'],
	'phone'=>$row['phone'],
	'mobile'=>$row['mobile'],
	'address1'=>$row['address1'],
	'address2'=>$row['address2'],
	'city'=>$row['city'],
	'country'=>$row['country'],
	'notes'=>$row['notes'],
	'pcode'=>$row['pcode']);

if ($row['country']==153)
	$array['region']=$row['region'];

$array2=array();
if ($row['gift']=='Y'){
	$array['gift']='Y';
	$array2=array(
		'name2'=>$row['name2'],
		'email2'=>$row['email2'],
		'phone2'=>$row['phone2'],
		'mobile2'=>$row['mobile2'],
		'address12'=>$row['address12'],
		'address22'=>$row['address22'],
		'city2'=>$row['city2'],
		'country2'=>$row['country2'],
		'pcode2'=>$row['pcode2']);
	if ($row['country2']==153)
		$array2['region2']=$row['region2'];
}

$new=array_merge($array,$array2);
$new['status']=1;
echo json_encode($new);
?>
