<?php
class update_form {
    private $template;
    private $admin;
    private $data;
    public $xact;

    function region($region=''){
        $out='';
        $array=array( 'Please select', 'Auckland', 'Bay of Plenty', 'Canterbury', 'Gisborne', 'Hawke\'s Bay', 'Manawatu-Wanganui', 'Marlborough', 'Nelson', 'Northland', 'Otago', 'Southland', 'Taranaki', 'Tasman', 'Waikato', 'Wellington', 'West Coast','Chatham Islands');
        foreach($array as $value){
            $sel='';
            if ($region==$value)
                $sel=' selected="selected"';
            $out.='<option'.$sel.'>'.$value.'</option>';
        }
        return $out;
    }
    function years($years=1){
        $sub[1]="One year<br/>";
        $sub[2]="Two years<br/>";
        $sub[5]="Five years<br/>";
        $sub[6]="Two years + free 75 Year Birthday Issue<br/>";
        $sub[7]="Five years + free &quot;TV Wild&quot; DVD<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and 75 Year Birthday Issue";
        $out='';
        for ($i=1;$i<=7;$i++){
            $check='';
            if (array_key_exists($i,$sub)){
                if ($i==$years)
                    $check=' checked="checked"';
                $out.='<input type="radio" name="package" value="'.$i.'" id="sub'.$i.'"'.$check.'>'.$sub[$i];
            }
        }
        return $out;
    }
    function method($pm='P'){
        $out='';
        $text=array('','Paymate','Cheque','Cash/Eftpos','Free');
        $value=array('','P','Q','C','F');
        $template='<input type="radio" [C] name="method" value="[V]" id="pm[I]">[T]<br>';
        for($i=1;$i<=4;$i++){
            $sel='';
            if ($pm==$value[$i])
                $sel='checked="checked"';
            $tmp=str_replace('[C]',$sel,$template);
            $tmp=str_replace('[V]',$value[$i],$tmp);
            $tmp=str_replace('[I]',$i,$tmp);
            $tmp=str_replace('[T]',$text[$i],$tmp);
            $out.=$tmp;
        }
        return $out;
    }

    function title($tit=''){
        $tits=array('','Mr','Mrs','Ms','Miss','Dr','Rev','Prof');
        $out='';
        foreach ($tits as $value){
            $sel='';
            if ($value==$tit)
                $sel=' selected="selected"';
            $out.='<option'.$sel.'>'.$value.'</option>';
        }
        return $out;
    }

    function paymate_response($res=''){
        $values=array(''=>'Not set', 'PD'=>'Declined','PA'=>'Accepted','PP'=>'Pending');
        $out='';
        foreach ($values as $key => $value){
            $sel='';
            if ($key==$res)
                $sel=' selected="selected"';
            $out.='<option value="'.$key.'"'.$sel.'>'.$value.'</option>';
        }
        return $out;
    }

    function get_rid($rid){
        $this->data['renewal_id']='value="'.$rid.'"';
    }

