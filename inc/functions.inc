<?php

function send_nocache_headers()
{
// Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");

// HTTP/1.0
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
}

function load_countries($id=153){
    global $mysqli;
    $res=$mysqli->query("select id,name from sub_countries order by sort,name");
    $out='<option value="0">Please select</option>';
    while($row=$res->fetch_assoc()) {
        $sel='';
        if($row['id']==$id)
            $sel=' selected="selected"';
        $out.="<option{$sel} value=\"{$row['id']}\">".htmlspecialchars($row['name']).'</option>';
    }
    $res->free;
    return $out;
}


function LuhnCheck($strDigits,$get=false)
{
    $sum = 0;
    $alt = $get;
    for($i = strlen($strDigits) - 1; $i >= 0; $i--)
    {
        if($alt)
        {
           $temp = $strDigits[$i];
           $temp *= 2;
           $strDigits[$i] = ($temp > 9) ? $temp = $temp - 9 : $temp;
        }
        $sum += $strDigits[$i];
        $alt = !$alt;
    }
    if ($get)
        return (10-($sum%10)) % 10;
    else
        return $sum % 10 == 0;
}

function make_code($id){
    $f=LuhnCheck((string)$id,true);
    $val=(string)$id.$f;
    $f=LuhnCheck($val,true);
    $val=(string)$val.$f;
    $f=LuhnCheck($val,true);
    $val=(string)$val.$f;
    $chars=chr(rand(65,90)).chr(rand(65,90));
    return $chars.strrev($val);
}

/*function make_id($code){
    $code=(int) $code;
    $val=strrev(trim($code));
    $val=substr($val,0,strlen($val)-1);
    if (LuhnCheck($val)){
        $val=substr($val,0,strlen($val)-1);
        if (LuhnCheck($val)){
            $val=substr($val,0,strlen($val)-1);
            if (LuhnCheck($val)){
                return $val;
            }
        }
    }
    return false;
}*/

function check_admin_cookie($exit=FALSE){
    if (array_key_exists('row',$_SESSION)) {
        if ($_SESSION['admin']+ADMIN_TIMEOUT < time()){
            session_destroy();
            unset($_SESSION);
            setcookie('PHPSESSID','',1,'/');
            return FALSE;
        }
        else {
            $_SESSION['admin']=time();
            return TRUE;
        }
    }
    if ($exit) {
        if (!array_key_exists('row',$_SESSION)) {
            header("Location: ".ADMIN_URL);
            exit();
        }
    }
    return FALSE;
}

function do_row($row,$extra=FALSE){
    global $mysqli;
    if ($row['gift'] == 'Y'){
        $name=csv_quote(trim($row['name2']));
	$company=csv_quote('');
        $address1=csv_quote(trim($row['address12']));
        $address2=csv_quote(trim($row['address22']));
        $address3=csv_quote($row['city2']);
        $address4=csv_quote($row['region2']);
        $address5=csv_quote($row['pcode2']);
        $address6= $row['country2'];
	$phone=csv_quote($row['phone2']);
    }
    else{
        $name=trim($row['title'].' '.$row['forename'].' '.$row['surname']);
        $company=trim($row['company']);
        $name=preg_replace('/\s\s+/', ' ', $name);
        $company=preg_replace('/\s\s+/', ' ', $company);
        $name=csv_quote($name);
        $company=csv_quote($company);
        $address1=csv_quote(trim($row['address1']));
        $address2=csv_quote(trim($row['address2']));
        $address3=csv_quote($row['city']);
        $address4=csv_quote($row['region']);
        $address5=csv_quote($row['pcode']);
        $address6= $row['country'];
	$phone=csv_quote($row['phone']);
    }
    $code= csv_quote($row['renewal_id']);
    $last_issue= csv_quote($row['last_issue']);
    if ($address6==153)
        $address6='""';
    else {
        $sql="select name from sub_countries where id=$address6";
        $res=$mysqli->query($sql);
        $country=$res->fetch_assoc();
        $address6=csv_quote($country['name']);
    }
    $return = $name.','.$company.','.$address1.','.$address2.','.$address3.','.$address4.','.$address5.','.$address6.','.$code;
    if ($extra)
	$return.=','.$phone;
	$return.=','.$last_issue;
    return $return."\r\n";
}

function csv_quote($string)
{
    return '"'.preg_replace ( "/\"/",'""',$string).'"' ;
}


function last_issue($first_issue,$years){
    $y=substr($first_issue,0,4);
    $m=substr($first_issue,4,2);

    $y+=($years-1);

    $M=$m+10;
    if ($M > 12) $y++;
    $m = $M >12 ? $M%12 : $M;

    $last_issue=$y.sprintf("%02d",$m);
    return $last_issue;
}

function first_issue($last_issue,$years){
    $y=substr($last_issue,0,4);
    $m=substr($last_issue,4,2);

    $y-=($years-1);

    $M=$m-10;
    if ($M <=0) $y--;
    $m = $M <=0 ? $M+12 : $M;

    $first_issue=$y.sprintf("%02d",$m);
    return $first_issue;
}

?>
