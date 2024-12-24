<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$nombreEmpresa = "";

$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'businessName'";
$result3 = $conn->query($sql3);

if ($result3->num_rows > 0) {
	$row3 = $result3->fetch_assoc();
	$nombreEmpresa = $row3["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Sales Receipts</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-quickview.min.css">
	<script type="text/javascript" src="/trv/include/libraries/bulma-quickview.min.js"></script>
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
</head>

<body onload="getInvoices()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Sales Receipts</h3>
		<p>View, print, or download past sales receipts</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label">Search receipts</label>
						<div class="control has-icons-left">
							<input type="text" class="input" placeholder="Search by receipt number" id="inputBuscar" onkeydown="onup()">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>
					</div>
				</div>

				<div class="column">
					<label class="label">Filter by date</label>

					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="date" class="input inputDate" id="fechaDesde">
							<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" title="Set dates" onclick="applyFilters()"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="has-text-centered"><button class="button backgroundDark" style="display: none" id="btnClearFilters" onclick="clearFilters()"><i class="fas fa-eraser"></i>Reset filters</button></div>
		</div>

		<?php if (isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])) { ?>
			<div class="notification is-warning">
				If you cancel sales, they will be shown as voided by the user <b><?php echo $_COOKIE[$prefixCoookie . "UsernameUser"]; ?></b>.
			</div>
		<?php } ?>

		<div class="box list has-visible-pointer-controls" id="invoicesList"></div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div class="quickviewOverlay" id="elementShareOverlay" onclick="document.getElementById('elementShare').classList.toggle('is-active'); document.getElementById('elementShareOverlay').classList.toggle('is-active');"></div>
	<div class="quickview" id="elementShare">
		<header class="quickview-header">
			<p class="title">Receipt details <span id="elementShareName"></span></p>
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
					<label class="label">Customer's email address</label>
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
						<button class="button is-fullwidth backgroundDark" onclick="sendMailCustomer()">Send email</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/get-invoices.php" style="display: none" id="getInvoicesForm" onsubmit="return getInvoicesReturn();">
		<input name="getInvoicesDateFrom" id="getInvoicesDateFrom" readonly>
		<input name="getInvoicesDateTo" id="getInvoicesDateTo" readonly>
		<input name="getInvoicesQuery" id="getInvoicesQuery" readonly>
		<input type="submit" id="getInvoicesSend" value="Send">
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

	<form method="POST" action="/trv/admin/include/generate-pdf-invoice.php" style="display: none">
		<input name="generatePDFIDInvoice" id="generatePDFIDInvoice" readonly>
		<input name="generatePDFNumberInvoice" id="generatePDFNumberInvoice" readonly>
		<input type="submit" id="generatePDFSend" value="Send">
	</form>

	<form action="/trv/include/mail-customer.php" method="POST" style="display: none" id="sendMailForm" onsubmit="return sendMailReturn();">
		<input name="customerBusiness" value="<?php echo $nombreEmpresa; ?>">
		<input id="customerDesign" name="customerDesign">
		<input id="customerEmail" name="customerEmail">
		<input id="sendCustomer" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var fechaDesde = '<?php echo date("Y-m-d"); ?>',
			fechaHasta = '<?php echo date("Y-m-d"); ?>';
		var invoiceTemplate = "",
			numberChangeTicketsPrint = 0;

		function getInvoices() {
			flatpickrCalendars = flatpickr(".inputDate", {
				mode: "range",
				altInput: true,
				locale: "en",
				dateFormat: "Y-m-d",
				minDate: '2021-01-01',
				maxDate: '<?php echo date("Y-m-d"); ?>',
				defaultDate: ["<?php echo date("Y-m-d"); ?>", "<?php echo date("Y-m-d"); ?>"]
			});

			document.getElementById('getInvoicesDateFrom').value = fechaDesde;
			document.getElementById('getInvoicesDateTo').value = fechaHasta;
			document.getElementById('invoicesList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';

			document.getElementById('getInvoicesSend').click();
		}

		function applyFilters() {
			var createArrayDates = document.getElementById('fechaDesde').value.split(" a ");
			var dateFrom = "",
				dateTo = "";

			if (createArrayDates != "") {
				dateFrom = createArrayDates[0];
				if (!createArrayDates[1]) {
					dateTo = dateFrom;
				} else {
					dateTo = createArrayDates[1];
				}
			}

			if (dateFrom == "" || dateTo == "") {
				newNotification("Select a date range");
			} else {
				var search = document.getElementById('inputBuscar').value;

				document.getElementById('getInvoicesQuery').value = search;
				fechaDesde = dateFrom;
				fechaHasta = dateTo;
				getInvoices();
				document.getElementById('btnClearFilters').style.display = '';
			}
		}

		function clearFilters() {
			fechaDesde = '<?php echo date("Y-m-d"); ?>';
			fechaHasta = '<?php echo date("Y-m-d"); ?>';
			document.getElementById('getInvoicesQuery').value = "";

			document.getElementById('fechaDesde').value = fechaDesde;
			document.getElementById('inputBuscar').value = "";
			getInvoices();
			document.getElementById('btnClearFilters').style.display = 'none';
		}

		function onup() {
			if (event.keyCode === 13) {
				applyFilters();
			}
		}

		function onup2() {
			if (event.keyCode === 13) {
				sendMailCustomer();
			}
		}

		function shareElement(idElement, titleElement) {
			document.getElementById('iframeElementShare').src = '/trv/admin/invoice-details.php?id=' + idElement + "&source=quickview";
			document.getElementById('elementShare').classList.toggle('is-active');
			document.getElementById('elementShareOverlay').classList.toggle('is-active');
			document.getElementById('elementShareName').innerHTML = titleElement;
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
				newNotification('An internet connection is required', 'error');
			} else {
				document.getElementById('customerDesign').value = invoiceTemplate;
				document.getElementById('customerEmail').value = email;

				document.getElementById('sendCustomer').click();
				document.getElementById('overlaySendEmail').style.display = "none";
				openLoader();
			}
		}

		function cancelSale(idInvoice) {
			var c = confirm("Are you sure? This action cannot be reversed and will subtract the sales of the corresponding day");

			if (c == true) {
				document.getElementById('cancelSaleIDInvoice').value = idInvoice;
				document.getElementById('cancelSaleSend').click();

				openLoader();
			}
		}

		function generatePDF(idInvoice, numInvoice) {
			document.getElementById('generatePDFIDInvoice').value = idInvoice;
			document.getElementById('generatePDFNumberInvoice').value = numInvoice;
			document.getElementById('generatePDFSend').click();
			newNotification('Downloading file, please wait', 'success');
		}

		function getInvoicesReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-invoices.php',
				data: $('#getInvoicesForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['comprobantes'] != "") {
						document.getElementById('invoicesList').innerHTML = response['comprobantes'];
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
						newNotification('Sale canceled', 'success');
						getInvoices();
					}
					closeLoader();
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
						newNotification('E-mail sent successfully', 'success');
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>