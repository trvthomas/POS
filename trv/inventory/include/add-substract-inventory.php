<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";

$existeError = false;
$productosRegistrados = false;
$prodsAgregados = 0;

if (isset($_POST["registerEntryExitArray"]) && isset($_POST["registerEntryExitArrayComplete"]) && isset($_POST["registerEntryExitType"]) && isset($_POST["registerEntryExitReason"]) && isset($_POST["registerEntryExitNotes"])) {
	$arrayProducts = json_decode($_POST["registerEntryExitArray"], true);
	$arrayProdsUnique = array_values(array_unique($arrayProducts));
	$newArrayUpdate = array();

	if (count($arrayProducts) > 0) {
		//Registrar before stock
		for ($x2 = 0; $x2 < count($arrayProdsUnique); ++$x2) {
			$sql4 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x2];
			$result4 = $conn->query($sql4);
			if ($result4->num_rows > 0) {
				$row4 = $result4->fetch_assoc();

				$differenceQuantity = 0;

				$arrayCompleteTemp = json_decode($_POST["registerEntryExitArrayComplete"], true);
				for ($x3 = 0; $x3 < count($arrayCompleteTemp); ++$x3) {
					if ($arrayCompleteTemp[$x3]["id"] == $arrayProdsUnique[$x2]) {
						if ($_POST["registerEntryExitType"] == "entry") {
							$differenceQuantity = $arrayCompleteTemp[$x3]["quantity"];
						} else if ($_POST["registerEntryExitType"] == "exit") {
							$differenceQuantity = "-" . $arrayCompleteTemp[$x3]["quantity"];
						}
					}
				}

				$beforeStockUpd = $row4["stock"];
				++$beforeStockUpd;
				--$beforeStockUpd;

				$pushArray = array(
					'id' => $arrayProdsUnique[$x2],
					'stock_before' => $beforeStockUpd,
					'stock_after' => 0,
					'difference' => $differenceQuantity
				);
				$newArrayUpdate[] = $pushArray;
			}
		}

		//Update stock
		for ($x = 0; $x < count($arrayProducts); ++$x) {
			$sql = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProducts[$x];
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();

				$stockFinal = $row["stock"];

				if ($_POST["registerEntryExitType"] == "entry") {
					++$stockFinal;
				} else if ($_POST["registerEntryExitType"] == "exit") {
					--$stockFinal;
				}

				$sql2 = "UPDATE trvsol_products SET stock='" . $stockFinal . "' WHERE id=" . $arrayProducts[$x];
				$conn->query($sql2);

				++$prodsAgregados;
			}
		}

		//Registrar after stock
		for ($x4 = 0; $x4 < count($arrayProdsUnique); ++$x4) {
			$sql5 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x4];
			$result5 = $conn->query($sql5);
			if ($result5->num_rows > 0) {
				$row5 = $result5->fetch_assoc();
				$afterStockUpd = $row5["stock"];
				++$afterStockUpd;
				--$afterStockUpd;

				$newArrayUpdate[$x4]["stock_after"] = $afterStockUpd;
			}
		}

		$sql3 = "INSERT INTO trvsol_inventory (date, hour, type, reason, notes, productsArray, productsArrayComplete, productsAdded)
	VALUES ('" . date("Y-m-d") . "', '" . date("H:i") . "', '" . $_POST["registerEntryExitType"] . "', '" . $_POST["registerEntryExitReason"] . "', '" . $_POST["registerEntryExitNotes"] . "', '" . $_POST["registerEntryExitArray"] . "', '" . json_encode($newArrayUpdate)	. "', '" . $prodsAgregados . "');";
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