    function merge(){
        $data=$this->data;
        if (!$this->admin){
            $data['region']=$this->region();
            $data['title']=$this->title();
            $data['country']=load_countries();
            $data['years']=$this->years();
            $data['region2']=$this->region();
            $data['country2']=load_countries();
        }
        $template=$this->template;
        $template=str_replace("[RENEWAL_ID]",$data['renewal_id'],$template);
        $template=str_replace("[ID]",$data['id'],$template);
        $template=str_replace("[EMAIL]",$data['email'],$template);
        $template=str_replace("[COMPANY]",$data['company'],$template);
        $template=str_replace("[FORENAME]",$data['forename'],$template);
        $template=str_replace("[SURNAME]",$data['surname'],$template);
        $template=str_replace("[ADDRESS1]",$data['address1'],$template);
        $template=str_replace("[ADDRESS2]",$data['address2'],$template);
        $template=str_replace("[CITY]",$data['city'],$template);
        $template=str_replace("[COUNTRY]",$data['country'],$template);
        $template=str_replace("[REGION]",$data['region'],$template);
        $template=str_replace("[REGIONO]",$data['regiono'],$template);
        $template=str_replace("[TITLE]",$data['title'],$template);
        $template=str_replace("[PCODE]",$data['pcode'],$template);
        $template=str_replace("[PHONE]",$data['phone'],$template);
        $template=str_replace("[MOBILE]",$data['mobile'],$template);
        $template=str_replace("[NOTES]",$data['notes'],$template);
        $template=str_replace("[RENEWAL]",$data['renewal'],$template);
        $template=str_replace("[YEARS]",$data['years'],$template);
        $template=str_replace("[RENEW_BTN]",$data['renew_btn'],$template);

        if ($this->admin) {
            $template=str_replace("[LAST]",$data['last_issue'],$template);
            $template=str_replace("[FIRST]",$data['first_issue'],$template);

            $bob = new next_issue();
            $template=str_replace("[CURRENT]",$bob->format(),$template);
            $bob = new next_issue();
            $template=str_replace("[CURRENT2]",$bob->current(),$template);

            $template=str_replace("[PAYMATE_RESPONSE]",$data['paymate_response'],$template);
            $template=str_replace("[PAYMATE_TXID]",$data['paymate_txid'],$template);
            $template=str_replace("[PAYMATE_TEXT]",nl2br($data['paymate_text']),$template);
            $template=str_replace("[TRANSACTIONS]",$this->xact,$template);
            $search='';
        }
        else{
            $bob = new next_issue();
            $template=str_replace("[FIRST]",$bob->option(1),$template);
            $search='';
/*
            $search=<<<HERE
</td></tr>
<td class="hform"><label for="search_text">..or ID Search</label></td>
<td class="hform2"><input id="search_text" type="text" size="30" title="Enter your name and address as it appears on the flyer that comes with your magazine">
<button id="search">Search</button>
*/
HERE;
        }

        $template=str_replace("[DATE]",$data['date_subscribed'],$template);
        $template=str_replace("[METHOD]",$data['method'],$template);
        $template=str_replace("[ADMIN]",$data['admin_notes'],$template);
        $template=str_replace("[GIFT]",$data['gift'],$template);
        $template=str_replace("[FREE]",$data['free'],$template);
        $template=str_replace("[EMAIL2]",$data['email2'],$template);
        $template=str_replace("[NAME2]",$data['name2'],$template);
        $template=str_replace("[ADDRESS12]",$data['address12'],$template);
        $template=str_replace("[ADDRESS22]",$data['address22'],$template);
        $template=str_replace("[CITY2]",$data['city2'],$template);
        $template=str_replace("[COUNTRY2]",$data['country2'],$template);
        $template=str_replace("[REGION2]",$data['region2'],$template);
        $template=str_replace("[REGIONO2]",$data['regiono2'],$template);
        $template=str_replace("[PCODE2]",$data['pcode2'],$template);
        $template=str_replace("[PHONE2]",$data['phone2'],$template);
        $template=str_replace("[MOBILE2]",$data['mobile2'],$template);
        $template=str_replace("[SEARCH]",$search,$template);

        return $template;
    }

    private function set_new(){
        $array=array('paymate_response'=>'','region'=>'','region2'=>'','country'=>'','country2'=>'','years'=>1,'title'=>'','method'=>'C');
        $this->set($array);
	if ($this->admin){
        	$y=date("Y");
        	$m=date("n")-2;
		$d=date("j");
        	if ($m<2){
            		$m=12;
            		$y--;
        	}
		while(($n=mktime(0,0,0,$m,$d,$y))===FALSE){
			$d--;
		}
        	$bob = new next_issue($n);
        	$this->data['first_issue']=$bob->option(1.2);
	}else{
        	$bob = new next_issue();
        	$this->data['first_issue']=$bob->option(1);
	}

        $y=date("Y");
        $m=date("n")+10;
	$d=date("j");
	if ($this->admin)
		$m=date("n")+8;
        if ($m>12){
            $m=$m%12;
            $y++;
        }
        #$n=mktime(0,0,0,$m,date("j")+10,$y);
// no idea why I'm adding 10 days to what could be the 31st of the month
	while(($n=mktime(0,0,0,$m,$d,$y))===FALSE){
		$d--;
	}
        $bob = new next_issue($n);
	if ($this->admin)
		$this->data['last_issue']=$bob->option(5.2);
	else
        	$this->data['last_issue']=$bob->option(5);
        $this->data['renew_btn']='';
        return NULL;
    }

