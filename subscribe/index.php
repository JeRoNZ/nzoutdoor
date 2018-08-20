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
	<p>If you are renewing an existing subscription please enter the subscriber code provided on our address label above your name. Make sure you click &quot;Go&quot;.</p>
	<p>If you are renewing, and can't find your subscriber ID, please email us on <a href="mailto:info@nzoutdoor.co.nz">info@nzoutdoor.co.nz</a>. </p>
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
