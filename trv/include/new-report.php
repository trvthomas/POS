<?php include_once "DBData.php";

$existeError = false;
$reporteAgregado = false;

if (isset($_POST["newReportText"])) {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$find =    array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", '"');
		$replace = array("&aacute", "&eacute", "&iacute", "&oacute", "&uacute", "&Aacute", "&Eacute", "&Iacute", "&Oacute", "&Uacute", "&ntilde", "&Ntilde", "\"");
		$finalReport = str_replace($find, $replace, $_POST["newReportText"]);

		$decoded = json_decode($row["estadisticas"], true);
		$decoded[date("d") - 1]["reports"] .= $finalReport . "<br>";

		$sql2 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . date("m") . " AND year=" . date("Y");
		$conn->query($sql2);

		$reporteAgregado = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'reporte_creado' => $reporteAgregado
);
echo json_encode(convertJson($varsSend));
?>