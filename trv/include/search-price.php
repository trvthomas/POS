<?php include_once "DBData.php";

$existeError = false;
$resultados = "";

if (isset($_POST["searchPriceQuery"])) {
	$sql = "SELECT * FROM trvsol_products WHERE (nombre LIKE '%" . $_POST["searchPriceQuery"] . "%' OR barcode LIKE '%" . $_POST["searchPriceQuery"] . "%' OR tags LIKE '%" . $_POST["searchPriceQuery"] . "%') AND activo=1 ORDER BY categoryID ASC";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$sql2 = "SELECT * FROM trvsol_categories WHERE id=" . $row["categoryID"];
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				$row2 = $result2->fetch_assoc();

				$imagenProd = "/trv/media/imagen-no-disponible.png";
				if ($row["imagen"] != "") {
					$imagenProd = $row["imagen"];
				}

				$finalPrice = '$' . number_format($row["precio"], 0, ",", ".");
				if ($row["variable_price"] == 1) {
					$finalPrice = 'Precio variable';
				}

				$resultados .= '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imagenProd . '" class= "is-rounded" style= "border: 2px solid ' . $row2["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded"><i class= "fas fa-barcode"></i> ' . $row["barcode"] . '</span> <span class="tag is-rounded"><b>Stock: ' . number_format($row["stock"], 0, ",", ".") . '</b></span></div>
		<div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row2["color"] . '; color: ' . $row2["color_txt"] . ';">' . $row2["nombre"] . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Selling price</h4>
			<p class= "is-size-5 has-text-success"><b>' . $finalPrice . '</b></p>
			</div>
		</div>
		</div>
		</div>
	</div>';
			}
		}
	} else {
		$resultados = "<p class= 'has-text-centered is-size-5'><b>No results found</b></p>";
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'resultados' => $resultados
);
echo json_encode(convertJson($varsSend));
?>