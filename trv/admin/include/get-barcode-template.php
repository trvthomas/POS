<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$plantillaCodigos = "";
$plantillaCodigosPDF = "";
$plantillaCodigosPDFNum = 1;

if (isset($_POST["getTemplateType"]) && isset($_POST["getTemplateShowBusiness"]) && isset($_POST["getTemplateShowTime"])) {
	$sql = "SELECT * FROM trvsol_products WHERE activo=1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		if ($_POST["getTemplateShowBusiness"] == "true") {
			$sql2 = "SELECT * FROM trvsol_configuration WHERE configName='businessName'";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				$row2 = $result2->fetch_assoc();

				$plantillaCodigos .= '<h1 class= "is-size-2 has-text-centered" style= "margin-bottom: 0">' . $row2["value"] . '</h1>';
				$plantillaCodigosPDF .= '<h1 class= "is-size-2 has-text-centered" style= "margin-bottom: 0">' . $row2["value"] . '</h1>';
			}
		} else {
			$plantillaCodigos .= '<h1 class= "is-size-2 has-text-centered" style= "margin-bottom: 0">Codes template</h1>';
			$plantillaCodigosPDF .= '<h1 class= "is-size-2 has-text-centered" style= "margin-bottom: 0">Codes template</h1>';
		}

		if ($_POST["getTemplateShowTime"] == "true") {
			$plantillaCodigos .= '<p class= "has-text-centered">Generated on: <b>' . date("d-m-Y h:i a") . '</b></p>';
			$plantillaCodigosPDF .= '<p class= "has-text-centered">Generated on: <b>' . date("d-m-Y h:i a") . '</b></p>';
		}

		$plantillaCodigos .= '<hr><div class= "columns is-multiline is-centered">';
		$plantillaCodigosPDF .= '<hr><table>';

		while ($row = $result->fetch_assoc()) {
			if ($plantillaCodigosPDFNum == 5) {
				$plantillaCodigosPDFNum = 1;
			}
			if ($plantillaCodigosPDFNum == 1) {
				$plantillaCodigosPDF .= "<tr>";
			}

			$plantillaCodigos .= '<div class= "column is-one-fifth">
	<img src= "https://barcode.tec-it.com/barcode.ashx?data=' . $row["barcode"] . '&code=Code128&dpi=500" alt= "Error generating the code" style= "width: 100%;margin-bottom: 0;">';
			$plantillaCodigosPDF .= '<td><div class= "cuadroDuplicados">
	<img src= "https://barcode.tec-it.com/barcode.ashx?data=' . $row["barcode"] . '&code=Code128&dpi=500" alt= "Error generating the code" style= "width: 100%;margin-bottom: 0;">';

			$finalPrice = '$' . number_format($row["precio"], 0, ",", ".");
			if ($row["variable_price"] == 1) {
				$finalPrice = 'Variable price';
			}

			if ($_POST["getTemplateType"] == "detailed") {
				$plantillaCodigos .= '<p class= "has-text-centered"><b>' . $row["nombre"] . '</b></p>';
				$plantillaCodigosPDF .= '<p style= "margin: 2px;"><b>' . $row["nombre"] . '</b></p>';
			} else if ($_POST["getTemplateType"] == "detailed2") {
				$plantillaCodigos .= '<p class= "has-text-centered"><b>' . $row["nombre"] . '</b>
	<br>' . $finalPrice . '</p>';
				$plantillaCodigosPDF .= '<p class= "has-text-centered"><b>' . $row["nombre"] . '</b>
	<br>' . $finalPrice . '</p>';
			}

			$plantillaCodigos .= '</div>';
			$plantillaCodigosPDF .= '</div></td>';

			++$plantillaCodigosPDFNum;
			if ($plantillaCodigosPDFNum == 5) {
				$plantillaCodigosPDF .= "</tr>";
			}
		}

		$plantillaCodigos .= '</div>
	<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';
		$plantillaCodigosPDF .= '</table>
	<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'plantilla_codigos' => $plantillaCodigos,
	'plantilla_codigos_pdf' => $plantillaCodigosPDF
);
echo json_encode(convertJson($varsSend));
?>