<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$lastPage = false;
$listaProds = "";
$totalProds = 0;
$totalValueInv = 0;

if (isset($_POST["getProdsSearch"]) && isset($_POST["getProdsIdCategory"]) && isset($_POST["getProdsPage"])) {
	$sqlProds = "SELECT purchasePrice, stock FROM trvsol_products";
	$resultProds = $conn->query($sqlProds);
	if ($resultProds->num_rows > 0) {
		while ($rowProds = $resultProds->fetch_assoc()) {
			++$totalProds;
			$totalValueInv += $rowProds["purchasePrice"] * $rowProds["stock"];
		}
	}

	if ($totalValueInv <= 0) {
		$totalValueInv = '<span class= "has-text-danger">' . number_format($totalValueInv, 0, ",", ".") . '</span>';
	} else {
		$totalValueInv = number_format($totalValueInv, 0, ",", ".");
	}

	$limit1 = $_POST["getProdsPage"] * 10 * 2 - 20;

	if ($_POST["getProdsIdCategory"] != 0) {
		$sql = "SELECT * FROM trvsol_products WHERE (nombre LIKE '%" . $_POST["getProdsSearch"] . "%' OR barcode LIKE '%" . $_POST["getProdsSearch"] . "%' OR precio LIKE '%" . $_POST["getProdsSearch"] . "%') AND categoryID=" . $_POST["getProdsIdCategory"] . " ORDER BY activo DESC, categoryID ASC, display_order ASC LIMIT " . $limit1 . ", 20";
	} else {
		$sql = "SELECT * FROM trvsol_products WHERE (nombre LIKE '%" . $_POST["getProdsSearch"] . "%' OR barcode LIKE '%" . $_POST["getProdsSearch"] . "%' OR precio LIKE '%" . $_POST["getProdsSearch"] . "%') ORDER BY activo DESC, categoryID ASC, display_order ASC LIMIT " . $limit1 . ", 20";
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$onclick = "openLoader()";
			$imageProduct = "/trv/media/imagen-no-disponible.png";

			$bgColor = "has-background-white-ter";
			$hideShowBtn = '<button class="button is-warning is-light" onclick= "hideShowProduct(' . $row["id"] . ', 1)" id= "btnHideProduct' . $row["id"] . '" title= "Show"><i class="fas fa-eye"></i></button>';
			if ($row["activo"] == 1) {
				$hideShowBtn = '<button class="button is-warning is-light" onclick= "hideShowProduct(' . $row["id"] . ', 0)" id= "btnHideProduct' . $row["id"] . '" title= "Hide"><i class="fas fa-eye-slash"></i></button>';
				$bgColor = "";
			}
			$colorRent = "var(--black-color)";
			$colorUtil = "var(--black-color)";

			$valorRentabilidad = $row["purchasePrice"];
			if ($valorRentabilidad == 0) {
				$valorRentabilidad = $row["precio"];
			}

			$valorCant = 0;
			$rentabilidad = 0;
			if ($row["precio"] != 0) {
				$valorCant = $row["precio"] - $row["purchasePrice"];
				$rentabilidad = $row["precio"] / $valorRentabilidad * 100 - 100;
			}

			if ($valorCant <= 0) {
				$colorUtil = "#ef4d4d";
			}
			if ($rentabilidad <= 0) {
				$colorRent = "#ef4d4d";
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

				$listaProds .= '<div class="list-item ' . $bgColor . '">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imageProduct . '" class= "is-rounded" style= "border: 2px solid ' . $row2["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">' . $finalPrice . '</span> <span class="tag is-rounded"><i class="fas fa-barcode"></i> ' . $row["barcode"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row2["color"] . '; color: ' . $row2["color_txt"] . ';">' . $row2["nombre"] . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered mb-0">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Profitability</h4>
			<p class= "is-size-5" style= "color: ' . $colorRent . ';"><b>' . number_format(round($rentabilidad), 0, ",", ".") . '%</b></p>
			</div>
		</div>
		
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Profit</h4>
			<p class= "is-size-5" style= "color: ' . $colorUtil . ';"><b>$' . number_format($valorCant, 0, ",", ".") . '</b></p>
			</div>
		</div>
		</div>
		
		<div class= "buttons is-right">
		' . $hideShowBtn . '
		<a class="button is-light is-info" href="/trv/admin/edit-product.php?id=' . $row["id"] . '" title= "Edit"><i class="fas fa-edit"></i></a>
		<button class="button is-danger is-light" onclick= "deleteProduct(' . $row["id"] . ')" id= "btnDeleteProduct' . $row["id"] . '" title= "Delete"><i class="fas fa-trash-alt"></i></button>
		<a class="button" href="/trv/admin/statistics-products.php?idProd=' . $row["id"] . '" title= "View product statistics"><i class="fas fa-chart-bar"></i></a>
		</div>
		</div>
	</div>';
			}
		}
	} else {
		$listaProds = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";
		$lastPage = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'productos' => $listaProds,
	'numero_productos' => number_format($totalProds, 0, ",", "."),
	'numero_valor_inventario' => $totalValueInv,
	'ultima_pagina' => $lastPage
);
echo json_encode(convertJson($varsSend));
?>