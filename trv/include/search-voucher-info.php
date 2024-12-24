<?php include_once "DBData.php";

$existeError = false;
$noCumpleCondiciones = false;
$voucherDescuento = 0;
$voucherID = 0;

if (isset($_POST["searchVoucherCode"]) && isset($_POST["searchVoucherPaymentMethod"]) && isset($_POST["searchVoucherSubtotal"])) {
	$sql = "SELECT * FROM trvsol_vouchers WHERE code= '" . strtoupper($_POST["searchVoucherCode"]) . "' AND paymentMethods LIKE '%a:" . $_POST["searchVoucherPaymentMethod"] . ":p%' AND totalAvailable > 0 AND minimumQuantity <= " . $_POST["searchVoucherSubtotal"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$voucherDescuento = $_POST["searchVoucherSubtotal"] * ($row["value"] / 100);
		$voucherID = $row["id"];
	} else {
		$noCumpleCondiciones = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'no_cumple_condiciones' => $noCumpleCondiciones,
	'valor_descuentos' => $voucherDescuento,
	'id_bono' => $voucherID
);
echo json_encode(convertJson($varsSend));
?>