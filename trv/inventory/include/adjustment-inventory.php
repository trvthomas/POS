<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";

$existeError = false;
$productosRegistrados = false;
$prodsAgregados = 0;

if (isset($_POST["registerMovementNotes"]) && isset($_POST["registerMovementArray"])) {
	$arrayProducts = json_decode($_POST["registerMovementArray"], true);
	$newArrayUpdate = array();
	$newArraySimplify = array();

	if (count($arrayProducts) > 0) {
		//Register before & after stock
		for ($x2 = 0; $x2 < count($arrayProducts); ++$x2) {
			$sql4 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProducts[$x2]["prodID"];
			$result4 = $conn->query($sql4);
			if ($result4->num_rows > 0) {
				$row4 = $result4->fetch_assoc();

				$beforeStockUpd = $row4["stock"];
				++$beforeStockUpd;
				--$beforeStockUpd;

				$differenceQuantity = $arrayProducts[$x2]["inventory"] - $beforeStockUpd;

				if ($beforeStockUpd != $arrayProducts[$x2]["inventory"]) {
					$pushArray = array(
						'id' => $arrayProducts[$x2]["prodID"],
						'stock_before' => $beforeStockUpd,
						'stock_after' => $arrayProducts[$x2]["inventory"],
						'difference' => $differenceQuantity
					);
					$newArrayUpdate[] = $pushArray;

					array_push($newArraySimplify, $arrayProducts[$x2]["prodID"]);

					//Update stock
					$sql2 = "UPDATE trvsol_products SET stock='" . $arrayProducts[$x2]["inventory"] . "' WHERE id=" . $arrayProducts[$x2]["prodID"];
					$conn->query($sql2);

					++$prodsAgregados;
				}
			}
		}

		$sql3 = "INSERT INTO trvsol_inventory (date, hour, type, reason, notes, productsArray, productsArrayComplete, productsAdded)
	VALUES ('" . date("Y-m-d") . "', '" . date("H:i") . "', 'adjust', 'Inventory adjustment', '" . $_POST["registerMovementNotes"] . "', '" . json_encode($newArraySimplify) . "', '" . json_encode($newArrayUpdate)	. "', '" . $prodsAgregados . "');";
		$conn->query($sql3);

		$productosRegistrados = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'documento_registrado' => $productosRegistrados
);
echo json_encode(convertJson($varsSend));
?>