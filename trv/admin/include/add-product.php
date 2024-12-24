<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$codigoExiste = false;
$productoAgregado = false;

if (isset($_POST["addProdName"]) && isset($_POST["addProdImage"]) && isset($_POST["addProdPrice"]) && isset($_POST["addProdBarcode"]) && isset($_POST["addProdCategory"]) && isset($_POST["addProdPurchasePrice"]) && isset($_POST["addProdIsVariable"]) && isset($_POST["addProdArrayPrices"])) {
	$strtotupperString = strtoupper($_POST["addProdBarcode"]);

	$stmt2 = $conn->prepare("SELECT barcode FROM trvsol_products WHERE barcode= ?");
	$stmt2->bind_param("s", $strtotupperString);
	$stmt2->execute();
	$result2 = $stmt2->get_result();

	if ($result2->num_rows > 0) {
		$codigoExiste = true;
	} else {
		$prodTags = "";

		$wordsProd = explode(" ", strtoupper($_POST["addProdName"]));
		for ($x = 0; $x < count($wordsProd); ++$x) {
			$prodTags .= $wordsProd[$x] . ", ";
		}

		$stmt = $conn->prepare("INSERT INTO trvsol_products (nombre, precio, variable_price, array_prices, imagen, barcode, categoryID, purchasePrice, tags, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
		$stmt->bind_param("siisssiis", $_POST["addProdName"], $_POST["addProdPrice"], $_POST["addProdIsVariable"], $_POST["addProdArrayPrices"], $_POST["addProdImage"], $strtotupperString, $_POST["addProdCategory"], $_POST["addProdPurchasePrice"], $prodTags);
		if ($stmt->execute()) {
			$productoAgregado = true;

			//Update stats
			$sql3 = "SELECT * FROM trvsol_products";
			$result3 = $conn->query($sql3);
			if ($result3->num_rows > 0) {
				while ($row3 = $result3->fetch_assoc()) {
					$arrayProducts = array();

					for ($x = 1; $x <= 12; ++$x) {
						$days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
						for ($x2 = 1; $x2 <= $days; ++$x2) {
							$pushArray2 = array(
								'month' => $x,
								'date' => date("Y") . '-' . $x . '-' . $x2,
								'quantitiesSold' => 0
							);
							$arrayProducts[] = $pushArray2;
						}
					}

					$sql4 = "SELECT * FROM trvsol_products_stats WHERE year=" . date("Y") . " AND productId=" . $row3["id"];
					$result4 = $conn->query($sql4);
					if ($result4->num_rows == 0) {
						$sql5 = "INSERT INTO trvsol_products_stats (year, productId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row3["id"] . "', '" . json_encode($arrayProducts) . "')";
						$conn->query($sql5);
					}
				}
			}
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'codigo_existe' => $codigoExiste,
	'producto_creado' => $productoAgregado
);
echo json_encode(convertJson($varsSend));
?>