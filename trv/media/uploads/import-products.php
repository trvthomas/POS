<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/media/uploads/excel-importer/SimpleXLSX.php";

use Shuchkin\SimpleXLSX;

$existeError = false;
$numeroProductosImportados = 0;
$numeroProductosTotales = 0;

if (isset($_POST["importProductsFileName"]) && isset($_POST["importProductsActionRepeated"])) {
	if ($xlsx = SimpleXLSX::parse($_POST["importProductsFileName"])) {
		$arrayRows = $xlsx->rows(1);

		for ($x = 1; $x < count($arrayRows); ++$x) {
			$insertSql = "";
			$uploadProduct = 1;

			for ($x2 = 0; $x2 < count($arrayRows[$x]); ++$x2) {
				$countRows = count($arrayRows[$x]) - 1;
				if ($x2 == $countRows) {
					$sql6 = "SELECT barcode FROM trvsol_products WHERE barcode= '" . strtoupper($arrayRows[$x][$x2]) . "'";
					$result6 = $conn->query($sql6);
					if ($result6->num_rows > 0) {
						if ($_POST["importProductsActionRepeated"] == 1) {
							$uploadProduct = 0;
						} else if ($_POST["importProductsActionRepeated"] == 2) {
							$insertSql .= "'" . strtoupper($arrayRows[$x][$x2]) . "-" . rand(100000, 999999) . "'";
						}
					} else {
						$insertSql .= "'" . strtoupper($arrayRows[$x][$x2]) . "'";
					}
				} else {
					if ($x2 == 3) {
						$sql2 = "SELECT * FROM trvsol_categories WHERE id=" . $arrayRows[$x][$x2];
						$result2 = $conn->query($sql2);

						if ($result2->num_rows <= 0) {
							$uploadProduct = 0;
						}
					}
					$insertSql .= "'" . $arrayRows[$x][$x2] . "', ";
				}
			}

			if ($uploadProduct == 1) {
				$sql = "INSERT INTO trvsol_products (nombre, precio, purchasePrice, categoryID, barcode, stock, activo, ventasMensuales)
	VALUES (" . $insertSql . ", 0, 1, 0)";
				if ($conn->query($sql) === TRUE) {
					++$numeroProductosImportados;
				}
			}
			++$numeroProductosTotales;
		}

		unlink($_POST["importProductsFileName"]);

		//Actualice estadísticas
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
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'productos_importados' => $numeroProductosImportados,
	'productos_totales' => $numeroProductosTotales
);
echo json_encode(convertJson($varsSend));
?>