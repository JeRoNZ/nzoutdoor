<?php
require("../inc/connect.inc");

$handle=fopen("Subscribers_addresses_Current.csv","r");

$sql="insert into sub_subscribers
(title,forename,surname,address1,address2,city,region,pcode,country,notes,id)
values (?,?,?,?,?,?,?,?,?,?,?)";
$stmt=$mysqli->prepare($sql);

$sql="select id from countries where name like ?";

$cnt=$mysqli->prepare($sql);

while ($row=fgetcsv($handle)){
	$forename=ucwords(strtolower(trim($row[0])));
	$surname=ucwords(strtolower(trim($row[1])));
	$bits=explode(" ",$surname);
	switch($bits[0]){
		case "Mr":
		case "Mrs":
		case "Miss":
		case "Ms":
			$title=$bits[0];
			$surname=trim(substr($surname,strlen($bits[0])));
			break;
		default:
			$title='';
			break;
	}

	$address1=ucwords(strtolower(trim($row[2])));
	$address2=ucwords(strtolower(trim($row[3])));
	$city=ucwords(strtolower(trim($row[4].' '.$row[5])));
	$pcode=ucwords(strtolower(trim($row[6])));
	$region=ucwords(strtolower(trim($row[7])));
	$country=ucwords(strtolower(trim($row[8])));

	if($country=='')
		$country=153;
	else{
		$var='%'.$country.'%';
		$cnt->bind_param('s',$var);
		$cnt->execute();
		$cnt->store_result();
		if($cnt->num_rows==1){
			$cnt->bind_result($country);
			$cnt->fetch();
		}
		$cnt->free_result();
	}

	$id=trim($row[9]);
	$notes='';
	for ($i=0;$i<count($row);$i++){
		$notes.=$row[$i].' ';
	}
	$notes=trim($notes);
	$stmt->bind_param('ssssssssdsd',$title,$forename,$surname,$address1,$address2,$city,$region,$pcode,$country,$notes,$id);
	$stmt->execute();
}
fclose($handle);

#company	Name	Address1	Address2	Address3	State	pcode	Region	Country	ID

