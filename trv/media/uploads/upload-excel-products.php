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
		<th>Product Name</th>
		<th>Selling Price (taxes included)</th>
		<th>Purchase Price</th>
		<th>Category</th>
		<th>Unique Code</th>
	</tr>
	</thead>
	<tbody>';

				for ($x = 1; $x < count($arrayRows); ++$x) {
					$productsList .= "<tr>";
					for ($x2 = 0; $x2 < count($arrayRows[$x]); ++$x2) {
						if ($x2 == 1 || $x2 == 2) {
							$productsList .= "<td>$" . number_format($arrayRows[$x][$x2], 0, ",", ".") . "</td>";
						} else if ($x2 == 3) {
							$sql = "SELECT * FROM trvsol_categories WHERE id=" . $arrayRows[$x][$x2];
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								$row = $result->fetch_assoc();
								$productsList .= "<td>" . $row["nombre"] . "</td>";
							} else {
								$productsList .= "<td style= 'border: 1px solid var(--normal-color);text-align: left;padding: 8px;color: #EF4D4D;'>This category does not exist, the product will not be imported</td>";
							}
						} else if ($x2 == 4) {
							$productsList .= "<td>" . strtoupper($arrayRows[$x][$x2]) . "</td>";
						} else {
							$productsList .= "<td>" . $arrayRows[$x][$x2] . "</td>";
						}
					}

					$productsList .= "</tr>";
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