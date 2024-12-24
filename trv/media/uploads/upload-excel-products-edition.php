<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/media/uploads/excel-importer/SimpleXLSX.php";

use Shuchkin\SimpleXLSX;

$errorArchivo = false;
$urlExcel = "";
$productsFound = 0;
$productsList = "";

if (isset($_FILES["excelFile"])) {
	$fileName = basename($_FILES["excelFile"]["name"]);
	$find = array(" ", "?", "¿", "!",  "¡",  "/", "á", "é", "í", "ó", "ú", "ñ");
	$target_file = date("Ymd-His") . str_replace($find, "", $fileName);
	$uploadOk = 1;
	$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	if (file_exists($target_file)) {
		$uploadOk = 0;
	}
	if ($_FILES["excelFile"]["size"] > 10000000) {
		$uploadOk = 0;
	}
	if ($fileType != "xlsx") {
		$uploadOk = 0;
	}

	if ($uploadOk != 0) {
		if (move_uploaded_file($_FILES["excelFile"]["tmp_name"], $target_file)) {
			$urlExcel = $target_file;

			if ($xlsx = SimpleXLSX::parse($target_file)) {
				$arrayRows = $xlsx->rows(1);
				$productsList .= '<table class="table is-striped is-fullwidth">
	<thead>
	<tr>
		<th>Actual information</th>
		<th>Edition to be made</th>
	</tr>
	</thead>
	<tbody>';

				for ($x = 1; $x < count($arrayRows); ++$x) {
					$productsList .= "<tr>";

					$editionToDoNameProd = "";
					$editionToDoPrice = "";
					$editionToDoPurchase = "";
					$editionToDoCategory = "'>";
					$editionToDoBarcode = "";

					for ($x2 = 0; $x2 < count($arrayRows[$x]); ++$x2) {
						if ($x2 == 0) {
							$sql = "SELECT * FROM trvsol_products WHERE id=" . $arrayRows[$x][$x2];
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								$row = $result->fetch_assoc();
								$categoryProdActual = "color: #EF4D4D;'>This category does not exist, the product will not be imported";

								$sql2 = "SELECT * FROM trvsol_categories WHERE id=" . $row["categoryID"];
								$result2 = $conn->query($sql2);
								if ($result2->num_rows > 0) {
									$row2 = $result2->fetch_assoc();
									$categoriaProdActual = "'>" . $row2["nombre"];
								}

								$productsList .= "<td>" . $row["nombre"] . "<br>$" . number_format($row["precio"], 0, ",", ".") . "<br>" . $row["barcode"] . "<br><br><b>Purchase price:</b> $" . $row["purchasePrice"] . "<br><b>Category:</b> <span style= '" . $categoriaProdActual . "</span></td>";
							}
						} else if ($x2 == 1) {
							$editionToDoNameProd = $arrayRows[$x][$x2];
						} else if ($x2 == 2) {
							$editionToDoPrice = number_format($arrayRows[$x][$x2], 0, ",", ".");
						} else if ($x2 == 3) {
							$editionToDoPurchase = number_format($arrayRows[$x][$x2], 0, ",", ".");
						} else if ($x2 == 4) {
							$sql = "SELECT * FROM trvsol_categories WHERE id=" . $arrayRows[$x][$x2];
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								$row = $result->fetch_assoc();
								$editionToDoCategory = "'>" . $row["nombre"];
							} else {
								$editionToDoCategory = "color: #EF4D4D;'>This category does not exist, the product will not be imported";
							}
						} else if ($x2 == 5) {
							$editionToDoBarcode = strtoupper($arrayRows[$x][$x2]);
						}
					}

					$productsList .= "<td>" . $editionToDoNameProd . "<br>$" . $editionToDoPrice . "<br>" . $editionToDoBarcode . "<br><br><b>Purchase price:</b> $" . $editionToDoPurchase . "<br><b>Category:</b> <span style= '" . $editionToDoCategory . "</span></td></tr>";
					++$productsFound;
				}

				$productsList .= "</tbody></table>";

				if ($productsFound <= 0) {
					unlink($target_file);
				}
			} else {
				$errorArchivo = true;
			}
		} else {
			$errorArchivo = true;
		}
	} else {
		$errorArchivo = true;
	}
} else {
	$errorArchivo = true;
}

$varsSend = array(
	'error_archivo' => $errorArchivo,
	'products_found' => $productsFound,
	'url_excel' => $urlExcel,
	'products_list' => $productsList
);
echo json_encode(convertJson($varsSend));
?>