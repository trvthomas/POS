<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$codigoExiste = false;
$voucherEditado = false;

if (isset($_POST["editVoucherCode"]) && isset($_POST["editVoucherAvailable"]) && isset($_POST["editVoucherMinimum"]) && isset($_POST["editVoucherValue"]) && isset($_POST["editVoucherExpiration"]) && isset($_POST["editVoucherPayment"]) && isset($_POST["editVoucherID"])) {
	$sql2 = "SELECT * FROM trvsol_vouchers WHERE code= '" . strtoupper($_POST["editVoucherCode"]) . "' AND NOT id=" . $_POST["editVoucherID"];
	$result2 = $conn->query($sql2);
	if ($result2->num_rows > 0) {
		$codigoExiste = true;
	} else {
		$sql = "UPDATE trvsol_vouchers SET code='" . strtoupper($_POST["editVoucherCode"]) . "', totalAvailable='" . $_POST["editVoucherAvailable"] . "', minimumQuantity='" . $_POST["editVoucherMinimum"] . "', value='" . $_POST["editVoucherValue"] . "', paymentMethods='" . $_POST["editVoucherPayment"] . "', expiration='" . $_POST["editVoucherExpiration"] . "' WHERE id=" . $_POST["editVoucherID"];
		if ($conn->query($sql) === TRUE) {
			$voucherEditado = true;
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'codigo_existe' => $codigoExiste,
	'voucher_editado' => $voucherEditado
);
echo json_encode(convertJson($varsSend));
?>