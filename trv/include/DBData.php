<?php
try {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "trvsol_pos";
	$conn = new mysqli($servername, $username, $password, $dbname);

	if (mysqli_connect_error()) {
		header("Location:prepare-system.php");
	}
} catch (Exception $e) {
	header("Location:prepare-system.php");
}

/* SMTP Email - Modify values */
define('phpmailer_host', 'HOST_NAME');
define('phpmailer_username', 'USERNAME');
define('phpmailer_password', 'PASSWORD');

$sqlVerPS = "SELECT * FROM trvsol_configuration WHERE configName='businessName' OR configName='version'";
$resultVerPS = $conn->query($sqlVerPS);
if ($resultVerPS->num_rows <= 0) {
	header("Location:prepare-system.php");
}

$prefixCoookie = "trv";

/* Timezone - Modify accordingly */
date_default_timezone_set("America/Los_Angeles");

include_once "convertJson.php";
