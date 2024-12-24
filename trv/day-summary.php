<?php include_once "include/DBData.php";
include_once "include/stats.php";
if (isset($_COOKIE[$prefixCoookie . "IdUser"]) || isset($_COOKIE[$prefixCoookie . "UsernameUser"])) {
	header("Location:home.php");
}

$printingTemplate = "";
$adminEmail = "";
$onloadActions = "";
$cloudServiceActive = 0;
$autoPrinting = false;

$autoPrintingDate = "";
$autoPrintingMonth = "";
$autoPrintingYear = "";

$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date('m', strtotime($_GET["day"])) . " AND year=" . date('Y', strtotime($_GET["day"]));
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	$decoded = json_decode($row["estadisticas"], true);

	for ($x = 0; $x < count($decoded); ++$x) {
		if (date('Y-m-d', strtotime($decoded[$x]["date"])) == date('Y-m-d', strtotime($_GET["day"]))) {
			$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'templateDayReport'";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				$row2 = $result2->fetch_assoc();

				$sql4 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
				$result4 = $conn->query($sql4);

				if ($result4->num_rows > 0) {
					$row4 = $result4->fetch_assoc();

					$autoPrintingDate = date('Y-m-d', strtotime($_GET["day"]));
					$autoPrintingMonth = date('m', strtotime($_GET["day"]));
					$autoPrintingYear = date('Y', strtotime($_GET["day"]));

					$totalSales = $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];

					$find =    array("{{trv_daysumm_enter}}", "{{trv_daysumm_exit}}", "{{trv_daysumm_seller}}", "{{trv_daysumm_cash_base}}", "{{trv_daysumm_number_sales}}", "{{trv_daysumm_cash_sales}}", "{{trv_daysumm_card_sales}}", "{{trv_daysumm_other_name}}", "{{trv_daysumm_other_sales}}", "{{trv_daysumm_total_sales}}", "{{trv_daysumm_reports}}");
					$replace = array($decoded[$x]["entryDate"], $decoded[$x]["closedDate"], $decoded[$x]["seller"], $decoded[$x]["initialCash"], number_format($decoded[$x]["numberSales"], 0, ",", "."), number_format($decoded[$x]["cashSales"], 0, ",", "."), number_format($decoded[$x]["cardSales"], 0, ",", "."), $row4["value"], number_format($decoded[$x]["otherSales"], 0, ",", "."), number_format($totalSales, 0, ",", "."), $decoded[$x]["reports"]);

					$printingTemplate = str_replace($find, $replace, $row2["value"]);
					$printingTemplate .= '<div style= "text-align: center">---------- ----------
	<p style= "font-size: 14px;">Software by TRV Solutions (' . date("Y") . ').
	<br><b>www.trvsolutions.com</b></p>
	</div>';

					$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'adminEmail' OR configName= 'sendAutoReports' OR configName= 'lowStockNotification' OR configName= 'trvCloudActive' OR configName= 'printingAuto'";
					$result3 = $conn->query($sql3);

					if ($result3->num_rows > 0) {
						while ($row3 = $result3->fetch_assoc()) {
							if ($row3["configName"] == "adminEmail") {
								$adminEmail = $row3["value"];
							} else if ($row3["configName"] == "sendAutoReports" && $row3["value"] == 1) {
								$onloadActions .= "sendReportEmail();";
							} else if ($row3["configName"] == "lowStockNotification" && $row3["value"] == 1) {
								$onloadActions .= "sendLowStockNotifications();";
							} else if ($row3["configName"] == "trvCloudActive") {
								$cloudServiceActive = $row3["value"];
							} else if ($row3["configName"] == "printingAuto") {
								$autoPrinting = $row3["value"];
							}
						}
					}
				}
			}

			break;
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Day summary</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body <?php if ($onloadActions != "") {
			echo 'onload= "' . $onloadActions . '"';
		} ?>>
	<?php include_once "include/header-login.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Day summary</h3>
		<p>View the summary of the sales made during the day</p>

		<div class="box">
			<div class="columns">
				<div class="column">
					<div id="printSummaryDiv" class="invoiceStyle content"><?php echo $printingTemplate; ?></div>
				</div>

				<div class="column">
					<div class="columns is-multiline is-centered has-text-centered">
						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-success-light" onclick="printSummary()">
								<span class="icon is-large"><i class="fas fa-print fa-2x"></i></span>
								<p><b>Print summary</b></p>
							</div>
						</div>

						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-warning-light" onclick="sendReportEmail()">
								<span class="icon is-large"><i class="fas fa-paper-plane fa-2x"></i></span>
								<p><b>Send report</b></p>
							</div>
						</div>

						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-info-light" onclick="newShift()">
								<span class="icon is-large"><i class="fas fa-circle-plus fa-2x"></i></span>
								<p><b>New shift</b></p>
							</div>
						</div>
					</div>

					<p>Below is the summary of sales for this shift. You can perform the <b>following actions</b>:
						<br><b>"Print summary":</b> Prints a ticket with the sales summary.
						<br><b>"Send report":</b> Send the report to the administrator's e-mail.
						<br><b>"New shift":</b> Return to the login tab to open a new shift.
					</p>
				</div>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form action="/trv/include/mail-close-cash.php" method="POST" style="display: none" id="sendMailForm" onsubmit="return sendMailReturn();">
		<input name="sendDaySummaryEmail" id="sendDaySummaryEmail" value="<?php echo $adminEmail; ?>">
		<input name="sendDaySummaryDesign" value='<?php echo $printingTemplate; ?>'>
		<input id="sendDaySummarySend" type="submit" value="Send">
	</form>

	<form action="/trv/include/mail-low-stock.php" method="POST" style="display: none" id="sendMailLowStockForm" onsubmit="return sendMailLowStockReturn();">
		<input name="sendMailLowStockEmail" id="sendMailLowStockEmail" value="<?php echo $adminEmail; ?>">
		<input id="sendMailLowStockSend" type="submit" value="Send">
	</form>

	<form action="/trv/include/autoPrintingCloseCash.php" method="POST" style="display: none" id="autoPrintForm" onsubmit="return autoPrintReturn();">
		<input name="autoPrintDate" id="autoPrintDate" value="<?php echo $autoPrintingDate; ?>">
		<input name="autoPrintMonth" id="autoPrintMonth" value="<?php echo $autoPrintingMonth; ?>">
		<input name="autoPrintYear" id="autoPrintYear" value="<?php echo $autoPrintingYear; ?>">
		<input id="autoPrintSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/cloudService.js"></script>
	<script>
		var timeoutHideOverlay;
		<?php if ($cloudServiceActive == 1) { ?>updateCloudInfo(0, 0);
		<?php } ?>

		function printSummary() {
			if (<?php echo $autoPrinting; ?> == 1) {
				document.getElementById('autoPrintSend').click();
				openLoader();
			} else {
				var restorePage = document.body.innerHTML;
				var printContent = document.getElementById("printSummaryDiv").innerHTML;
				document.body.innerHTML = printContent;
				window.print();
				document.body.innerHTML = restorePage;
			}
		}

		function sendReportEmail() {
			var emailAdmin = document.getElementById('sendDaySummaryEmail').value;

			if (emailAdmin == "") {
				newNotification('E-mail not configured', 'error');
			} else if (navigator.onLine == false) {
				newNotification('Internet connection required', 'error');
			} else {
				document.getElementById('sendDaySummarySend').click();
				openLoader();

				timeoutHideOverlay = setTimeout(function() {
					closeLoader();
				}, 10000);
			}
		}

		function sendLowStockNotifications() {
			var emailAdmin = document.getElementById('sendMailLowStockEmail').value;

			if (emailAdmin != "" && navigator.onLine == true) {
				document.getElementById('sendMailLowStockSend').click();
			}
		}

		function newShift() {
			var c = confirm("Remember to print or send the day's report. Click 'OK' to open a new shift.");
			if (c == true) {
				window.location = "/trv/home.php";
			}
		}

		function sendMailReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/mail-close-cash.php',
				data: $('#sendMailForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error sending the e-mail', 'error');
					} else if (response['email_enviado'] == true) {
						newNotification('E-mail sent', 'success');
					}
					closeLoader();
					clearTimeout(timeoutHideOverlay);
				}
			});

			return false;
		}

		function sendMailLowStockReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/mail-low-stock.php',
				data: $('#sendMailLowStockForm').serialize(),
				dataType: 'json',
				success: function(response) {
					console.log(response['errores'] + " " + response['email_enviado']);
				}
			});

			return false;
		}

		function autoPrintReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/autoPrintingCloseCash.php',
				data: $('#autoPrintForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores']) {
						newNotification('There was an error', 'error');
					} else if (response['impreso']) {
						newNotification('Printed', 'success');
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>