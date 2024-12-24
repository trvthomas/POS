<?php include_once "DBData.php";

$existeError = false;
$movimientoRegistrado = false;

if (isset($_POST["newMovementPayment"]) && isset($_POST["newMovementType"]) && isset($_POST["newMovementValue"]) && isset($_POST["newMovementDescription"]) && isset($_POST["newMovementOtherName"])) {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$typeMovement = "Inflow";
		$paymentMovement = "Cash";
		$arrayMovement = "cashSales";
		if ($_POST["newMovementType"] == "EXIT") {
			$typeMovement = "Outflow";
		}
		if ($_POST["newMovementPayment"] == "T") {
			$paymentMovement = "Card";
			$arrayMovement = "cardSales";
		} else if ($_POST["newMovementPayment"] == "O") {
			$paymentMovement = $_POST["newMovementOtherName"];
			$arrayMovement = "otherSales";
		}

		$decoded = json_decode($row["estadisticas"], true);
		$decoded[date("d") - 1]["reports"] .= $typeMovement . ": <b>" . $paymentMovement . " - $" . number_format($_POST["newMovementValue"], 0, ",", ".") . " - " . $_POST["newMovementDescription"] . "</b><br>";

		if ($_POST["newMovementType"] == "EXIT") {
			$decoded[date("d") - 1][$arrayMovement] -= $_POST["newMovementValue"];
		} else {
			$decoded[date("d") - 1][$arrayMovement] += $_POST["newMovementValue"];
		}

		$sql2 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . date("m") . " AND year=" . date("Y");
		$conn->query($sql2);

		$movimientoRegistrado = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'movimiento_registrado' => $movimientoRegistrado
);
echo json_encode(convertJson($varsSend));
?>