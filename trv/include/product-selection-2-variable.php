<?php include_once "DBData.php";

$listaPrecios = "";

if (isset($_POST["prodSelection2IdProd"])) {
	$stmt = $conn->prepare("SELECT id, nombre, precio, array_prices, stock FROM trvsol_products WHERE id= ? AND activo= 1 AND variable_price= 1");
	$stmt->bind_param("i", $_POST["prodSelection2IdProd"]);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["array_prices"], true);
		for ($x = 0; $x < count($decoded); ++$x) {
			$onclick = "variablePriceAdd(" . $row["id"] . ", '" . $row["nombre"] . "', " . $row["precio"] . ", " . $row["stock"] . ", " . $decoded[$x] . ")";
			$onclick2 = "variablePriceAddOther(" . $row["id"] . ", '" . $row["nombre"] . "', " . $row["precio"] . ", " . $row["stock"] . ")";

			$listaPrecios .= '<div class= "column is-one-quarter-tablet is-half-mobile">
		<div class= "box is-shadowless is-clickable pastel-bg-lightblue" onclick= "' . $onclick . '">
		<h2 class= "is-size-3">$' . number_format($decoded[$x], 0, ",", ".") . '</h2>
		</div>
	</div>';
		}

		$listaPrecios .= '<div class= "column is-half-tablet is-half-mobile">
		<div class= "box is-shadowless pastel-bg-orange">
		<h4 class= "is-size-4">Other value</h4>
		
		<div class="field has-addons">
		<div class="control has-icons-left is-expanded">
		<input type= "number" class= "input" placeholder= "e.g. 50, 300" id= "variablePriceOtherInput" step= "500">
		<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
		</div>
		
		<div class="control">
		<button class= "button backgroundDark" onclick= "' . $onclick2 . '"><i class= "fas fa-circle-check"></i></button>
		</div>
		</div>
		</div>
	</div>';
	} else {
		$listaPrecios .= '<div class= "column is-full has-text-centered">
	<h3 class= "is-size-5 has-text-grey-dark">There was an error</h3>
	</div>';
	}
}

$varsSend = array(
	'lista_pecios' => $listaPrecios
);
echo json_encode(convertJson($varsSend));
?>