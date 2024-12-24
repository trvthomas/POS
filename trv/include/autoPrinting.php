<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/printing/autoload.php";

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

function autoPrintInvoice($saleId, $changeTicketsNum, $printOnlyChangeTickets)
{
	global $conn;

	$configBusinessName = "";
	$configChangeTickets = 0;
	$configChangeTicketsExpireDays = 0;
	$configPrinterName = "";
	$configHeading = "";
	$configThanksMsg = "";
	$configFooter = "";
	$configBarcode = 0;
	$configDrawer = 1;
	$configDrawerCard = 1;

	$sqlC = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName' OR configName= 'changeTickets' OR configName= 'changeTicketsExpireDays' OR configName= 'printingAutoPrinterName' OR configName= 'printingHeadingInfo' OR configName= 'printingFooterThanksMsg' OR configName= 'printingFooterInfo' OR configName= 'printingFooterBarcode' OR configName= 'printingOpenDrawer' OR configName= 'printingOpenDrawerCard'";
	$resultC = $conn->query($sqlC);

	if ($resultC->num_rows > 0) {
		while ($rowC = $resultC->fetch_assoc()) {
			if ($rowC["configName"] == "businessName") {
				$configBusinessName = $rowC["value"];
			} else if ($rowC["configName"] == "changeTickets") {
				$configChangeTickets = $rowC["value"];
			} else if ($rowC["configName"] == "changeTicketsExpireDays") {
				$configChangeTicketsExpireDays = $rowC["value"];
			} else if ($rowC["configName"] == "printingAutoPrinterName") {
				$configPrinterName = $rowC["value"];
			} else if ($rowC["configName"] == "printingHeadingInfo") {
				$configHeading = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterThanksMsg") {
				$configThanksMsg = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterInfo") {
				$configFooter = $rowC["value"];
			} else if ($rowC["configName"] == "printingFooterBarcode") {
				$configBarcode = $rowC["value"];
			} else if ($rowC["configName"] == "printingOpenDrawer") {
				$configDrawer = $rowC["value"];
			} else if ($rowC["configName"] == "printingOpenDrawerCard") {
				$configDrawerCard = $rowC["value"];
			}
		}
	}

	$sql = "SELECT * FROM trvsol_invoices WHERE id= " . $saleId;
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		try {
			$connector = new WindowsPrintConnector($configPrinterName);
			$printer = new Printer($connector);

			if ($printOnlyChangeTickets != true) {
				//Business name
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setEmphasis(true);
				$printer->setTextSize(3, 3);
				$printer->text($configBusinessName . "\n");
				$printer->setTextSize(1, 1);

				//Additional lines
				$printer->setEmphasis(false);
				$printer->text($configHeading);

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Date and time
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Date and time of purchase: ");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["fechaComplete"] . "\n");

				//Receipt #
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Receipt #");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["numero"] . "\n");

				//Attended by
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Attended by: ");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text($row["vendedor"] . "\n");

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Products
				$replacedString = str_replace("{{new_line}}", "\n", $row["productosArray_autoPrint"]);
				$decoded = json_decode($replacedString, true);
				for ($x = 0; $x < count($decoded); ++$x) {
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text($decoded[$x]["line1"]);
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text($decoded[$x]["line2"]);
				}

				//Divider
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("--------------------\n");

				//Payment method
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Payment method: " . $row["formaPago"] . "\n");

				//Values
				$calcTotal = $row["subtotal"] - $row["descuentos"];

				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_RIGHT);
				$printer->text("Subtotal: $" . number_format($row["subtotal"], 0, ",", ".") . "\n");
				$printer->text("Discounts: -$" . number_format($row["descuentos"], 0, ",", ".") . "\n");
				$printer->setEmphasis(true);
				$printer->setTextSize(2, 2);
				$printer->text("TOTAL: $" . number_format($calcTotal, 0, ",", ".") . "\n");
				$printer->setEmphasis(false);
				$printer->setTextSize(1, 1);
				$printer->text("Received: $" . $row["recibido"] . "\n");
				$printer->text("Change: $" . number_format($row["cambio"], 0, ",", ".") . "\n");

				//Notes
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->text("Additional notes: " . $row["notas"] . "\n");

				//Thank you message
				$printer->setEmphasis(true);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setTextSize(2, 2);
				$printer->text($configThanksMsg . "\n");
				$printer->setTextSize(1, 1);

				//Additional lines
				$printer->setEmphasis(false);
				$printer->text($configFooter);

				//Barcode
				if ($configBarcode == 1) {
					$printer->barcode($row["numero"]);
					$printer->text("\n");
					$printer->setEmphasis(true);
					$printer->text($row["numero"]);
				}

				//TRV SOLUTIONS
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("\n--------------------");
				$printer->setEmphasis(false);
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("\nSoftware by TRV Solutions (" . date("Y") . ")\nwww.trvsolutions.com");

				$printer->feed(3);
				$printer->cut(Printer::CUT_PARTIAL);
			}

			//Change tickets
			if ($configChangeTickets == 1 && $changeTicketsNum > 0 && $changeTicketsNum <= 5) {
				for ($x2 = 0; $x2 < $changeTicketsNum; ++$x2) {
					//Business name
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setEmphasis(true);
					$printer->setTextSize(1, 1);
					$printer->text($configBusinessName . "\n");
					$printer->setTextSize(1, 2);
					$printer->text("Gift ticket\n");
					$printer->setTextSize(1, 1);

					//Divider
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->text("--------------------\n");

					//Date and time
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text("Date and time of purchase: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text($row["fechaComplete"] . "\n");

					//Attended by
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->text("Attended by: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text($row["vendedor"] . "\n");

					//Expiration date
					$printer->setEmphasis(true);
					$printer->setJustification(Printer::JUSTIFY_LEFT);
					$printer->setTextSize(2, 2);
					$printer->text("Valid until: ");
					$printer->setEmphasis(false);
					$printer->setJustification(Printer::JUSTIFY_RIGHT);
					$printer->text(date("d-m-Y h:i a", strtotime($row["fechaComplete"] . " +" . $configChangeTicketsExpireDays . " days")) . "\n");
					$printer->setTextSize(1, 1);

					//Divider
					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->text("--------------------\n");

					//Barcode
					$printer->barcode($row["numero"]);
					$printer->text("\n");
					$printer->setEmphasis(true);
					$printer->text($row["numero"]);

					$printer->feed(3);
					$printer->cut(Printer::CUT_PARTIAL);
				}
			}

			if ($configDrawer == 1) {
				if ($configDrawerCard == 1 || ($configDrawerCard == 0 && $row["formaPago"] != "Card")) {
					$printer->pulse();
				}
			}
		} catch (Exception $e) {
			$errorPrint = true;
		} finally {
			$printer->close();
		}
	}
}
?>