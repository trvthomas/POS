<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Home</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onload="countdownHideSales(); loadSales();">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox has-text-centered">
		<div class="columns is-multiline is-centered has-text-left">
			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Receipts Generated</h4>
					<h3 class="is-size-3"><i class="fas fa-receipt fa-fw"></i> <span id="statsSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless pastel-bg-green">
					<h4 class="is-size-6">Total Income</h4>
					<h3 class="is-size-3"><i class="fas fa-sack-dollar fa-fw"></i> <span id="statsSalesMoney">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless has-background-grey-lighter">
					<h4 class="is-size-6">Goal of the Day</h4>
					<h3 class="is-size-3"><i class="fas fa-bullseye fa-fw"></i> <span id="statsGoal">...</span></h3>
				</div>
			</div>
		</div>

		<div class="columns is-multiline is-centered">
			<div class="column is-one-third">
				<a href="/trv/new-invoice.php">
					<div class="box pastel-bg-green">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-receipt fa-2x"></i> New Sale (F1)</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/reports.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-pencil-alt fa-2x"></i> Reports</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/money-movement.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-money-bill-wave fa-2x"></i> Cash Adjustment</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/sales.php">
					<div class="box pastel-bg-cyan">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-coins fa-2x"></i> Day Statistics</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/price-check.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-tags fa-2x"></i> Price Check</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/verify-invoices.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-magnifying-glass fa-2x"></i> Verify Receipts</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/home.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-boxes fa-2x"></i> Inventory Dashboard</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/home.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-user-cog fa-2x"></i> Admin Dashboard</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/close-day.php">
					<div class="box pastel-bg-orange">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-moon fa-2x"></i> End Shift</h3>
					</div>
				</a>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form action="/trv/include/get-home-sales.php" method="POST" style="display: none" id="getInfoForm" onsubmit="return getInfoReturn();">
		<input name="getInfoToken" value="pos4862" readonly>
		<input id="getInfoSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function loadSales() {
			document.getElementById('getInfoSend').click();
		}

		function countdownHideSales() {
			setTimeout(hideSales, 300000);
		}

		function hideSales() {
			document.getElementById('statsSales').innerHTML = "***";
			document.getElementById('statsSalesMoney').innerHTML = "***";
		}

		function getInfoReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/get-home-sales.php',
				data: $('#getInfoForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['sales'] != "") {
						document.getElementById('statsSales').innerHTML = response['sales'];
						document.getElementById('statsSalesMoney').innerHTML = "$" + response['sales_money'];
						document.getElementById('statsGoal').innerHTML = "$" + response['sales_goal'];
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>