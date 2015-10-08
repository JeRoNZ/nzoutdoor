<?php
session_start();
require("../inc/functions.inc");
require("../inc/connect.inc");
require("../inc/Next.inc");
check_admin_cookie(TRUE);

$sql = "select * from sub_temp order by id desc";

$res = $mysqli->query($sql);

header("Content-type: text/html");

while ($row = $res->fetch_assoc()) {
	?>
	<table>
	<tr>
		<td>Date</td>
		<td><?php
			echo $row['timestamp'];
			?></td>
	</tr>
	<tr>
	<td>ID</td><td><?php
		$a = unserialize($row['data']);
		?><a href="/mag_admin/paymate.php?id=<?php echo $row['id'] ?>"><?php echo $row['id'] ?></a></td></tr><?php
	foreach ($a as $key => $value) {
		?>
		<tr>
		<td><?php echo $key ?></td>
		<td><?php echo htmlspecialchars($value) ?></td></tr><?php
	}
	?>
	<hr/><?php
};
