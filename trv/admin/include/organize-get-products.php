<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";

use Mexitek\PHPColors\Color;

$listaProds = "";
$arrayProds = array();

if (isset($_POST["getProdsIdCategory"])) {
	$stmt = $conn->prepare("SELECT trvsol_products.*, trvsol_categories.color AS category_color FROM trvsol_products INNER JOIN trvsol_categories ON trvsol_products.categoryID = trvsol_categories.id WHERE trvsol_products.categoryID = ? AND trvsol_products.activo= 1 ORDER BY display_order ASC");
	$stmt->bind_param("i", $_POST["getProdsIdCategory"]);

	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			array_push($arrayProds, $row["id"]);

			$secondColor = "#3e4095";
			$originalColor = new Color($row["category_color"]);
			if ($originalColor->isLight()) {
				$secondColor = $originalColor->darken(35);
			}

			$imgProd = "/trv/media/imagen-no-disponible.png";
			if ($row["imagen"] != "") {
				$imgProd = $row["imagen"];
			}

			$listaProds .= '<div class= "column is-one-quarter-tablet is-half-mobile">
		<div class= "box p-2 is-shadowless productButtonsNew" style= "--prod-border-color: ' . $row["category_color"] . '; --prod-text-color: #' . $secondColor . ';cursor: move;">
		<div class= "has-text-centered"><img src= "' . $imgProd . '" alt= ""></div>
		
		<h4 class= "is-size-5">' . $row["nombre"] . '</h4>
		<p><b>$' . number_format($row["precio"], 0, ",", ".") . '</b></p>
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
	'productos' => $listaProds,
	'array' => json_encode($arrayProds)
);
echo json_encode(convertJson($varsSend));
?>