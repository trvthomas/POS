<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";

if (isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	$sql = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "' AND inventory=1";
	$result = $conn->query($sql);

	if ($result->num_rows <= 0) {
		verifyTemporaryAccess();
	}
} else if (!isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "UsernameUser"]) || isset($_COOKIE[$prefixCoookie . "IdUser"]) && !isset($_COOKIE[$prefixCoookie . "UsernameUser"]) || !isset($_COOKIE[$prefixCoookie . "IdUser"]) && !isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	redirectTemporary();
}

function verifyTemporaryAccess()
{
	global $prefixCoookie, $conn;

	if (isset($_COOKIE[$prefixCoookie . "TemporaryInventoryIdUser"]) && isset($_COOKIE[$prefixCoookie . "TemporaryInventoryUsernameUser"])) {
		$sql2 = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryInventoryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryInventoryUsernameUser"] . "' AND inventory=1";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows <= 0) {
			redirectTemporary();
		}
	} else {
		redirectTemporary();
	}
}

if (isset($_COOKIE[$prefixCoookie . "IdUser"]) && isset($_COOKIE[$prefixCoookie . "DateEnter"]) && $_COOKIE[$prefixCoookie . "DateEnter"] != date("Y-m-d")) {
	header("Location:/trv/alert-new-day.php");
}

function redirectTemporary()
{
	header("Location:temporary-access.php");
}
?>