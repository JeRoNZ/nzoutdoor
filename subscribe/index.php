<?php

// Make sure we're on the right host or the final step with the hard coded
// paymate return URL will fail


/*********************************
if ($_SERVER['HTTP_HOST'] != 'www.nzoutdoor.co.nz'){
	header("Location: http://www.nzoutdoor.co.nz/subscribe/");
	exit();
}
*************************************/

// clear these in case there's a new attempt to sign up.
session_start();
unset($_SESSION);
require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/Next.inc");
require("../inc/Updateform.inc");

require("head.inc");

?>
<div id="sub_wrapper">
<div id="subscribediv">
<p class="heading_large">Subscribe Online</p>
<?php
$bob = new update_form();
if (array_key_exists('rid',$_GET)){
    $bob->get_rid($_GET['rid']);
}
echo $bob->merge();?>
</div><!--subscribediv-->
</div><!--sub_wrapper-->
<script type='text/javascript' src='/js/subscribe.js?t=<?php echo time()?>'></script>
<? require("foot.inc");?>
