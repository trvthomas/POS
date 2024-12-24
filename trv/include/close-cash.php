<?php include_once "DBData.php";

$existeError = false;
$cajaCerrada = false;
$backupCreado = false;

include_once "backup.php";

$verifKey = $_COOKIE[$prefixCoookie . "DateEnter"] . "T24498";

if (isset($_POST["closeCashPass"]) && $_POST["closeCashPass"] == $verifKey) {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);
		$decoded[$_COOKIE[$prefixCoookie . "DateDay"] - 1]["closedDate"] .= " " . date("d-m-Y h:i a");

		$sql2 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . date("m") . " AND year=" . date("Y");
		$conn->query($sql2);

		$cajaCerrada = $_COOKIE[$prefixCoookie . "DateEnter"];

		setcookie($prefixCoookie . "IdUser", "", time() - 3600, "/");
		setcookie($prefixCoookie . "UsernameUser", "", time() - 3600, "/");
		setcookie($prefixCoookie . "DateEnter", "", time() - 3600, "/");
		setcookie($prefixCoookie . "DateDay", "", time() - 3600, "/");

		setcookie($prefixCoookie . "TemporaryIdUser", "", time() - 3600, "/");
		setcookie($prefixCoookie . "TemporaryUsernameUser", "", time() - 3600, "/");
		setcookie($prefixCoookie . "TemporaryInventoryIdUser", "", time() - 3600, "/");
		setcookie($prefixCoookie . "TemporaryInventoryUsernameUser", "", time() - 3600, "/");

		if (date("N") == 2 || date("N") == 5) {
			generateBackup();
		}
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'caja_cerrada' => $cajaCerrada
);
echo json_encode(convertJson($varsSend));
?>