<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$salesList = "";

if (isset($_POST["getInvoicesDateFrom"]) && isset($_POST["getInvoicesDateTo"]) && isset($_POST["getInvoicesQuery"])) {
	$fecha1 = date('Y-m-d', strtotime($_POST["getInvoicesDateFrom"]));
	$fecha2 = date('Y-m-d', strtotime($_POST["getInvoicesDateTo"]));

	$sql = "SELECT * FROM trvsol_invoices WHERE fecha BETWEEN '" . $fecha1 . "' AND '" . $fecha2 . "' AND numero LIKE '%" . $_POST["getInvoicesQuery"] . "%'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$totalVenta = $row["subtotal"] - $row["descuentos"];

			$onclick1 = "shareElement(" . $row["id"] . ", '" . $row["numero"] . "')";
			$onclick2 = "sendEmail(" . $row["id"] . ")";
			$onclick3 = "cancelSale(" . $row["id"] . ")";
			$onclick4 = "generatePDF(" . $row["id"] . ", '" . $row["numero"] . "')";

			$decoded = json_decode($row["productosArray"], true);
			$bgColor = "";
			if ($row["cancelada"] == true) {
				$bgColor = "has-background-danger-light";
			}

			$pluralAddS = "s";
			if (count($decoded) == 1) {
				$pluralAddS = "";
			}

			$paymentBgColor = "pastel-bg-green";
			if ($row["formaPago"] == "Card") {
				$paymentBgColor = "pastel-bg-purple";
			} else if ($row["formaPago"] == "Multipayment") {
				$paymentBgColor = "pastel-bg-darkorange";
			} else if ($row["formaPago"] != "Cash") {
				$paymentBgColor = "pastel-bg-cyan";
			}

			$salesList .= '<div class="list-item ' . $bgColor . '">
		<div class="list-item-content">
		<div class="list-item-title">Receipt ' . $row["numero"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">$' . number_format($totalVenta, 0, ",", ".") . '</span> <span class="tag is-rounded">' . $row["fechaComplete"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded ' . $paymentBgColor . '">' . $row["formaPago"] . '</span> <span class="tag is-rounded"><b>' . count($decoded) . ' product' . $pluralAddS . '</b></span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<button class="button backgroundDark" onclick= "' . $onclick1 . '"><i class="fas fa-clipboard-list"></i> Details</button>';

			if ($row["cancelada"] != true) {
				$salesList .= '<button class="button is-light is-info" onclick= "' . $onclick2 . '" title= "Send by email"><i class="fas fa-envelope"></i></button>
		<button class="button is-light" onclick= "' . $onclick4 . '" title= "Download PDF"><i class="fas fa-file-pdf"></i></button>
		<button class="button is-light is-danger" onclick= "' . $onclick3 . '" title= "Cancel sale"><i class="fas fa-ban"></i></button>
		</div>
		</div>
	</div>';
			} else {
				$salesList .= '<button class="button is-danger" disabled>Sale canceled by: ' . $row["canceladaPor"] . '</button>
		</div>
		</div>
	</div>';
			}
		}
	} else {
		$salesList = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'comprobantes' => $salesList
);
echo json_encode(convertJson($varsSend));
?>