    function set($data){
        if (!is_array($data))
            return $this->set_new();

        $data['regiono']=$data['region'];
        $data['regiono2']=$data['region2'];
        foreach($data as $key=>$value){
            switch($key){
                case 'notes':
                case 'admin_notes':
                case 'paymate_txid':
                case 'paymate_text':
                    $this->data[$key]=htmlspecialchars($value);
                    break;
                case 'paymate_response':
                    $this->data[$key]=$this->paymate_response($value);
                    break;
                case 'first_issue';
                case 'last_issue';
                    if (($value==0) or (is_null($value)))
                        $stamp=0;
                    else {
                        $y=substr($value,0,4)-5;
                        $m=substr($value,4,2);
                        $stamp=mktime(0,0,0,$m,1,$y);
                    }
                    $bob = new next_issue($stamp);
                    $bob->default=$data[$key];
                    $this->data[$key]=$bob->option(12); // allow a 12 year range (-5 +7)
                    break;

                case 'region':
                case 'region2':
                    $this->data[$key]=$this->region($value);
                    break;
                case 'country':
                case 'country2':
                    $this->data[$key]=load_countries($value);
                    break;
                case 'renewal':
                case 'gift':
                case 'free':
                    if ($value=='Y')
                        $this->data[$key]=' checked="checked"';
                    break;
                case 'years':
                    $this->data['years']=$this->years($value);
                    break;
                case 'title':
                    $this->data['title']=$this->title($value);
                    break;
                case 'method':
                    $this->data['method']=$this->method($value);
                    break;
                default:
                    $this->data[$key]='value="'.htmlspecialchars($value).'"';
                    break;
            }
        }
        $this->data['renew_btn']=<<<HERE
<input type="submit" id="renew_btn" value="Renew">
HERE;
    }

