<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

use Dompdf\Dompdf;

require_once('dompdf/autoload.inc.php');

$existeError = false;
$pdfDescargado = false;
$contentFinal = "";

if (isset($_POST["generatePDFIDInvoice"]) && isset($_POST["generatePDFNumberInvoice"])) {
	$sql = "SELECT * FROM trvsol_invoices WHERE id=" . $_POST["generatePDFIDInvoice"] . " AND cancelada=0";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice'";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_assoc();

			$printingTemplate = "";
			$totalVenta = $row["subtotal"] - $row["descuentos"];

			$find =    array("{{trv_date_purchase}}", "{{trv_num_invoice}}", "{{trv_seller}}", "{{trv_products}}", "{{trv_payment_method}}", "{{trv_subtotal}}", "{{trv_discount}}", "{{trv_total}}", "{{trv_change_received}}", "{{trv_change}}", "{{trv_notes}}");
			$replace = array($row["fechaComplete"], $row["numero"], $row["vendedor"], $row["productos"], $row["formaPago"], number_format($row["subtotal"], 0, ",", "."), number_format($row["descuentos"], 0, ",", "."), number_format($totalVenta, 0, ",", "."), $row["recibido"], number_format($row["cambio"], 0, ",", "."), $row["notas"]);

			$printingTemplate = str_replace($find, $replace, $row2["value"]);
			$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

			$contentFinal = '<!DOCTYPE html>
	<html>
	<head>
	<style>
		body{
		background-color: #fff;
		color: #19191a;
		font-family: Helvetica;
		font-size: 20px;
		cursor: auto;
		margin: 0;
		}
		
		.pdfClassProd{ position: relative; }
		.prodListPrice{
		position: absolute;
		right: 0;
		}
	</style>
	</head>
 
	<body>
	' . $printingTemplate . '
	</body>
	</html>';
		}
	} else {
		$existeError = true;
	}

	$dompdf = new Dompdf();
	$dompdf->loadHtml($contentFinal);

	$dompdf->setPaper(array(0, 0, 453.543, 1000), 'portrait');
	$dompdf->render();
	if ($dompdf->stream("Sales Receipt " . $_POST["generatePDFNumberInvoice"] . ".pdf", array("Attachment" => 1))) {
		$pdfDescargado = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

if ($existeError == true) {
	echo "There was an error generating the PDF<br><a href= '/trv/home.php'>Return to home</a>";
}
?>