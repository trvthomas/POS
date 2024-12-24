<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/media/uploads/excel-importer/SimpleXLSX.php";

use Shuchkin\SimpleXLSX;

$existeError = false;
$numeroProductosEditados = 0;
$numeroProductosTotales = 0;

if (isset($_POST["editProductsFileName"]) && isset($_POST["editProductsActionRepeated"])) {
	if ($xlsx = SimpleXLSX::parse($_POST["editProductsFileName"])) {
		$arrayRows = $xlsx->rows(1);

		for ($x = 1; $x < count($arrayRows); ++$x) {
			$uploadProduct = 1;

			$editionToDoIdProd = "";
			$editionToDoNameProd = "";
			$editionToDoPrice = "";
			$editionToDoPurchase = "";
			$editionToDoCategory = "";
			$editionToDoBarcode = "";

			for ($x2 = 0; $x2 < count($arrayRows[$x]); ++$x2) {
				if ($x2 == 0) {
					$editionToDoIdProd = $arrayRows[$x][$x2];
				} else if ($x2 == 5) {
					$sql6 = "SELECT barcode FROM trvsol_products WHERE barcode= '" . strtoupper($arrayRows[$x][$x2]) . "' AND NOT id=" . $editionToDoIdProd;
					$result6 = $conn->query($sql6);
					if ($result6->num_rows > 0) {
						if ($_POST["editProductsActionRepeated"] == 1) {
							$uploadProduct = 0;
						} else if ($_POST["editProductsActionRepeated"] == 2) {
							$editionToDoBarcode = strtoupper($arrayRows[$x][$x2]) . "-" . rand(100000, 999999) . "'";
						}
					} else {
						$editionToDoBarcode = strtoupper($arrayRows[$x][$x2]);
					}
				} else if ($x2 == 1) {
					$editionToDoNameProd = $arrayRows[$x][$x2];
				} else if ($x2 == 2) {
					$editionToDoPrice = $arrayRows[$x][$x2];
				} else if ($x2 == 3) {
					$editionToDoPurchase = $arrayRows[$x][$x2];
				} else if ($x2 == 4) {
					$sql2 = "SELECT * FROM trvsol_categories WHERE id=" . $arrayRows[$x][$x2];
					$result2 = $conn->query($sql2);

					if ($result2->num_rows <= 0) {
						$uploadProduct = 0;
					}
					$editionToDoCategory = $arrayRows[$x][$x2];
				}
			}

			if ($uploadProduct == 1) {
				$sql = "UPDATE trvsol_products SET nombre='" . $editionToDoNameProd . "', precio='" . $editionToDoPrice . "', purchasePrice='" . $editionToDoPurchase . "', categoryID='" . $editionToDoCategory . "', barcode='" . $editionToDoBarcode . "' WHERE id=" . $editionToDoIdProd;
				if ($conn->query($sql) === TRUE) {
					++$numeroProductosEditados;
				}
			}
			++$numeroProductosTotales;
		}

		unlink($_POST["editProductsFileName"]);
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'productos_editados' => $numeroProductosEditados,
	'productos_totales' => $numeroProductosTotales
);
echo json_encode(convertJson($varsSend));
?>