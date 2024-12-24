<?php include_once "include/verifySession.php";

$nombreEmpresa = "";
$metodoPagoPersonalizado = "";
$cloudServiceActive = 0;

$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName' OR configName= 'newPaymentMethod' OR configName= 'trvCloudActive'";
$result3 = $conn->query($sql3);

if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		if ($row3["configName"] == "businessName") {
			$nombreEmpresa = $row3["value"];
		} else if ($row3["configName"] == "newPaymentMethod") {
			$metodoPagoPersonalizado = $row3["value"];
		} else if ($row3["configName"] == "trvCloudActive") {
			$cloudServiceActive = $row3["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Day Sales Receipts</title>

	<?php include_once "include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-quickview.min.css">
	<script type="text/javascript" src="/trv/include/libraries/bulma-quickview.min.js"></script>
</head>

<body onload="getSalesList(true)">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Day sales receipts</h3>
		<p>View all sales made during the day</p>

		<div class="box">
			<div class="buttons">
				<a class="button is-small" href="/trv/sales.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
				<button class="button is-small" onclick="toggleFilters()"><span class="icon is-small"><i class="fas fa-filter"></i></span></button>
			</div>
			<nav class="panel filtersBox is-hidden" id="filtersPanel"><button class="button is-loading is-static is-large">Loading...</button></nav>

			<div class="list has-visible-pointer-controls" id="salesList"></div>

			<nav class="pagination is-hidden" id="paginationPanel"></nav>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<div class="quickviewOverlay" id="elementShareOverlay" onclick="document.getElementById('elementShare').classList.toggle('is-active'); document.getElementById('elementShareOverlay').classList.toggle('is-active');"></div>
	<div class="quickview" id="elementShare">
		<header class="quickview-header">
			<p class="title">Sales details <span id="elementShareName"></span></p>
			<span class="delete" onclick="document.getElementById('elementShare').classList.toggle('is-active'); document.getElementById('elementShareOverlay').classList.toggle('is-active');"></span>
		</header>

		<div class="quickview-body">
			<div class="quickview-block" style="width: 100%;height: 100%;overflow: hidden;">
				<iframe style="width: 100%; height: 100%;" id="iframeElementShare"></iframe>
			</div>
		</div>
	</div>

	<div id="overlaySendEmail" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlaySendEmail').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Send receipt by e-mail</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<label class="label">Customer e-mail</label>
					<div class="control has-icons-left">
						<input type="email" class="input" placeholder="e.g. jhondoe@gmail.com" id="inputEmailCliente" onkeyup="onup()">
						<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlaySendEmail').style.display='none'">Cancel</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="sendMailCustomer()">Send e-mail</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/include/get-day-invoices.php" style="display: none" id="getDayInvoicesForm" onsubmit="return getDayInvoicesReturn();">
		<input name="getDayInvoicesPayment" id="getDayInvoicesPayment" value="0" readonly>
		<input name="getDayInvoicesSearch" id="getDayInvoicesSearch" readonly>
		<input type="submit" id="getDayInvoicesSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/generate-sale-template.php" style="display: none" id="generateTemplateForm" onsubmit="return generateTemplateReturn();">
		<input name="generateTemplateIDInvoice" id="generateTemplateIDInvoice" readonly>
		<input name="generateTemplatePrintOrSend" id="generateTemplatePrintOrSend" readonly>
		<input name="generateTemplateAutoChangeTickets" id="generateTemplateAutoChangeTickets" readonly>
		<input type="submit" id="generateTemplateSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/cancel-sale.php" style="display: none" id="cancelSaleForm" onsubmit="return cancelSaleReturn();">
		<input name="cancelSaleIDInvoice" id="cancelSaleIDInvoice" readonly>
		<input type="submit" id="cancelSaleSend" value="Send">
	</form>

	<form action="/trv/include/mail-customer.php" method="POST" style="display: none" id="sendMailForm" onsubmit="return sendMailReturn();">
		<input name="customerBusiness" value="<?php echo $nombreEmpresa; ?>">
		<input id="customerDesign" name="customerDesign">
		<input id="customerEmail" name="customerEmail">
		<input id="sendCustomer" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/filters-pagination.js"></script>
	<script src="/trv/include/cloudService.js"></script>
	<script>
		var invoiceTemplate = "";
		var numberChangeTicketsPrint = 0;

		function getSalesList(updateFilters) {
			if (updateFilters == true) {
				createFiltersBox(true, 'Search by receipt number or subtotal value', JSON.stringify([{
						icon: "coins",
						title: "Cash sales",
						actionCode: "Efectivo"
					},
					{
						icon: "credit-card",
						title: "Card sales",
						actionCode: "Tarjeta"
					},
					{
						icon: "money-check-alt",
						title: "Multipayment sales",
						actionCode: "Multipago"
					},
					<?php if ($metodoPagoPersonalizado != "") { ?> {
							icon: "wallet",
							title: "<?php echo $metodoPagoPersonalizado; ?> sales",
							actionCode: "<?php echo $metodoPagoPersonalizado; ?>"
						},
					<?php } ?> {
						icon: "ban",
						title: "Canceled sales",
						actionCode: "C"
					}
				]), false);

				onloadSetFilters("<?php if (isset($_GET['search'])) { echo $_GET['search']; } ?>", "<?php if (isset($_GET['filter1'])) { echo $_GET['filter1']; } ?>", "<?php if (isset($_GET['filter2'])) { echo $_GET['filter2']; } ?>", "<?php if (isset($_GET['page'])) { echo $_GET['page']; } else { echo "1"; } ?>");
			}

			document.getElementById('salesList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getDayInvoicesSend').click()
		}

		function onpageSetFilter(idFilter) {
			document.getElementById('getDayInvoicesPayment').value = idFilter;
			getSalesList(false);
		}

		function onpageSearchFilter(searchMade) {
			document.getElementById('getDayInvoicesSearch').value = searchMade;
			getSalesList(false);
		}

		function onpageResetFilters() {
			document.getElementById('getDayInvoicesPayment').value = "0";
			document.getElementById('getDayInvoicesSearch').value = "";
			getSalesList(false);
		}

		function onpageNextPage(actualPage) {}

		function onup() {
			if (event.keyCode === 13) {
				sendMailCustomer();
			}
		}

		function sendEmail(idInvoice) {
			openLoader();

			document.getElementById('generateTemplateIDInvoice').value = idInvoice;
			document.getElementById('generateTemplatePrintOrSend').value = "S";
			document.getElementById('generateTemplateSend').click();
		}

		function sendMailCustomer() {
			var email = document.getElementById('inputEmailCliente').value;
			if (email == "" || email.includes("@") == false || email.includes(".") == false) {
				newNotification('Enter a valid e-mail', 'error');
			} else if (navigator.onLine == false) {
				newNotification('Internet connection required', 'error');
			} else {
				document.getElementById('customerDesign').value = invoiceTemplate;
				document.getElementById('customerEmail').value = email;

				document.getElementById('sendCustomer').click();
				document.getElementById('overlaySendEmail').style.display = "none";
				openLoader();
			}
		}

		function cancelSale(idInvoice) {
			var c = confirm("Are you sure? This action cannot be reversed");

			if (c == true) {
				document.getElementById('cancelSaleIDInvoice').value = idInvoice;
				document.getElementById('cancelSaleSend').click();

				openLoader();
			}
		}

		function shareElement(idElement, titleElement) {
			document.getElementById('iframeElementShare').src = '/trv/invoice-details.php?id=' + idElement + "&source=quickview";
			document.getElementById('elementShare').classList.toggle('is-active');
			document.getElementById('elementShareOverlay').classList.toggle('is-active');
			document.getElementById('elementShareName').innerHTML = titleElement;
		}

		function getDayInvoicesReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/get-day-invoices.php',
				data: $('#getDayInvoicesForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['comprobantes'] != "") {
						document.getElementById('salesList').innerHTML = response['comprobantes'];
					}
				}
			});

			return false;
		}

		function generateTemplateReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/generate-sale-template.php',
				data: $('#generateTemplateForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');

						closeLoader();
					} else if (response['plantilla_impresion'] != "") {
						invoiceTemplate = response['plantilla_impresion'];

						document.getElementById('overlaySendEmail').style.display = "block";
						closeLoader();
					}
				}
			});

			return false;
		}

		function cancelSaleReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/cancel-sale.php',
				data: $('#cancelSaleForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');

						closeLoader();
					} else if (response['venta_cancelada'] == true) {
						<?php if ($cloudServiceActive == 1) { ?>updateCloudInfo(1, 0);
					<?php } ?>
					newNotification('Sale canceled', 'success');
					window.location = "/trv/day-invoices.php";
					}
				}
			});

			return false;
		}

		function sendMailReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/mail-customer.php',
				data: $('#sendMailForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error sending the e-mail', 'error');
					} else if (response['email_enviado'] == true) {
						newNotification('E-mail sent', 'success');
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>