<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$voucherEliminado = false;
$existeError = false;

if (isset($_POST["voucherDeleteId"])) {
	$sql = "DELETE FROM trvsol_vouchers WHERE id=" . $_POST["voucherDeleteId"];
	if ($conn->query($sql) === TRUE) {
		$sql2 = "DELETE FROM trvsol_vouchers_stats WHERE voucherId=" . $_POST["voucherDeleteId"];
		$conn->query($sql2);

		$voucherEliminado = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'voucher_eliminado' => $voucherEliminado
);
echo json_encode(convertJson($varsSend));
?>