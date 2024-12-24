<?php include_once "DBData.php";
include_once "autoPrinting.php";

$existeError = false;
$printingTemplate = false;
$changeTicketTemplate = false;
$autoPrinting = false;

if (isset($_POST["generateTemplateIDInvoice"]) && isset($_POST["generateTemplatePrintOrSend"]) && isset($_POST["generateTemplateAutoChangeTickets"])) {
	$sql = "SELECT * FROM trvsol_invoices WHERE id=" . $_POST["generateTemplateIDInvoice"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice'";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_assoc();

			$totalVenta = $row["subtotal"] - $row["descuentos"];

			$find =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}", "{{trv_payment_method}}", "{{trv_subtotal}}", "{{trv_discount}}", "{{trv_total}}", "{{trv_change_received}}", "{{trv_change}}", "{{trv_notes}}");
			$replace = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos"], $row["formaPago"], number_format($row["subtotal"], 0, ",", "."), number_format($row["descuentos"], 0, ",", "."), number_format($totalVenta, 0, ",", "."), $row["recibido"], number_format($row["cambio"], 0, ",", "."), $row["notas"]);

			$printingTemplate = str_replace($find, $replace, $row2["value"]);
			$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutionss.com</b></p>
	</div>';

			//Auto printing?
			$sql27 = "SELECT * FROM trvsol_configuration WHERE configName= 'printingAuto'";
			$result27 = $conn->query($sql27);

			if ($result27->num_rows > 0) {
				$row27 = $result27->fetch_assoc();

				if ($row27["value"] == 1 && $_POST["generateTemplatePrintOrSend"] != "S") {
					$autoPrinting = true;

					$printOnlyChange = false;
					if ($_POST["generateTemplateAutoChangeTickets"] != "" && $_POST["generateTemplateAutoChangeTickets"] > 0 && $_POST["generateTemplateAutoChangeTickets"] <= 5) {
						$printOnlyChange = true;
					}

					autoPrintInvoice($_POST["generateTemplateIDInvoice"], $_POST["generateTemplateAutoChangeTickets"], $printOnlyChange);
				}
			}

			//Change ticket template
			$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTicketsTemplate'";
			$result3 = $conn->query($sql3);

			if ($result3->num_rows > 0) {
				$row3 = $result3->fetch_assoc();

				$find2 =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}");
				$replace2 = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos_cambio"]);

				$changeTicketTemplate = str_replace($find2, $replace2, $row3["value"]);
			}
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'plantilla_impresion' => $printingTemplate,
	'plantilla_tickets_cambio' => $changeTicketTemplate,
	'auto_print' => $autoPrinting
);
echo json_encode(convertJson($varsSend));
?>