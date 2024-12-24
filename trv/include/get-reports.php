<?php include_once "DBData.php";

$existeError = false;
$reportes = false;

if (isset($_POST["getReportsID"]) && $_POST["getReportsID"] == "seller189") {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);
		$reportes = $decoded[date("d") - 1]["reports"];
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'reportes' => $reportes
);
echo json_encode(convertJson($varsSend));
?>