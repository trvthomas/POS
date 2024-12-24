<?php include_once "DBData.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";

use Mexitek\PHPColors\Color;

$listaProds = "";

if (isset($_POST["prodSelection1Category"]) && isset($_POST["prodSelection1Search"])) {
	$searchLike = "%{$_POST["prodSelection1Search"]}%";
	$stmt;

	if ($_POST["prodSelection1Category"] != 0 && $_POST["prodSelection1Category"] != "") {
		//Category search
		$stmt = $conn->prepare("SELECT trvsol_products.*, trvsol_categories.color AS category_color FROM trvsol_products INNER JOIN trvsol_categories ON trvsol_products.categoryID = trvsol_categories.id WHERE trvsol_products.categoryID = ? AND trvsol_products.activo= 1 ORDER BY display_order ASC");
		$stmt->bind_param("i", $_POST["prodSelection1Category"]);
	} else if ($_POST["prodSelection1Search"] != "") {
		//Text search
		$stmt = $conn->prepare("SELECT trvsol_products.*, trvsol_categories.color AS category_color FROM trvsol_products INNER JOIN trvsol_categories ON trvsol_products.categoryID = trvsol_categories.id WHERE trvsol_products.activo= 1 AND (trvsol_products.nombre LIKE ? OR trvsol_products.precio LIKE ? OR trvsol_products.barcode LIKE ? OR trvsol_products.tags LIKE ?) ORDER BY categoryID, display_order ASC");
		$stmt->bind_param("ssss", $searchLike, $searchLike, $searchLike, $searchLike);
	}

	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$secondColor = "#3e4095";
			$originalColor = new Color($row["category_color"]);
			if ($originalColor->isLight()) {
				$secondColor = $originalColor->darken(35);
			}

			$onclick = "addProduct(" . $row["id"] . ", '" . $row["nombre"] . "', " . $row["precio"] . ", " . $row["stock"] . ", " . $row["variable_price"] . ")";

			$imgProd = "/trv/media/imagen-no-disponible.png";
			if ($row["imagen"] != "") {
				$imgProd = $row["imagen"];
			}

			$finalPrice = '$' . number_format($row["precio"], 0, ",", ".");
			if ($row["variable_price"] == 1) {
				$finalPrice = 'Select price';
			}

			$listaProds .= '<div class= "column is-one-quarter-tablet is-half-mobile">
		<div class= "box p-2 is-shadowless is-clickable productButtonsNew" onclick= "' . $onclick . '" style= "--prod-border-color: ' . $row["category_color"] . '; --prod-text-color: #' . $secondColor . ';">
		<div class= "has-text-centered"><img src= "' . $imgProd . '" alt= ""></div>
		
		<h4 class= "is-size-5">' . $row["nombre"] . '</h4>
		<p><b>' . $finalPrice . '</b></p>
		</div>
	</div>';
		}
	} else {
		$listaProds .= '<div class= "column is-full has-text-centered">
	<h3 class= "is-size-5 has-text-grey-dark">No results found</h3>
	</div>';
	}
}

$varsSend = array(
	'lista_prods' => $listaProds
);
echo json_encode(convertJson($varsSend));
?>