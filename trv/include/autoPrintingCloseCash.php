<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/printing/autoload.php";

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$existeError = false;
$comprobanteImpreso = false;

if (isset($_POST["autoPrintDate"]) && isset($_POST["autoPrintMonth"]) && isset($_POST["autoPrintYear"])) {
	$configBusinessName = "";
	$configPrinterName = "";

	$sqlC = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName' OR configName= 'printingAutoPrinterName'";
	$resultC = $conn->query($sqlC);

	if ($resultC->num_rows > 0) {
		while ($rowC = $resultC->fetch_assoc()) {
			if ($rowC["configName"] == "businessName") {
				$configBusinessName = $rowC["value"];
			} else if ($rowC["configName"] == "printingAutoPrinterName") {
				$configPrinterName = $rowC["value"];
			}
		}
	}

	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . $_POST["autoPrintMonth"] . " AND year=" . $_POST["autoPrintYear"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);

		for ($x = 0; $x < count($decoded); ++$x) {
			if (date('Y-m-d', strtotime($decoded[$x]["date"])) == $_POST["autoPrintDate"]) {
				$sql4 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
				$result4 = $conn->query($sql4);

				if ($result4->num_rows > 0) {
					$row4 = $result4->fetch_assoc();

					try {
						$connector = new WindowsPrintConnector($configPrinterName);
						$printer = new Printer($connector);

						//Business name
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->setEmphasis(true);
						$printer->setTextSize(1, 1);
						$printer->text($configBusinessName . "\n");
						$printer->setTextSize(1, 2);
						$printer->text("Cierre de caja\n");
						$printer->setTextSize(1, 1);

						//Divider
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("--------------------\n");

						//Date and time of entry
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Date and time of entry: ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text($decoded[$x]["entryDate"] . "\n");

						//Date and time of closure
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Date and time of closure: ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text($decoded[$x]["closedDate"] . "\n");

						//Seller(s)
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Seller(s): ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text($decoded[$x]["seller"] . "\n");

						//Divider
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("--------------------\n");

						//Initial cash
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Initial cash: ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text("$" . $decoded[$x]["initialCash"] . "\n");

						//Sales made
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text(number_format($decoded[$x]["numberSales"], 0, ",", ".") . " sales made\n");
						$printer->setEmphasis(false);

						//Divider
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("--------------------\n");

						//Values
						$totalSales = $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];

						//Cash
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Cash sales: ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text("$" . number_format($decoded[$x]["cashSales"], 0, ",", ".") . "\n");

						//Card
						$printer->setEmphasis(true);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("Card sales: ");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text("$" . number_format($decoded[$x]["cardSales"], 0, ",", ".") . "\n");

						//Other payment method
						if ($row4["value"] != "") {
							$printer->setEmphasis(true);
							$printer->setJustification(Printer::JUSTIFY_LEFT);
							$printer->text($row4["value"] . " sales: ");
							$printer->setEmphasis(false);
							$printer->setJustification(Printer::JUSTIFY_RIGHT);
							$printer->text("$" . number_format($decoded[$x]["otherSales"], 0, ",", ".") . "\n");
						}

						//Total income
						$printer->setEmphasis(true);
						$printer->setTextSize(2, 2);
						$printer->setJustification(Printer::JUSTIFY_LEFT);
						$printer->text("TOTAL INCOME: ");
						$printer->setJustification(Printer::JUSTIFY_RIGHT);
						$printer->text("$" . number_format($totalSales, 0, ",", ".") . "\n");
						$printer->setEmphasis(false);
						$printer->setTextSize(1, 1);

						//Reports
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("Reports are available only in digital formats");

						//TRV SOLUTIONS
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("\n--------------------");
						$printer->setEmphasis(false);
						$printer->setJustification(Printer::JUSTIFY_CENTER);
						$printer->text("\nSoftware by TRV Solutions (" . date("Y") . ")\nwww.trvsolutions.com");

						$printer->feed(3);
						$printer->cut(Printer::CUT_PARTIAL);

						$printer->pulse();

						$comprobanteImpreso = true;
					} catch (Exception $e) {
						$existeError = true;
					} finally {
						$printer->close();
					}
				}

				break;
			}
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'impreso' => $comprobanteImpreso
);
echo json_encode(convertJson($varsSend));
?>