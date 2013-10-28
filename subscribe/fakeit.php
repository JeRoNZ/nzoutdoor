<?php
session_start();
$ret=urldecode($_SESSION['paymate']['return']);
?>
</pre>
<html>
<body>
<p>Pretend to type in card details...
<form method=POST action="<?php echo $ret;?>">
<input type=hidden name="transactionID" value="1245678901">
<input type=hidden name="responseCode" value="PA">
<input type=hidden name="paymentAmount" value="<? echo $_SESSION['paymate']['amt']?>">
<input type=hidden name="currency" value="NZD">
<input type=hidden name="paymentDate" value="paymentDate">
<input type=hidden name="ref" value="ref">
<input type=hidden name="buyerEmail" value="buyerEmail">
<input type=hidden name="billingAddress1" value="billingAddress1">
<input type=hidden name="billingAddress2" value="billingAddress2">
<input type=hidden name="billingCity" value="billingCity">
<input type=hidden name="billingState" value="billingState">
<input type=hidden name="billingCountry" value="billingCountry">
<input type=hidden name="billingPostcode" value="billingPostcode">
<input type=hidden name="billingFirstName" value="billingFirstName">
<input type=hidden name="billingSurname" value="billingSurname">
<input type=submit>
</form>
<p>...and click submit
</body>
</html>