    function __construct($admin=FALSE){
        $this->data=array();
        $this->admin=$admin;
        if ($admin)
            $post=$_SERVER['PHP_SELF'];
        else
            $post='process.php';
        $template='<form id="subscribeform" method="post" action="'.$post.'">';
        if ($admin){
            $template.=<<<HERE
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
HERE;
        }
        $template.=<<<HERE
<table id="subscribetable" border="0" cellpadding="0" cellspacing="0">
HERE;

        if (!$admin){
            $template.=<<<HERE
<tbody>
<tr>
<td class="hform"><label for="renewal">Renewal?</label></td>
<td class="hform2"><input [RENEWAL] type="checkbox" name="renewal" id="renewal" >
</td>
</tr>
</tbody>

<tbody id="renewal_bit">
<tr><td colspan="2" class="hform"><p style="font-size:10pt">
If you are renewing an existing subscription it is important that you enter the subscriber code provided
on your renewal notice (found on your address label above your name) in order to get the renewal price. <b>Make sure you
click &quot;Go&quot;</b>.
 
<p style="font-size:10pt">If you are renewing, and can't find your subscriber ID, please call us on <b>+64 (0)7 577 9931</b>.
</td></tr>
<tr>
<td class="hform"><label for="renewal_id">Subscriber ID..</label></td>
<td class="hform2"><input [RENEWAL_ID] type="text" size="10" maxlength="10" name="renewal_pw" id="renewal_pw" disabled="disabled">
<button id="lookup">Go</button>
[SEARCH]
</td>
</tr>
</tbody>
HERE;
        }

        $template.=<<<HERE
<tbody>
<tr>
<td class="hform"><label for="title">Title</label></td>
<td class="hform2"><select name="title" id="title">
[TITLE]
</select>
</td>
</tr>
<tr>
<td class="hform"><label for="forename">First name</label></td>
<td class="hform2"><input [FORENAME] type="text" name="forename" id="forename" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="surname">Last name</label></td>
<td class="hform2"><input [SURNAME] type="text" name="surname" id="surname" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="company">Company / Institution</label></td>
<td class="hform2"><input [COMPANY] type="text" name="company" id="company" size="60" ></td>
</tr>
<tr>
<td class="hform"><label for="email">Email</label></td>
<td class="hform2"><input [EMAIL] type="text" name="email" id="email" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="phone">Telephone</label></td>
<td class="hform2"><input [PHONE] type="text" name="phone" id="phone" size="20" ></td>
</tr>
<tr>
<td class="hform"><label for="mobile">Mobile</label></td>
<td class="hform2"><input [MOBILE] type="text" name="mobile" id="mobile" size="20" ></td>
</tr>
<tr>
<td class="hform"><label for="address1">Address</label></td>
<td class="hform2"><input [ADDRESS1] type="text" name="address1" id="address1" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="address2">Address</label></td>
<td class="hform2"><input [ADDRESS2] type="text" name="address2" id="address2" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="city">City</label></td>
<td class="hform2"><input [CITY] type="text" name="city" id="city" size="40" ></td>
</tr>

<tr>
<td class="hform"><label for="country">Country</label></td>
<td class="hform2"><select name="country" id="country">
[COUNTRY]
</select>
</td>
</tr>

<tr id="region_row">
<td class="hform"><label for="region">Region</label></td>
<td class="hform2"><select name="region" id="region">
[REGION]
</select>
</td>
</tr>

<tr id="region_other_row">
<td class="hform"><label for="regiono">Region</label></td>
<td class="hform2"><input [REGIONO] type="text" name="regiono" id="regiono" size="40" ></td>
</tr>

<tr>
<td class="hform"><label for="pcode">Postcode</label></td>
<td class="hform2"><input style="vertical-align:middle" [PCODE] type="text" name="pcode" id="pcode" size="8" >
<a href="http://tools.nzpost.co.nz/tools/address-postcode-finder/" target="_blank">
<img alt="nzpost" border="0" title="Search NZ Postcodes" src="/images/nzpost.gif" style="vertical-align:middle"></a>
</td>
</tr>

<tr><td class="hform"><label for="gift">Gift subscription</label></td>
<td class="hform2"><input [GIFT] type="checkbox" id="gift" name="gift" ></td></tr>
</tbody>

<tbody id="gift_bit">
<tr>
<td class="hform"><label for="name2">Recipient name</label></td>
<td class="hform2"><input [NAME2] type="text" name="name2" id="name2" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="email2">Email</label></td>
<td class="hform2"><input [EMAIL2] type="text" name="email2" id="email2" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="phone2">Telephone</label></td>
<td class="hform2"><input [PHONE2] type="text" name="phone2" id="phone2" size="20" ></td>
</tr>
<tr>
<td class="hform"><label for="mobile2">Mobile</label></td>
<td class="hform2"><input [MOBILE2] type="text" name="mobile2" id="mobile2" size="20" ></td>
</tr>
<tr>
<td class="hform"><label for="address12">Address</label></td>
<td class="hform2"><input [ADDRESS12] type="text" name="address12" id="address12" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="address22">Address</label></td>
<td class="hform2"><input [ADDRESS22] type="text" name="address22" id="address22" size="40" ></td>
</tr>
<tr>
<td class="hform"><label for="city2">City</label></td>
<td class="hform2"><input [CITY2] type="text" name="city2" id="city2" size="40" ></td>
</tr>

<tr>
<td class="hform"><label for="country2">Country</label></td>
<td class="hform2"><select name="country2" id="country2">
[COUNTRY2]
</select>
</td>
</tr>
<tr id="region_row2">
<td class="hform"><label for="region2">Region</label></td>
<td class="hform2"><select name="region2" id="region2">
[REGION2]
</select>
</td>
</tr>

<tr id="region_other_row2">
<td class="hform"><label for="regiono2">Region</label></td>
<td class="hform2"><input [REGIONO2] type="text" name="regiono2" id="regiono2" size="40" ></td>
</tr>

<tr>
<td class="hform"><label for="pcode2">Postcode</label>
</td>
<td class="hform2">
<input style="vertical-align:middle" [PCODE2] type="text" name="pcode2" id="pcode2" size="8" >
<a href="http://tools.nzpost.co.nz/tools/address-postcode-finder/" target="_blank">
<img alt="nzpost" style="vertical-align:middle" border="0" title="Search NZ Postcodes" src="/images/nzpost.gif"></a>
</td>
</tr>
<tr>
<td class="hform"><label for="notes">Message</label></td>
<td class="hform2"><textarea id="notes" name="notes" cols="40" rows="5">[NOTES]</textarea></td>
</tr>
</tbody>

<tbody>
HERE;
        $template.=<<<HERE
<tr>
<td class="hform"><label for="sub1">Subscription</label></td>
<td class="hform2">
[YEARS]
</td>
</tr>
<tr>
<td class="hform"><label for="issue">First issue</label></td>
<td class="hform2"><select name="issue" id="issue">[FIRST]
</select>
</td>
</tr>
HERE;
        if ($admin){
            $template.=<<<HERE
    <tr>
    <td class="hform"><label for="last_issue">Last issue</label></td>
    <td class="hform2"><select name="last_issue" id="last_issue">[LAST]
    </select>
    </td>
    </tr>
    <tr>
    <td class="hform">Current issue</td>
    <td class="hform2">[CURRENT]
    </td>
    </tr>
HERE;
        }
        $template.=<<<HERE
<tr><td class="hform" colspan="2" align="center">
HERE;
        if ($admin){
            $template.=<<<HERE
<input type="submit" name="action" id="Update" value="Save">
<input type="submit" name="Quit" id="Quit" value="Quit">
[RENEW_BTN]
<input type="hidden" name="id" id="id" [ID]>
<input type="hidden" name="renewal" id="renewal" value="N">
<input type="hidden" id="current" value="[CURRENT2]">
HERE;
        }
        else
        {
            $template.=<<<HERE
<input type="submit" name="signup" id="signup" value="Submit">
<input type="hidden" name="cid" id="cid">
HERE;
        }
        $template.=<<<HERE
<input type="reset" name="reset" id="reset" value="Reset">
</td>
</tr>
</tbody>
</table>
HERE;
        if ($admin){
// date_subscribed
            $template.=<<<HERE
</td>
<td valign="top">
    <table>
    <tr>
    <td class="hform"><label for="admin_notes">Admin Notes</label></td>
    <td class="hform2"><textarea id="admin_notes" name="admin_notes" cols="40" rows="5">[ADMIN]</textarea></td>
    </tr>
    <tr>
    <td class="hform"><label for="pm1">Payment Method</label></td>
    <td class="hform2">
[METHOD]
    </td>
    </tr>

    <tr>
    <td class="hform"><label for="free">Complimentary sub</label></td>
    <td class="hform2"><input [FREE] type="checkbox" name="free" id="free" ></td>
    </tr>

    <tr>
    <td class="hform"><label for="paymate_response">Paymate Response</label></td>
    <td class="hform2"><select id="paymate_response" name="paymate_response">[PAYMATE_RESPONSE]</select></td>
    </tr>

    <tr><td>Paymate Transaction ID</td><td>[PAYMATE_TXID]</td></tr>
    <tr><td>Renewals</td><td>[TRANSACTIONS]</td></tr>

    <tr><td>Paymate text</td><td id="Paymatedata">[PAYMATE_TEXT]</td></tr>
    </table>
</td>
</tr>
</table>
HERE;
        }
        $template.=<<<HERE
</form>
HERE;
        $this->template=$template;
    }
}
?>
