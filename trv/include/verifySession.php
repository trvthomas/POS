<?php include_once "DBData.php";

if (isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	$sql = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "'";
	$result = $conn->query($sql);

	if ($result->num_rows <= 0) {
		deleteCookies();
	}
} else if (!isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "UsernameUser"]) || isset($_COOKIE[$prefixCoookie . "IdUser"]) && !isset($_COOKIE[$prefixCoookie . "UsernameUser"]) || !isset($_COOKIE[$prefixCoookie . "IdUser"]) && !isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	deleteCookies();
}

if (isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "DateEnter"]) && $_COOKIE[$prefixCoookie . "DateEnter"] != date("Y-m-d")) {
	header("Location:alert-new-day.php");
}

function deleteCookies()
{
	global $prefixCoookie;
	setcookie($prefixCoookie . "IdUser", "", time() - 3600, "/");
	setcookie($prefixCoookie . "UsernameUser", "", time() - 3600, "/");
	header("Location:login.php");
}

$sql2 = "SELECT * FROM trvsol_configuration WHERE configName='businessName' OR configName='templateInvoice' OR configName='templateDayReport'";
$result2 = $conn->query($sql2);
if ($result2->num_rows <= 0) {
	while ($row2 = $result2->fetch_assoc()) {
		if ($row2["value"] == "") {
			header("Location:prepare-system.php");
		}
	}
}
?>