<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Print Sales Receipt</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onload="printPage()">
	<div class="content">
		<?php
		$printingTemplate = "";

		if (isset($_GET["idInvoice"])) {
			$sql = "SELECT * FROM trvsol_invoices WHERE id=" . $_GET["idInvoice"] . " AND cancelada=0";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();

				$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice'";
				$result2 = $conn->query($sql2);
				if ($result2->num_rows > 0) {
					$row2 = $result2->fetch_assoc();

					$totalSale = $row["subtotal"] - $row["descuentos"];

					$find =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}", "{{trv_payment_method}}", "{{trv_subtotal}}", "{{trv_discount}}", "{{trv_total}}", "{{trv_change_received}}", "{{trv_change}}", "{{trv_notes}}");
					$replace = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos"], $row["formaPago"], number_format($row["subtotal"], 0, ",", "."), number_format($row["descuentos"], 0, ",", "."), number_format($totalSale, 0, ",", "."), $row["recibido"], number_format($row["cambio"], 0, ",", "."), $row["notas"]);

					$printingTemplate = str_replace($find, $replace, $row2["value"]);
					$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

					//Change tickets
					if (isset($_GET["numberChangeTickets"]) && $_GET["numberChangeTickets"] > 0) {
						$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTicketsTemplate'";
						$result3 = $conn->query($sql3);
						if ($result3->num_rows > 0) {
							$row3 = $result3->fetch_assoc();

							$find2 =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}");
							$replace2 = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos_cambio"]);

							$changeTicketTemplate = str_replace($find2, $replace2, $row3["value"]);

							for ($x = 1; $x <= $_GET["numberChangeTickets"]; ++$x) {
								$printingTemplate .= '<hr class= "dottedHr">' . $changeTicketTemplate;
							}
						}
					}
				}
			}
		} else {
			$printingTemplate = "There was an error";
		}

		echo $printingTemplate;
		?>
	</div>

	<script>
		function printPage() {
			setTimeout(function() {
				window.print();
				window.close();
			}, 500);
		}
	</script>
</body>

</html>