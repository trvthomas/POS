<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";

$existeError = false;
$inventoryList = "";
$numberProducts = 0;

if (isset($_POST["getInventoryCategory"])) {
	if ($_POST["getInventoryCategory"] != 0) {
		$sql = "SELECT * FROM trvsol_products WHERE categoryID=" . $_POST["getInventoryCategory"] . " ORDER BY activo DESC, categoryID ASC, display_order ASC";
	} else {
		$sql = "SELECT * FROM trvsol_products ORDER BY activo DESC, categoryID ASC, display_order ASC";
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$bgColor = "has-background-white-ter";
			$imageProduct = "/trv/media/imagen-no-disponible.png";
			$colorValorInv = "var(--black-color)";

			if ($row["activo"] == 1) {
				$bgColor = "";
			}
			if ($row["imagen"] != "") {
				$imageProduct = $row["imagen"];
			}

			$finalPrice = '$' . number_format($row["precio"], 0, ",", ".");
			if ($row["variable_price"] == 1) {
				$finalPrice = 'Variable price';
			}

			$sql2 = "SELECT * FROM trvsol_categories WHERE id=" . $row["categoryID"];
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				$row2 = $result2->fetch_assoc();

				$inventoryList .= '<div class="list-item ' . $bgColor . '">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imageProduct . '" class= "is-rounded" style= "border: 2px solid ' . $row2["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">' . $finalPrice . '</span> <span class="tag is-rounded"><i class="fas fa-barcode"></i> ' . $row["barcode"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row2["color"] . '; color: ' . $row2["color_txt"] . ';">' . $row2["nombre"] . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Expected units</h4>
			<p class= "is-size-5 has-text-success"><b>' . $row["stock"] . '</b></p>
			</div>
		</div>
		
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Counted units</h4>
			
			<div class="field">
			<div class="control has-icons-left is-expanded">
			<input type= "number" class= "input" placeholder= "e.g. 1, 15" id= "inventoryProdValue' . $numberProducts . '" value= "' . $row["stock"] . '">
			<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
			</div>
			</div>
			<input class= "is-hidden" type= "text" id= "inventoryProdID' . $numberProducts . '" value= "' . $row["id"] . '" disabled>
			</div>
		</div>
		</div>
		</div>
	</div>';

				++$numberProducts;
			}
		}
	} else {
		$inventoryList = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'inventario' => $inventoryList,
	'numero_productos' => $numberProducts
);
echo json_encode(convertJson($varsSend));
?>