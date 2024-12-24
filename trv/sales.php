<?php include_once "include/verifySession.php";

$metodoPagoPersonalizado = "";

$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
$result2 = $conn->query($sql2);
if ($result2->num_rows > 0) {
	$row2 = $result2->fetch_assoc();

	$metodoPagoPersonalizado = $row2["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Day Statistics</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onload="countdownHideSales(); loadSales();">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Day Statistics</h3>
				<p>Check today's sales divided by payment method</p>
			</div>

			<div class="column is-one-third">
				<a class="button backgroundDark is-fullwidth" href="/trv/day-invoices.php"><i class="fas fa-file-invoice-dollar"></i> Day Sales Receipts</a>
			</div>
		</div>

		<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

		<div class="columns is-multiline is-centered has-text-left">
			<div class="column is-one-third">
				<div class="box is-shadowless pastel-bg-green">
					<h4 class="is-size-6">Total income</h4>
					<h3 class="is-size-3"><i class="fas fa-sack-dollar fa-fw"></i> <span id="boxSales2">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Cash Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-coins fa-fw"></i> <span id="boxSales3">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Card Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-credit-card fa-fw"></i> <span id="boxSales4">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third" <?php if ($metodoPagoPersonalizado == "") {
													echo 'style= "display: none"';
												} ?>>
				<div class="box is-shadowless">
					<h4 class="is-size-6"><?php echo $metodoPagoPersonalizado; ?> Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-wallet fa-fw"></i> <span id="boxSales7">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Receipts Generated</h4>
					<h3 class="is-size-3"><i class="fas fa-receipt fa-fw"></i> <span id="boxSales1">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Initial Cash</h4>
					<h3 class="is-size-3"><i class="fas fa-cash-register fa-fw"></i> <span id="boxSales5">...</span></h3>
				</div>
			</div>

			<div class="column is-full">
				<div class="box is-shadowless has-background-grey-lighter">
					<h4 class="is-size-6">Goal of the Day</h4>
					<h3 class="is-size-3"><i class="fas fa-bullseye fa-fw"></i> <span id="boxSales6">...</span></h3>
				</div>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form action="/trv/include/get-all-sales.php" method="POST" style="display: none" id="getInfoForm" onsubmit="return getInfoReturn();">
		<input name="getInfoToken" value="pos4862" readonly>
		<input id="getInfoSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var numberBoxes = <?php if ($metodoPagoPersonalizado != "") {
								echo "7";
							} else {
								echo "6";
							} ?>;
		var completeInitialCash = "Error";

		function countdownHideSales() {
			setTimeout(hideSales, 300000);
		}

		function hideSales() {
			for (var i = 1; i <= numberBoxes; i++) {
				document.getElementById('boxSales' + i).innerHTML = "***";
			}
		}

		function showAllBase() {
			document.getElementById('showAllBaseBtn').style.display = 'none';
			document.getElementById('boxSales5').innerHTML = completeInitialCash;
			document.getElementById('boxSales5').style.fontSize = '20px';
		}

		function loadSales() {
			document.getElementById('getInfoSend').click();
		}

		function getInfoReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/get-all-sales.php',
				data: $('#getInfoForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['sales'] != "") {
						completeInitialCash = response['sales_initial_complete'];

						document.getElementById('boxSales1').innerHTML = response['sales'];
						document.getElementById('boxSales2').innerHTML = "$" + response['sales_money'];
						document.getElementById('boxSales3').innerHTML = "$" + response['sales_cash'];
						document.getElementById('boxSales4').innerHTML = "$" + response['sales_card'];
						document.getElementById('boxSales5').innerHTML = "$" + response['sales_initial'];
						document.getElementById('boxSales6').innerHTML = "$" + response['sales_goal'];
						<?php if ($metodoPagoPersonalizado != "") { ?>
							document.getElementById('boxSales7').innerHTML = "$" + response['sales_other'];
						<?php } ?>
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>