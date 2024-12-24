<?php include_once "include/verifySession.php";

$ticketsCambio = 0;

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTickets'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$ticketsCambio = $row["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Home - Admin Dashboard</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts-more.js"></script>
	<script src="/trv/include/libraries/graphs/code/highcharts-3d.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/exporting.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/data.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/export-data.js"></script>
	<script src="/trv/include/libraries/graphs/code/modules/series-label.js"></script>
	<script src="/trv/include/graph-options.js"></script>
</head>

<body onload="loadStats()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox has-text-centered">
		<div class="columns is-multiline is-centered has-text-left">
			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Monthly Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-receipt fa-fw"></i> <span id="statsSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless pastel-bg-green">
					<h4 class="is-size-6">Monthly Income</h4>
					<h3 class="is-size-3"><i class="fas fa-sack-dollar fa-fw"></i> <span id="statsSalesMoney">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Daily Average</h4>
					<h3 class="is-size-3"><i class="fas fa-magnifying-glass-chart fa-fw"></i> <span id="statsAverage">...</span></h3>
				</div>
			</div>
		</div>

		<div class="columns has-text-centered">
			<div class="column">
				<div class="box" id="statsSalesChart"></div>
				<table class="is-hidden" id="statsChartTable"></table>
			</div>
			<div class="column">
				<div class="box">
					<h3 class="is-size-5">Top Selling Products</h3>
					<hr>

					<div id="statsProducts" class="list has-visible-pointer-controls has-text-left"></div>
				</div>
			</div>
		</div>

		<div class="columns is-multiline is-centered">
			<div class="column is-one-third">
				<a href="/trv/admin/statistics.php">
					<div class="box pastel-bg-orange">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-chart-bar fa-2x"></i> Statistics</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/configuration.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-gears fa-2x"></i> Settings</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/invoices-design.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-brush fa-2x"></i> Invoice Design</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/products.php">
					<div class="box pastel-bg-bluepurple">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-tshirt fa-2x"></i> Products</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/barcode-creator.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-barcode fa-2x"></i> Barcode Generator</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/vouchers.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-tag fa-2x"></i> Coupons</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/home.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-boxes fa-2x"></i> Inventory</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/users.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-users fa-2x"></i> Sellers</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/admin/invoices.php">
					<div class="box pastel-bg-yellow">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-file-invoice-dollar fa-2x"></i> Sales Receipts</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<div class="box is-clickable" onclick="document.getElementById('overlayDaySummaries').style.display= 'block';">
					<h3 class="is-size-5"><i class="is-pulled-left fas fa-moon fa-2x"></i> Past Shifts</h3>
				</div>
			</div>

			<?php if ($ticketsCambio == 1) { ?>
				<div class="column is-one-third">
					<a href="/trv/admin/change-tickets.php">
						<div class="box">
							<h3 class="is-size-5"><i class="is-pulled-left fas fa-right-left fa-2x"></i> Gift Tickets Configuration</h3>
						</div>
					</a>
				</div>
			<?php } ?>

			<div class="column is-one-third">
				<a href="/trv/admin/backups.php">
					<div class="box">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-arrows-rotate fa-2x"></i> Backups</h3>
					</div>
				</a>
			</div>

			<?php if (isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])) { ?>
				<div class="column is-one-third">
					<a href="/trv/admin/logout-temporary.php">
						<div class="box">
							<h3 class="is-size-5"><i class="is-pulled-left fas fa-right-from-bracket fa-2x"></i> Log out</h3>
						</div>
					</a>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayDaySummaries" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayDaySummaries').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Select a date to view the report</h3>
			</div>

			<div class="trvModal-elements">
				<div class="field">
					<div class="control has-icons-left">
						<input type="date" class="input" id="fechaReporteCierre">
						<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayDaySummaries').style.display='none'">Cancel</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="goToDaySummary()"><i class="fas fa-circle-check"></i> Accept</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form action="/trv/admin/include/get-home-sales.php" method="POST" style="display: none" id="getInfoForm" onsubmit="return getInfoReturn();">
		<input name="getInfoToken" value="pos4862" readonly>
		<input id="getInfoSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function loadStats() {
			flatpickrCalendars = flatpickr("#fechaReporteCierre", {
				altInput: true,
				locale: "en",
				dateFormat: "Y-m-d",
				minDate: '2021-01-01',
				maxDate: '<?php echo date("Y-m-d", strtotime("-1 day")); ?>'
			});

			document.getElementById('getInfoSend').click();
		}

		function goToDaySummary() {
			var dateSelected = document.getElementById('fechaReporteCierre').value;

			if (dateSelected == "") {
				newNotification('Select a date', 'error');
			} else {
				openLoader();
				document.getElementById('overlayDaySummaries').style.display = 'none';

				window.location = "/trv/admin/copy-day-summary.php?day=" + dateSelected;
			}
		}

		function generateChart() {
			Highcharts.chart("statsSalesChart", {
				data: {
					table: "statsChartTable"
				},
				chart: {
					type: "line"
				},
				title: {
					text: "Sales"
				},
				subtitle: {
					text: "Last 5 days"
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: "Sales"
					}
				},
				tooltip: {
					formatter: function() {
						return "<b>" + this.series.name + "</b><br>$" + thousands_separators(this.point.y);
					}
				}
			});
		}

		function getInfoReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-home-sales.php',
				data: $('#getInfoForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['sales'] != "") {
						document.getElementById('statsSales').innerHTML = thousands_separators(response['sales']);
						document.getElementById('statsSalesMoney').innerHTML = "$" + thousands_separators(response['sales_money']);
						document.getElementById('statsAverage').innerHTML = "$" + thousands_separators(response['sales_average']);
						document.getElementById('statsChartTable').innerHTML = "<tr><th>Date</th> <th>Sales</th></tr>" + response['sales_table'];
						document.getElementById('statsProducts').innerHTML = response['sales_products'];

						if (response['sales_table'] == "") {
							document.getElementById('statsSalesChart').innerHTML = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";
						} else {
							generateChart();
						}
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>