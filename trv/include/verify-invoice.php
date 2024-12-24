<?php include_once "DBData.php";

$existeError = false;
$saleNotExists = false;
$idSale = 0;
$numberSale = 0;

if (isset($_POST["verifyInvoiceSaleNumber"])) {
	$stmt = $conn->prepare("SELECT id FROM trvsol_invoices WHERE numero= ?");
	$stmt->bind_param("s", $_POST["verifyInvoiceSaleNumber"]);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$idSale = $row["id"];
		$numberSale = $_POST["verifyInvoiceSaleNumber"];
	} else {
		$saleNotExists = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'comprobante_no_existe' => $saleNotExists,
	'id_venta' => $idSale,
	'numero_venta' => $numberSale
);
echo json_encode(convertJson($varsSend));
?>