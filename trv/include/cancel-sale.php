<?php include_once "DBData.php";

$existeError = false;
$ventaCancelada = false;

if (isset($_POST["cancelSaleIDInvoice"])) {
	$sql = "SELECT * FROM trvsol_invoices WHERE id=" . $_POST["cancelSaleIDInvoice"] . " AND cancelada=0";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$totalVenta = $row["subtotal"] - $row["descuentos"];

		//Set sale as canceled
		$sql2 = "UPDATE trvsol_invoices SET cancelada=1, canceladaPor='" . ucfirst($_COOKIE[$prefixCoookie . "UsernameUser"]) . "' WHERE id=" . $_POST["cancelSaleIDInvoice"];
		if ($conn->query($sql2) === TRUE) {
			//Update stats
			$sql3 = "SELECT * FROM trvsol_stats WHERE mes=" . $row["mes"] . " AND year=" . $row["year"];
			$result3 = $conn->query($sql3);

			if ($result3->num_rows > 0) {
				$row3 = $result3->fetch_assoc();

				$decoded = json_decode($row3["estadisticas"], true);

				for ($x = 0; $x < count($decoded); ++$x) {
					if (date('Y-m-d', strtotime($decoded[$x]["date"])) == date('Y-m-d', strtotime($row["fecha"]))) {
						$decoded[$x]["numberSales"] -= 1;

						if ($row["formaPago"] == "Cash") {
							$decoded[$x]["cashSales"] -= $totalVenta;
						} else if ($row["formaPago"] == "Card") {
							$decoded[$x]["cardSales"] -= $totalVenta;
						} else if ($row["formaPago"] == "Multipayment") {
							$decoded[$x]["cashSales"] -= $row["multipagoEfectivo"];
							$decoded[$x]["cardSales"] -= $row["multipagoTarjeta"];
							$decoded[$x]["otherSales"] -= $row["multipagoOtro"];
						} else {
							$decoded[$x]["otherSales"] -= $totalVenta;
						}

						$sql4 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . $row["mes"] . " AND year=" . $row["year"];
						$conn->query($sql4);
					}
				}
			}

			$arrayProducts = json_decode($row["productosArray"], true);
			$newArrayUpdate = array();
			$prodsAgregados = 0;

			//INVENTORY - Get stock before
			$arrayProdsUnique = array_values(array_unique($arrayProducts));
			for ($x5 = 0; $x5 < count($arrayProdsUnique); ++$x5) {
				$sql11 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x5];
				$result11 = $conn->query($sql11);
				if ($result11->num_rows > 0) {
					$row11 = $result11->fetch_assoc();

					$beforeStockUpd = $row11["stock"];
					++$beforeStockUpd;
					--$beforeStockUpd;

					$pushArray = array(
						'id' => $arrayProdsUnique[$x5],
						'stock_before' => $beforeStockUpd,
						'stock_after' => 0,
						'difference' => 0
					);
					$newArrayUpdate[] = $pushArray;
				}
			}

			//Update stock
			for ($x2 = 0; $x2 < count($arrayProducts); ++$x2) {
				$sql5 = "SELECT * FROM trvsol_products WHERE id=" . $arrayProducts[$x2];
				$result5 = $conn->query($sql5);

				if ($result5->num_rows > 0) {
					$row5 = $result5->fetch_assoc();

					$stockProducto = $row5["stock"] + 1;

					$sql6 = "UPDATE trvsol_products SET stock='" . $stockProducto . "' WHERE id=" . $arrayProducts[$x2];
					$conn->query($sql6);

					//Update product stats
					$sql7 = "SELECT * FROM trvsol_products_stats WHERE year=" . $row["year"] . " AND productId=" . $arrayProducts[$x2];
					$result7 = $conn->query($sql7);

					++$prodsAgregados;

					if ($result7->num_rows > 0) {
						$row7 = $result7->fetch_assoc();

						$decoded2 = json_decode($row7["estadisticas"], true);

						for ($x3 = 0; $x3 < count($decoded2); ++$x3) {
							if ($decoded2[$x3]["month"] == $row["mes"] && $decoded2[$x3]["date"] == $row["fecha"]) {
								$decoded2[$x3]["quantitiesSold"] -= 1;

								$sql8 = "UPDATE trvsol_products_stats SET estadisticas='" . json_encode($decoded2) . "' WHERE year=" . $row["year"] . " AND productId=" . $arrayProducts[$x2];
								$conn->query($sql8);
							}
						}
					}


					if ($row["mes"] == date("m") && $row["year"] == date("Y")) {
						$newMonthlySales = $row5["ventasMensuales"] - 1;
						$sql14 = "UPDATE trvsol_products SET ventasMensuales=" . $newMonthlySales . " WHERE id=" . $row5["id"];
						$conn->query($sql14);
					}
				}
			}

			//INVENTORY - Get stock after
			for ($x6 = 0; $x6 < count($arrayProdsUnique); ++$x6) {
				$sql12 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x6];
				$result12 = $conn->query($sql12);
				if ($result12->num_rows > 0) {
					$row12 = $result12->fetch_assoc();

					$afterStockUpd = $row12["stock"];
					++$afterStockUpd;
					--$afterStockUpd;

					$differenceStock = $afterStockUpd - $newArrayUpdate[$x6]["stock_before"];

					$newArrayUpdate[$x6]["stock_after"] = $afterStockUpd;
					$newArrayUpdate[$x6]["difference"] = $differenceStock;
				}
			}

			$sql13 = "INSERT INTO trvsol_inventory (date, hour, type, reason, notes, productsArray, productsArrayComplete, productsAdded)
	VALUES ('" . date("Y-m-d") . "', '" . date("H:i") . "', 'saleCancel', 'Cancelación de venta', 'Venta #" . $row["numero"] . "', '" . json_encode($arrayProducts) . "', '" . json_encode($newArrayUpdate)	. "', '" . $prodsAgregados . "');";
			$conn->query($sql13);

			//Update user stats
			$sql9 = "SELECT * FROM trvsol_users_stats WHERE year=" . $row["year"] . " AND userId=" . $row["idSeller"];
			$result9 = $conn->query($sql9);

			if ($result9->num_rows > 0) {
				$row9 = $result9->fetch_assoc();

				$decoded3 = json_decode($row9["estadisticas"], true);

				for ($x4 = 0; $x4 < count($decoded3); ++$x4) {
					if ($decoded3[$x4]["month"] == $row["mes"] && $decoded3[$x4]["date"] == $row["fecha"]) {
						if ($row["formaPago"] == "Cash") {
							$decoded3[$x4]["cashSales"] -= $totalVenta;
						} else if ($row["formaPago"] == "Card") {
							$decoded3[$x4]["cardSales"] -= $totalVenta;
						} else if ($row["formaPago"] == "Multipayment") {
							$decoded3[$x4]["cashSales"] -= $row["multipagoEfectivo"];
							$decoded3[$x4]["cardSales"] -= $row["multipagoTarjeta"];
						} else {
							$decoded3[$x4]["otherSales"] -= $totalVenta;
						}

						$sql10 = "UPDATE trvsol_users_stats SET estadisticas='" . json_encode($decoded3) . "' WHERE year=" . $row["year"] . " AND userId=" . $row["idSeller"];
						$conn->query($sql10);
					}
				}
			}

			$ventaCancelada = true;
		} else {
			$existeError = true;
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'venta_cancelada' => $ventaCancelada
);
echo json_encode(convertJson($varsSend));
?>