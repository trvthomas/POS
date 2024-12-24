 <?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php";

	$existeError = false;
	$listaHistorial = "";
	$lastPage = false;

	if (isset($_POST["getHistoryCategory"]) && isset($_POST["getHistoryDateFrom"]) && isset($_POST["getHistoryDateTo"]) && isset($_POST["getHistoryPage"])) {
		if ($_POST["getHistoryCategory"] != "0") {
			$sql = "SELECT * FROM trvsol_inventory WHERE type='" . $_POST["getHistoryCategory"] . "'";

			if ($_POST["getHistoryDateFrom"] != "N/A" && $_POST["getHistoryDateTo"] != "N/A") {
				$fecha1 = date('Y-m-d', strtotime($_POST["getHistoryDateFrom"]));
				$fecha2 = date('Y-m-d', strtotime($_POST["getHistoryDateTo"]));

				$sql .= " AND date BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
			}
		} else {
			$sql = "SELECT * FROM trvsol_inventory";

			if ($_POST["getHistoryDateFrom"] != "N/A" && $_POST["getHistoryDateTo"] != "N/A") {
				$fecha1 = date('Y-m-d', strtotime($_POST["getHistoryDateFrom"]));
				$fecha2 = date('Y-m-d', strtotime($_POST["getHistoryDateTo"]));

				$sql .= " WHERE date BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "'";
			}
		}

		$sql .= " ORDER BY date DESC";

		$limit2 = $_POST["getHistoryPage"] * 10 * 4;
		$limit1 = $limit2 - 40;
		$sql .= " LIMIT " . $limit1 . ", 40";

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$imageProduct = "/trv/media/imagen-no-disponible.png";
				$titleType = "Error";
				$addPoints = "";
				$iconAlert = "";
				$colorBorder = "";
				$onclick2 = "document.getElementById('overlayVariableMovement').style.display= 'block';";

				if ($row["type"] == "entry") {
					$imageProduct = "/trv/media/inventario-entry.png";
					$titleType = "Entry";
					$colorBorder = "#660066";
				} else if ($row["type"] == "exit") {
					$imageProduct = "/trv/media/inventario-exit.png";
					$titleType = "Exit";
					$colorBorder = "#ff6600";
				} else if ($row["type"] == "adjust") {
					$imageProduct = "/trv/media/inventario-adjust.png";
					$titleType = "Adjustment";
					$colorBorder = "#006699";
				} else if ($row["type"] == "sales") {
					$imageProduct = "/trv/media/inventario-sales.png";
					$titleType = "Sale";
					$colorBorder = "#008000";
				} else if ($row["type"] == "saleCancel") {
					$imageProduct = "/trv/media/inventario-cancel.png";
					$titleType = "Canceled Sale";
					$colorBorder = "#ef4d4d";
				}

				if (strlen($row["notes"]) > 30) {
					$addPoints = "...";
				}

				if ($row["type"] == "sales" && $row["date"] == date("Y-m-d")) {
					$iconAlert = '<i class="fas fa-circle-exclamation" title="Variable movement - Click for more information" onclick= "' . $onclick2 . '"></i>';
				}

				$listaHistorial .= '<div class="list-item">
		<div class="list-item-image">
		<a href="/trv/inventory/movement-details.php?id=' . $row["id"] . '"><figure class="image is-64x64"><img src="' . $imageProduct . '" class= "is-rounded" style= "border: 2px solid ' . $colorBorder . ';"></figure></a>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $titleType . $iconAlert . '</div>
		<div class="list-item-description">' . $row["reason"] . ' &#8226; ' . substr($row["notes"], 0, 30) . $addPoints . '</div>
		<div class="list-item-description"><span class="tag is-rounded">' . date("d-m-Y", strtotime($row["date"])) . '</span> <span class="tag is-rounded"><b>' . $row["productsAdded"] . ' affected products</b></span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<a class="button backgroundDark" href="/trv/inventory/movement-details.php?id=' . $row["id"] . '"><i class="fas fa-clipboard-list"></i> Details</a>
		</div>
		</div>
	</div>';
			}
		} else {
			$lastPage = true;
			$listaHistorial = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";
		}
	} else {
		$existeError = true;
	}

	$varsSend = array(
		'errores' => $existeError,
		'historial' => $listaHistorial,
		'ultima_pagina' => $lastPage
	);
	echo json_encode(convertJson($varsSend));
?>