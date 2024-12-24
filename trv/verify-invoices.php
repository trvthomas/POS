<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Verify Receipts</title>

	<?php include_once "include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-quickview.min.css">
	<script type="text/javascript" src="/trv/include/libraries/bulma-quickview.min.js"></script>
</head>

<body onload="getSalesList(true)">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox loginBox">
		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<h3 class="is-size-5 has-text-centered">Verify Receipts</h3>
			<p class="has-text-centered">Verify the products, payment method, and other information of past receipts using the receipt number</p>
			<hr>

			<div class="field">
				<label class="label">Receipt number</label>
				<div class="control has-icons-left">
					<input type="text" class="input" placeholder="e.g. 1200, PRE1200" id="numberSale" onkeyup="onup()">
					<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
				</div>
			</div>

			<div class="field">
				<div class="control"><button class="button backgroundDark is-fullwidth" onclick="checkInvoice()"><i class="fas fa-circle-check"></i> Check information</button></div>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<div class="quickviewOverlay" id="elementShareOverlay" onclick="document.getElementById('elementShare').classList.toggle('is-active'); document.getElementById('elementShareOverlay').classList.toggle('is-active');"></div>
	<div class="quickview" id="elementShare">
		<header class="quickview-header">
			<p class="title">Details receipt <span id="elementShareName"></span></p>
			<span class="delete" onclick="document.getElementById('elementShare').classList.toggle('is-active'); document.getElementById('elementShareOverlay').classList.toggle('is-active');"></span>
		</header>

		<div class="quickview-body">
			<div class="quickview-block" style="width: 100%;height: 100%;overflow: hidden;">
				<iframe style="width: 100%; height: 100%;" id="iframeElementShare"></iframe>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/include/verify-invoice.php" style="display: none" id="verifyInvoiceForm" onsubmit="return verifyInvoiceReturn();">
		<input name="verifyInvoiceSaleNumber" id="verifyInvoiceSaleNumber" readonly>
		<input type="submit" id="verifyInvoiceSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function onup() {
			if (event.keyCode === 13) {
				checkInvoice();
			}
		}

		function checkInvoice() {
			var numberSales = document.getElementById('numberSale').value;

			if (numberSale == "") {
				newNotification('Enter the receipt number', 'error');
			} else {
				document.getElementById('verifyInvoiceSaleNumber').value = numberSales;
				document.getElementById('verifyInvoiceSend').click();
				openLoader();
			}
		}

		function shareElement(idElement, titleElement) {
			document.getElementById('iframeElementShare').src = '/trv/invoice-details.php?id=' + idElement + "&source=quickview";
			document.getElementById('elementShare').classList.toggle('is-active');
			document.getElementById('elementShareOverlay').classList.toggle('is-active');
			document.getElementById('elementShareName').innerHTML = titleElement;
		}

		function verifyInvoiceReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/verify-invoice.php',
				data: $('#verifyInvoiceForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['comprobante_no_existe'] == true) {
						newNotification('This receipt does not exist, check the number', 'error');
					} else if (response['id_venta'] != "") {
						shareElement(response['id_venta'], response['numero_venta']);
					}
					closeLoader();
					document.getElementById('numberSale').value = "";
				}
			});

			return false;
		}
	</script>
</body>

</html>