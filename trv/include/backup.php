<?php include_once "DBOptimization.php";

function generateBackup()
{
	global $conn, $backupCreado, $existeError, $dbOptimize_trvsol_admin_images, $dbOptimize_trvsol_categories, $dbOptimize_trvsol_invoices, $dbOptimize_trvsol_products, $dbOptimize_trvsol_products_stats, $dbOptimize_trvsol_stats, $dbOptimize_trvsol_users, $dbOptimize_trvsol_users_stats, $dbOptimize_trvsol_vouchers, $dbOptimize_trvsol_vouchers_stats, $dbOptimize_trvsol_configuration, $dbOptimize_trvsol_inventory;

	//Backup
	$backupFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/trv/include/backups/backup-" . date("Y-m-d-h-ia") . ".sql", "w");
	$nombreEmpresaBackup = '';
	$numberDefault = "'1'";

	$backupContent = '-- Crear las tablas
	-- trvsol_admin_images
	' . $dbOptimize_trvsol_admin_images . '
	
	-- trvsol_categories
	' . $dbOptimize_trvsol_categories . '
	
	-- trvsol_invoices
	' . $dbOptimize_trvsol_invoices . '
	
	-- trvsol_products
	' . $dbOptimize_trvsol_products . '
	
	-- trvsol_products_stats
	' . $dbOptimize_trvsol_products_stats . '
	
	-- trvsol_stats
	' . $dbOptimize_trvsol_stats . '
	
	-- trvsol_users
	' . $dbOptimize_trvsol_users . '
	
	-- trvsol_users_stats
	' . $dbOptimize_trvsol_users_stats . '
	
	-- trvsol_vouchers
	' . $dbOptimize_trvsol_vouchers . '
	
	-- trvsol_vouchers_stats
	' . $dbOptimize_trvsol_vouchers_stats . '
	
	-- trvsol_configuration
	' . $dbOptimize_trvsol_configuration . '
	
	-- trvsol_inventory
	' . $dbOptimize_trvsol_inventory . '
	
	-- ------------------------------
	
	';

	//Database: trvsol_categories
	$totalNumCats = 0;
	$gettingNumCats = 0;

	$sqlDBVer1 = "SELECT id FROM trvsol_categories";
	$resultDBVer1 = $conn->query($sqlDBVer1);
	if ($resultDBVer1->num_rows > 0) {
		while ($rowDBVer1 = $resultDBVer1->fetch_assoc()) {
			++$totalNumCats;
		}
	}

	$sqlDB2 = "SELECT * FROM trvsol_categories";
	$resultDB2 = $conn->query($sqlDB2);
	if ($resultDB2->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_categories (id, nombre, color, color_txt) VALUES';

		while ($rowDB2 = $resultDB2->fetch_assoc()) {
			$valuesInput1 = "'" . $rowDB2["id"] . "', '" . $rowDB2["nombre"] . "', '" . $rowDB2["color"] . "', '" . $rowDB2["color_txt"] . "'";
			$backupContent .= ' (' . $valuesInput1 . ')';

			++$gettingNumCats;
			if ($gettingNumCats == $totalNumCats) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_invoices
	$totalNumInv = 0;
	$gettingNumInv = 0;

	$sqlDBVer2 = "SELECT id FROM trvsol_invoices";
	$resultDBVer2 = $conn->query($sqlDBVer2);
	if ($resultDBVer2->num_rows > 0) {
		while ($rowDBVer2 = $resultDBVer2->fetch_assoc()) {
			++$totalNumInv;
		}
	}

	$sqlDB3 = "SELECT * FROM trvsol_invoices";
	$resultDB3 = $conn->query($sqlDB3);
	if ($resultDB3->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_invoices (id, numero, mes, year, fecha, fechaComplete, vendedor, idSeller, productos, productos_cambio, productosArray, formaPago, subtotal, descuentos, multipagoEfectivo, multipagoTarjeta, multipagoOtro, recibido, cambio, notas, cancelada, canceladaPor) VALUES';

		while ($rowDB3 = $resultDB3->fetch_assoc()) {
			$valuesInput2 = "'" . $rowDB3["id"] . "', '" . $rowDB3["numero"] . "', '" . $rowDB3["mes"] . "', '" . $rowDB3["year"] . "', '" . $rowDB3["fecha"] . "', '" . $rowDB3["fechaComplete"] . "', '" . $rowDB3["vendedor"] . "', '" . $rowDB3["idSeller"] . "', '" . $rowDB3["productos"] . "', '" . $rowDB3["productos_cambio"] . "', '" . $rowDB3["productosArray"] . "', '" . $rowDB3["formaPago"] . "', '" . $rowDB3["subtotal"] . "', '" . $rowDB3["descuentos"] . "', '" . $rowDB3["multipagoEfectivo"] . "', '" . $rowDB3["multipagoTarjeta"] . "', '" . $rowDB3["multipagoOtro"] . "', '" . $rowDB3["recibido"] . "', '" . $rowDB3["cambio"] . "', '" . $rowDB3["notas"] . "', '" . $rowDB3["cancelada"] . "', '" . $rowDB3["canceladaPor"] . "'";

			$backupContent .= ' (' . $valuesInput2 . ')';

			++$gettingNumInv;
			if ($gettingNumInv == $totalNumInv) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_products
	$totalNumProds = 0;
	$gettingNumProds = 0;

	$sqlDBVer3 = "SELECT id FROM trvsol_products";
	$resultDBVer3 = $conn->query($sqlDBVer3);
	if ($resultDBVer3->num_rows > 0) {
		while ($rowDBVer3 = $resultDBVer3->fetch_assoc()) {
			++$totalNumProds;
		}
	}

	$sqlDB4 = "SELECT * FROM trvsol_products";
	$resultDB4 = $conn->query($sqlDB4);
	if ($resultDB4->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_products (id, nombre, precio, imagen, barcode, categoryID, purchasePrice, stock, tags, activo, ventasMensuales) VALUES';

		while ($rowDB4 = $resultDB4->fetch_assoc()) {
			$valuesInput3 = "'" . $rowDB4["id"] . "', '" . $rowDB4["nombre"] . "', '" . $rowDB4["precio"] . "', '" . $rowDB4["imagen"] . "', '" . $rowDB4["barcode"] . "', '" . $rowDB4["categoryID"] . "', '" . $rowDB4["purchasePrice"] . "', '" . $rowDB4["stock"] . "', '" . $rowDB4["tags"] . "', '" . $rowDB4["activo"] . "', '" . $rowDB4["ventasMensuales"] . "'";

			$backupContent .= ' (' . $valuesInput3 . ')';

			++$gettingNumProds;
			if ($gettingNumProds == $totalNumProds) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_products_stats
	$totalNumProdsStats = 0;
	$gettingNumProdsStats = 0;

	$sqlDBVer4 = "SELECT id FROM trvsol_products_stats";
	$resultDBVer4 = $conn->query($sqlDBVer4);
	if ($resultDBVer4->num_rows > 0) {
		while ($rowDBVer4 = $resultDBVer4->fetch_assoc()) {
			++$totalNumProdsStats;
		}
	}

	$sqlDB5 = "SELECT * FROM trvsol_products_stats";
	$resultDB5 = $conn->query($sqlDB5);
	if ($resultDB5->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_products_stats (id, year, productId, estadisticas) VALUES';

		while ($rowDB5 = $resultDB5->fetch_assoc()) {
			$valuesInput4 = "'" . $rowDB5["id"] . "', '" . $rowDB5["year"] . "', '" . $rowDB5["productId"] . "', '" . $rowDB5["estadisticas"] . "'";

			$backupContent .= ' (' . $valuesInput4 . ')';

			++$gettingNumProdsStats;
			if ($gettingNumProdsStats == $totalNumProdsStats) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_stats
	$totalNumStats = 0;
	$gettingNumStats = 0;

	$sqlDBVer5 = "SELECT id FROM trvsol_stats";
	$resultDBVer5 = $conn->query($sqlDBVer5);
	if ($resultDBVer5->num_rows > 0) {
		while ($rowDBVer5 = $resultDBVer5->fetch_assoc()) {
			++$totalNumStats;
		}
	}

	$sqlDB6 = "SELECT * FROM trvsol_stats";
	$resultDB6 = $conn->query($sqlDB6);
	if ($resultDB6->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_stats (id, mes, year, estadisticas, reportSent) VALUES';

		while ($rowDB6 = $resultDB6->fetch_assoc()) {
			$valuesInput5 = "'" . $rowDB6["id"] . "', '" . $rowDB6["mes"] . "', '" . $rowDB6["year"] . "', '" . $rowDB6["estadisticas"] . "', '" . $rowDB6["reportSent"] . "'";

			$backupContent .= ' (' . $valuesInput5 . ')';

			++$gettingNumStats;
			if ($gettingNumStats == $totalNumStats) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_users
	$totalNumUsers = 0;
	$gettingNumUsers = 0;

	$sqlDBVer6 = "SELECT id FROM trvsol_users";
	$resultDBVer6 = $conn->query($sqlDBVer6);
	if ($resultDBVer6->num_rows > 0) {
		while ($rowDBVer6 = $resultDBVer6->fetch_assoc()) {
			++$totalNumUsers;
		}
	}

	$sqlDB7 = "SELECT * FROM trvsol_users";
	$resultDB7 = $conn->query($sqlDB7);
	if ($resultDB7->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_users (id, username, password, inventory, admin, securityCode) VALUES';

		while ($rowDB7 = $resultDB7->fetch_assoc()) {
			$valuesInput6 = "'" . $rowDB7["id"] . "', '" . $rowDB7["username"] . "', '" . $rowDB7["password"] . "', '" . $rowDB7["inventory"] . "', '" . $rowDB7["admin"] . "', '" . $rowDB7["securityCode"] . "'";

			$backupContent .= ' (' . $valuesInput6 . ')';

			++$gettingNumUsers;
			if ($gettingNumUsers == $totalNumUsers) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_users_stats
	$totalNumUsersStats = 0;
	$gettingNumUsersStats = 0;

	$sqlDBVer7 = "SELECT id FROM trvsol_users_stats";
	$resultDBVer7 = $conn->query($sqlDBVer7);
	if ($resultDBVer7->num_rows > 0) {
		while ($rowDBVer7 = $resultDBVer7->fetch_assoc()) {
			++$totalNumUsersStats;
		}
	}

	$sqlDB8 = "SELECT * FROM trvsol_users_stats";
	$resultDB8 = $conn->query($sqlDB8);
	if ($resultDB8->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_users_stats (id, year, userId, estadisticas) VALUES';

		while ($rowDB8 = $resultDB8->fetch_assoc()) {
			$valuesInput7 = "'" . $rowDB8["id"] . "', '" . $rowDB8["year"] . "', '" . $rowDB8["userId"] . "', '" . $rowDB8["estadisticas"] . "'";

			$backupContent .= ' (' . $valuesInput7 . ')';

			++$gettingNumUsersStats;
			if ($gettingNumUsersStats == $totalNumUsersStats) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_vouchers
	$totalNumVouchers = 0;
	$gettingNumVouchers = 0;

	$sqlDBVer8 = "SELECT id FROM trvsol_vouchers";
	$resultDBVer8 = $conn->query($sqlDBVer8);
	if ($resultDBVer8->num_rows > 0) {
		while ($rowDBVer8 = $resultDBVer8->fetch_assoc()) {
			++$totalNumVouchers;
		}
	}

	$sqlDB9 = "SELECT * FROM trvsol_vouchers";
	$resultDB9 = $conn->query($sqlDB9);
	if ($resultDB9->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_vouchers (id, code, totalAvailable, minimumQuantity, value, paymentMethods, expiration, color, color_txt) VALUES';

		while ($rowDB9 = $resultDB9->fetch_assoc()) {
			$valuesInput8 = "'" . $rowDB9["id"] . "', '" . $rowDB9["code"] . "', '" . $rowDB9["totalAvailable"] . "', '" . $rowDB9["minimumQuantity"] . "', '" . $rowDB9["value"] . "', '" . $rowDB9["paymentMethods"] . "', '" . $rowDB9["expiration"] . "', '" . $rowDB9["color"] . "', '" . $rowDB9["color_txt"] . "'";

			$backupContent .= ' (' . $valuesInput8 . ')';

			++$gettingNumVouchers;
			if ($gettingNumVouchers == $totalNumVouchers) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_vouchers_stats
	$totalNumVouchersStats = 0;
	$gettingNumVouchersStats = 0;

	$sqlDBVer9 = "SELECT id FROM trvsol_vouchers_stats";
	$resultDBVer9 = $conn->query($sqlDBVer9);
	if ($resultDBVer9->num_rows > 0) {
		while ($rowDBVer9 = $resultDBVer9->fetch_assoc()) {
			++$totalNumVouchersStats;
		}
	}

	$sqlDB10 = "SELECT * FROM trvsol_vouchers_stats";
	$resultDB10 = $conn->query($sqlDB10);
	if ($resultDB10->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_vouchers_stats (id, year, voucherId, estadisticas) VALUES';

		while ($rowDB10 = $resultDB10->fetch_assoc()) {
			$valuesInput9 = "'" . $rowDB10["id"] . "', '" . $rowDB10["year"] . "', '" . $rowDB10["voucherId"] . "', '" . $rowDB10["estadisticas"] . "'";

			$backupContent .= ' (' . $valuesInput9 . ')';

			++$gettingNumVouchersStats;
			if ($gettingNumVouchersStats == $totalNumVouchersStats) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_inventory
	$totalNumInventory = 0;
	$gettingNumInventory = 0;

	$sqlDBVer10 = "SELECT id FROM trvsol_inventory";
	$resultDBVer10 = $conn->query($sqlDBVer10);
	if ($resultDBVer10->num_rows > 0) {
		while ($rowDBVer10 = $resultDBVer10->fetch_assoc()) {
			++$totalNumInventory;
		}
	}

	$sqlDB11 = "SELECT * FROM trvsol_inventory";
	$resultDB11 = $conn->query($sqlDB11);
	if ($resultDB11->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_inventory (id, date, hour, type, reason, notes, productsArray, productsArrayComplete, productsAdded) VALUES';

		while ($rowDB11 = $resultDB11->fetch_assoc()) {
			$valuesInput10 = "'" . $rowDB11["id"] . "', '" . $rowDB11["date"] . "', '" . $rowDB11["hour"] . "', '" . $rowDB11["type"] . "', '" . $rowDB11["reason"] . "', '" . $rowDB11["notes"] . "', '" . $rowDB11["productsArray"] . "', '" . $rowDB11["productsArrayComplete"] . "', '" . $rowDB11["productsAdded"] . "'";

			$backupContent .= ' (' . $valuesInput10 . ')';

			++$gettingNumInventory;
			if ($gettingNumInventory == $totalNumInventory) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	//Database: trvsol_configuration
	$totalNumConfig = 0;
	$gettingNumConfig = 0;

	$sqlDBVer11 = "SELECT id FROM trvsol_configuration";
	$resultDBVer11 = $conn->query($sqlDBVer11);
	if ($resultDBVer11->num_rows > 0) {
		while ($rowDBVer11 = $resultDBVer11->fetch_assoc()) {
			++$totalNumConfig;
		}
	}

	$sqlDB12 = "SELECT * FROM trvsol_configuration";
	$resultDB12 = $conn->query($sqlDB12);
	if ($resultDB12->num_rows > 0) {
		$backupContent .= 'INSERT INTO trvsol_configuration (id, configName, value) VALUES';

		while ($rowDB12 = $resultDB12->fetch_assoc()) {
			$valuesInput11 = "'" . $rowDB12["id"] . "', '" . $rowDB12["configName"] . "', '" . $rowDB12["value"] . "'";

			$backupContent .= ' (' . $valuesInput11 . ')';

			++$gettingNumConfig;
			if ($gettingNumConfig == $totalNumConfig) {
				$backupContent .= ';';
			} else {
				$backupContent .= ',';
			}
		}
	}

	if (fwrite($backupFile, $backupContent)) {
		$backupCreado = true;
	} else {
		$existeError = true;
	}

	fclose($backupFile);
}
?>