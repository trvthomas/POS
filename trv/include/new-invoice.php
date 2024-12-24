<?php include_once "DBData.php";
include_once "autoPrinting.php";

$existeError = false;
$saleCreated = false;
$idSale = 0;
$printingTemplate = "";
$autoPrinting = false;

if (isset($_POST["createSaleProducts"]) && isset($_POST["createSaleProductsArray"]) && isset($_POST["createSaleProductsArrayAuto"]) && isset($_POST["createSalePayment"]) && isset($_POST["createSaleSubtotal"]) && isset($_POST["createSaleDiscounts"]) && isset($_POST["createSaleReceived"]) && isset($_POST["createSaleChange"]) && isset($_POST["createSaleNotes"]) && isset($_POST["createSaleMultiEf"]) && isset($_POST["createSaleMultiTa"]) && isset($_POST["createSaleMultiOt"]) && isset($_POST["createSaleVoucherID"]) && isset($_POST["createSaleProductsChangeTicket"]) && isset($_POST["createSaleChangeTicketsNum"]) && isset($_POST["createSaleAutoNoPrint"])) {
	$newArrayUpdate = array();
	$prodsAgregados = 0;

	//Config & receipt num
	$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'numInvoice'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		//Prefix
		$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'prefixNumInvoice'";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_assoc();

			$payment = "Cash";
			$received = $_POST["createSaleReceived"];
			if ($_POST["createSalePayment"] != "M") {
				$received = number_format($_POST["createSaleReceived"], 0, ",", ".");
			}

			if ($_POST["createSalePayment"] == "T") {
				$payment = "Card";
			} else if ($_POST["createSalePayment"] == "M") {
				$payment = "Multipayment";
			} else if ($_POST["createSalePayment"] == "O") {
				$sql20 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
				$result20 = $conn->query($sql20);
				if ($result20->num_rows > 0) {
					while ($row20 = $result20->fetch_assoc()) {
						$payment = $row20["value"];
					}
				}
			}

			//Add sale to DB
			$completeNum = $row2["value"] . $row["value"];
			$date1 = date("m");
			$date2 = date("Y");
			$date3 = date("Y-m-d");
			$date4 = date("d-m-Y h:i a");
			$ucfirstString = ucfirst($_COOKIE[$prefixCoookie . "UsernameUser"]);

			$stmt3 = $conn->prepare("INSERT INTO trvsol_invoices (numero, mes, year, fecha, fechaComplete, vendedor, idSeller, productos, productos_cambio, productosArray, productosArray_autoPrint, formaPago, subtotal, descuentos, multipagoEfectivo, multipagoTarjeta, multipagoOtro, recibido, cambio, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt3->bind_param("siisssisssssiiiiisis", $completeNum, $date1, $date2, $date3, $date4, $ucfirstString, $_COOKIE[$prefixCoookie . "IdUser"], $_POST["createSaleProducts"], $_POST["createSaleProductsChangeTicket"], $_POST["createSaleProductsArray"], $_POST["createSaleProductsArrayAuto"], $payment, $_POST["createSaleSubtotal"], $_POST["createSaleDiscounts"], $_POST["createSaleMultiEf"], $_POST["createSaleMultiTa"], $_POST["createSaleMultiOt"], $received, $_POST["createSaleChange"], $_POST["createSaleNotes"]);
			if ($stmt3->execute()) {
				$saleCreated = true;
				$idSale = $conn->insert_id;

				//Update stats
				$sql4 = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
				$result4 = $conn->query($sql4);

				if ($result4->num_rows > 0) {
					$row4 = $result4->fetch_assoc();

					$totalVenta = $_POST["createSaleSubtotal"] - $_POST["createSaleDiscounts"];

					$decoded = json_decode($row4["estadisticas"], true);
					$decoded[date("d") - 1]["numberSales"] += 1;

					if ($_POST["createSalePayment"] == "E") {
						$decoded[date("d") - 1]["cashSales"] += $totalVenta;
					} else if ($_POST["createSalePayment"] == "T") {
						$decoded[date("d") - 1]["cardSales"] += $totalVenta;
					} else if ($_POST["createSalePayment"] == "M") {
						$decoded[date("d") - 1]["cashSales"] += $_POST["createSaleMultiEf"];
						$decoded[date("d") - 1]["cardSales"] += $_POST["createSaleMultiTa"];
						$decoded[date("d") - 1]["otherSales"] += $_POST["createSaleMultiOt"];
					} else if ($_POST["createSalePayment"] == "O") {
						$decoded[date("d") - 1]["otherSales"] += $totalVenta;
					}

					$sql5 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . date("m") . " AND year=" . date("Y");
					if ($conn->query($sql5) === TRUE) {
						$arrayProducts = json_decode($_POST["createSaleProductsArray"], true);

						//INVENTORY - Stock before
						$arrayProdsUnique = array_values(array_unique($arrayProducts));
						for ($x5 = 0; $x5 < count($arrayProdsUnique); ++$x5) {
							$sql24 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x5];
							$result24 = $conn->query($sql24);
							if ($result24->num_rows > 0) {
								$row24 = $result24->fetch_assoc();

								$beforeStockUpd = $row24["stock"];
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
						for ($x = 0; $x < count($arrayProducts); ++$x) {
							$sql6 = "SELECT * FROM trvsol_products WHERE id=" . $arrayProducts[$x];
							$result6 = $conn->query($sql6);

							if ($result6->num_rows > 0) {
								$row6 = $result6->fetch_assoc();

								$stockProducto = $row6["stock"] - 1;
								$ventasMensuales = $row6["ventasMensuales"] + 1;

								$sql7 = "UPDATE trvsol_products SET stock='" . $stockProducto . "', ventasMensuales='" . $ventasMensuales . "' WHERE id=" . $arrayProducts[$x];
								$conn->query($sql7);

								++$prodsAgregados;

								//Update prods stats
								$sql10 = "SELECT * FROM trvsol_products_stats WHERE year=" . date("Y") . " AND productId=" . $arrayProducts[$x];
								$result10 = $conn->query($sql10);

								if ($result10->num_rows > 0) {
									$row10 = $result10->fetch_assoc();

									$decoded2 = json_decode($row10["estadisticas"], true);

									for ($x2 = 0; $x2 < count($decoded2); ++$x2) {
										$dateRead = date('Y-m-d', strtotime($decoded2[$x2]["date"]));

										if ($dateRead == date("Y-m-d")) {
											$decoded2[$x2]["quantitiesSold"] += 1;

											$sql11 = "UPDATE trvsol_products_stats SET estadisticas='" . json_encode($decoded2) . "' WHERE year=" . date("Y") . " AND productId=" . $arrayProducts[$x];
											$conn->query($sql11);
										}
									}
								}
							}
						}

						//Update user stats
						$sql12 = "SELECT * FROM trvsol_users_stats WHERE year=" . date("Y") . " AND userId=" . $_COOKIE[$prefixCoookie . "IdUser"];
						$result12 = $conn->query($sql12);

						if ($result12->num_rows > 0) {
							$row12 = $result12->fetch_assoc();

							$decoded3 = json_decode($row12["estadisticas"], true);

							for ($x3 = 0; $x3 < count($decoded3); ++$x3) {
								$dateRead = date('Y-m-d', strtotime($decoded3[$x3]["date"]));

								if ($dateRead == date("Y-m-d")) {
									if ($_POST["createSalePayment"] == "E") {
										$decoded3[$x3]["cashSales"] += $totalVenta;
									} else if ($_POST["createSalePayment"] == "T") {
										$decoded3[$x3]["cardSales"] += $totalVenta;
									} else if ($_POST["createSalePayment"] == "M") {
										$decoded3[$x3]["cashSales"] += $_POST["createSaleMultiEf"];
										$decoded3[$x3]["cardSales"] += $_POST["createSaleMultiTa"];
										$decoded3[$x3]["otherSales"] += $_POST["createSaleMultiOt"];
									} else if ($_POST["createSalePayment"] == "O") {
										$decoded3[$x3]["otherSales"] += $totalVenta;
									}

									$sql13 = "UPDATE trvsol_users_stats SET estadisticas='" . json_encode($decoded3) . "' WHERE year=" . date("Y") . " AND userId=" . $_COOKIE[$prefixCoookie . "IdUser"];
									$conn->query($sql13);
								}
							}
						}

						//INVENTORY - Stock after
						for ($x6 = 0; $x6 < count($arrayProdsUnique); ++$x6) {
							$sql25 = "SELECT id, stock FROM trvsol_products WHERE id=" . $arrayProdsUnique[$x6];
							$result25 = $conn->query($sql25);
							if ($result25->num_rows > 0) {
								$row25 = $result25->fetch_assoc();

								$afterStockUpd = $row25["stock"];
								++$afterStockUpd;
								--$afterStockUpd;

								$differenceStock = $afterStockUpd - $newArrayUpdate[$x6]["stock_before"];

								$newArrayUpdate[$x6]["stock_after"] = $afterStockUpd;
								$newArrayUpdate[$x6]["difference"] = $differenceStock;
							}
						}

						//INVENTORY - History
						$sql22 = "SELECT id, date, productsArray, productsArrayComplete, productsAdded FROM trvsol_inventory WHERE date='" . date("Y-m-d") . "' AND type='sales'";
						$result22 = $conn->query($sql22);

						if ($result22->num_rows > 0) {
							$row22 = $result22->fetch_assoc();

							$decoded5 = json_decode($row22["productsArrayComplete"], true);
							$decoded6 = json_decode($row22["productsArray"], true);

							for ($x7 = 0; $x7 < count($newArrayUpdate); ++$x7) {
								array_push($decoded5, $newArrayUpdate[$x7]);
							}
							for ($x8 = 0; $x8 < count($arrayProducts); ++$x8) {
								array_push($decoded6, $arrayProducts[$x8]);
							}

							$totalProdsAdded = $row22["productsAdded"] + $prodsAgregados;

							$sql26 = "UPDATE trvsol_inventory SET productsArray='" . json_encode($decoded6) . "', productsArrayComplete='" . json_encode($decoded5) . "', productsAdded='" . $totalProdsAdded . "' WHERE id=" . $row22["id"];
							$conn->query($sql26);
						} else {
							$sql23 = "INSERT INTO trvsol_inventory (date, hour, type, reason, notes, productsArray, productsArrayComplete, productsAdded)
	VALUES ('" . date("Y-m-d") . "', '" . date("H:i") . "', 'sales', 'Venta de artículos', '', '" . $_POST["createSaleProductsArray"] . "', '" . json_encode($newArrayUpdate)	. "', '" . $prodsAgregados . "');";
							$conn->query($sql23);
						}

						//Replace values printing template
						$sql8 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice'";
						$result8 = $conn->query($sql8);

						if ($result8->num_rows > 0) {
							$row8 = $result8->fetch_assoc();

							$find =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}", "{{trv_payment_method}}", "{{trv_subtotal}}", "{{trv_discount}}", "{{trv_total}}", "{{trv_change_received}}", "{{trv_change}}", "{{trv_notes}}");
							$replace = array(date("d-m-Y h:i a"), $row2["value"] . $row["value"], ucfirst($_COOKIE[$prefixCoookie . "UsernameUser"]), $_POST["createSaleProducts"], $payment, number_format($_POST["createSaleSubtotal"], 0, ",", "."), number_format($_POST["createSaleDiscounts"], 0, ",", "."), number_format($totalVenta, 0, ",", "."), $received, number_format($_POST["createSaleChange"], 0, ",", "."), $_POST["createSaleNotes"]);

							$printingTemplate = str_replace($find, $replace, $row8["value"]);
							$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

							//Auto printing?
							$sql27 = "SELECT * FROM trvsol_configuration WHERE configName= 'printingAuto'";
							$result27 = $conn->query($sql27);

							if ($result27->num_rows > 0) {
								$row27 = $result27->fetch_assoc();

								if ($row27["value"] == 1 && $_POST["createSaleAutoNoPrint"] == 1) {
									$autoPrinting = true;
									autoPrintInvoice($idSale, $_POST["createSaleChangeTicketsNum"], false);
								}
							}
						}

						//Update receipt num
						$numeroFactura = $row["value"] + 1;
						$sql9 = "UPDATE trvsol_configuration SET value=" . $numeroFactura . " WHERE configName='numInvoice'";
						$conn->query($sql9);

						//Update coupons info, if applicable
						if ($_POST["createSaleVoucherID"] != "" && $_POST["createSaleVoucherID"] != 0) {
							$sql16 = "SELECT * FROM trvsol_vouchers WHERE id= " . $_POST["createSaleVoucherID"];
							$result16 = $conn->query($sql16);
							if ($result16->num_rows > 0) {
								$row16 = $result16->fetch_assoc();

								$totalAvailableFinal = $row16["totalAvailable"] - 1;

								$sql17 = "UPDATE trvsol_vouchers SET totalAvailable='" . $totalAvailableFinal . "' WHERE id=" . $_POST["createSaleVoucherID"];
								$conn->query($sql17);

								$sql18 = "SELECT * FROM trvsol_vouchers_stats WHERE year=" . date("Y") . " AND voucherId=" . $_POST["createSaleVoucherID"];
								$result18 = $conn->query($sql18);
								if ($result18->num_rows > 0) {
									$row18 = $result18->fetch_assoc();

									$decoded4 = json_decode($row18["estadisticas"], true);

									for ($x4 = 0; $x4 < count($decoded4); ++$x4) {
										$dateRead = date('Y-m-d', strtotime($decoded4[$x4]["date"]));

										if ($dateRead == date("Y-m-d")) {
											$decoded4[$x4]["uses"] += 1;

											$sql19 = "UPDATE trvsol_vouchers_stats SET estadisticas='" . json_encode($decoded4) . "' WHERE year=" . date("Y") . " AND voucherId=" . $_POST["createSaleVoucherID"];
											$conn->query($sql19);
										}
									}
								}
							}
						}
					} else {
						$existeError = true;
					}
				}
			} else {
				$existeError = true;
			}
		} else {
			$existeError = true;
		}

		$sql14 = "SELECT * FROM trvsol_configuration WHERE configName= 'deleteInvoiceNumbersAuto'";
		$result14 = $conn->query($sql14);

		if ($result14->num_rows > 0) {
			$row14 = $result14->fetch_assoc();

			if ($row14["value"] != "" && $row14["value"] == $row["value"]) {
				$sql15 = "UPDATE trvsol_configuration SET value=1 WHERE configName='numInvoice'";
				$conn->query($sql15);
			}
		}
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'venta_creada' => $saleCreated,
	'id_venta' => $idSale,
	'plantilla_impresion' => $printingTemplate,
	'auto_print' => $autoPrinting
);
echo json_encode(convertJson($varsSend));
?>