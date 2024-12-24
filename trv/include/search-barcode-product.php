<?php include_once "DBData.php";

$existeError = false;
$codigoExiste = false;
$codigoId = false;
$codigoNombre = false;
$codigoPrecio = false;
$codigoStock = false;

if (isset($_POST["searchBarcodeQuery"])) {
	$ucwordsString = ucwords($_POST["searchBarcodeQuery"]);

	$stmt = $conn->prepare("SELECT * FROM trvsol_products WHERE barcode= ?");
	$stmt->bind_param("s", $ucwordsString);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$codigoExiste = true;
		$codigoId = $row["id"];
		$codigoNombre = $row["nombre"];
		$codigoPrecio = $row["precio"];
		$codigoStock = $row["stock"];
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'codigo_existe' => $codigoExiste,
	'codigo_id' => $codigoId,
	'codigo_nombre' => $codigoNombre,
	'codigo_precio' => $codigoPrecio,
	'codigo_stock' => $codigoStock
);
echo json_encode(convertJson($varsSend));
?>