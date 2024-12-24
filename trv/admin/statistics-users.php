<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

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
	<title>User Statistics</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
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

<body onload="getStats(true)">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">User Statistics</h3>
		<p>Check the sales made by each of the system's sellers</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="tabs is-centered is-boxed">
				<ul>
					<li><a href="/trv/admin/statistics.php"><span class="icon is-small"><i class="fas fa-coins"></i></span><span>Total Sales</span></a></li>
					<li class="is-active"><a href="/trv/admin/statistics-users.php"><span class="icon is-small"><i class="fas fa-users"></i></span><span>Sales by User</span></a></li>
					<li><a href="/trv/admin/statistics-products.php"><span class="icon is-small"><i class="fas fa-tshirt"></i></span><span>Sold Quantities</span></a></li>
					<li><a href="/trv/admin/statistics-vouchers.php"><span class="icon is-small"><i class="fas fa-tags"></i></span><span>Coupons</span></a></li>
				</ul>
			</div>

			<h3 class="is-size-5 has-text-centered">Filters</h3>
			<hr>

			<div class="columns">
				<div class="column">
					<div class="field">
						<div>
							<input type="checkbox" class="is-checkradio" id="checkboxCash" onclick="setFilters()">
							<label class="label" for="checkboxCash">Show cash sales</label>
						</div>

						<div>
							<input type="checkbox" class="is-checkradio" id="checkboxCard" onclick="setFilters()">
							<label class="label" for="checkboxCard">Show card sales</label>
						</div>

						<div <?php if ($metodoPagoPersonalizado == "") {
									echo 'style= "display: none"';
								} ?>>
							<input type="checkbox" class="is-checkradio" id="checkboxOther" onclick="setFilters()">
							<label class="label" for="checkboxOther">Show <?php echo $metodoPagoPersonalizado; ?> sales</label>
						</div>
					</div>
				</div>

				<div class="column">
					<div class="field">
						<label class="label">Select the seller</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="selectSeller" oninput="selectSeller()">
									<?php
									$sql = "SELECT id, username FROM trvsol_users";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo '<option value="' . $row["id"] . '">' . $row["username"] . '</option>';
										}
									}
									?>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
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
							<button class="button backgroundDark" title="Set dates" onclick="setFilters()"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="has-text-centered"><button class="button backgroundDark" style="display: none" id="btnClearFilters" onclick="clearFilters()"><i class="fas fa-eraser"></i>Reset filters</button></div>
		</div>

		<p class="has-text-centered has-text-weight-bold" id="summaryPeriod"></p>

		<div class="columns is-multiline is-centered has-text-left">
			<div class="column is-one-third">
				<div class="box is-shadowless has-background-success-light">
					<h4 class="is-size-6">Total income</h4>
					<h3 class="is-size-3"><i class="fas fa-sack-dollar fa-fw"></i> <span id="boxesTotalSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Cash Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-coins fa-fw"></i> <span id="boxesCashSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third">
				<div class="box is-shadowless">
					<h4 class="is-size-6">Card Sales</h4>
					<h3 class="is-size-3"><i class="fas fa-credit-card fa-fw"></i> <span id="boxesCardSales">...</span></h3>
				</div>
			</div>

			<div class="column is-one-third" <?php if ($metodoPagoPersonalizado == "") {
													echo 'style= "display: none"';
												} ?>>
				<div class="box is-shadowless">
					<h4 class="is-size-6"><?php echo $metodoPagoPersonalizado; ?> sales</h4>
					<h3 class="is-size-3"><i class="fas fa-wallet fa-fw"></i> <span id="boxesOtherSales">...</span></h3>
				</div>
			</div>
		</div>

		<div class="box">
			<div id="chartShow"></div>
			<table class="is-hidden" id="chartTable"></table>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/get-stats-sellers.php" style="display: none" id="getStatsForm" onsubmit="return getStatsReturn();">
		<input name="getStatsFrom" id="getStatsFrom" readonly>
		<input name="getStatsTo" id="getStatsTo" readonly>
		<input name="getStatsCash" id="getStatsCash" readonly>
		<input name="getStatsCard" id="getStatsCard" readonly>
		<input name="getStatsOther" id="getStatsOther" readonly>
		<input name="getStatsSellerId" id="getStatsSellerId" value="<?php
																	if (isset($_GET["idUser"])) {
																		echo $_GET["idUser"];
																	} else {
																		$sql2 = "SELECT id FROM trvsol_users";
																		$result2 = $conn->query($sql2);

																		if ($result2->num_rows > 0) {
																			$row2 = $result2->fetch_assoc();
																			echo $row2["id"];
																		}
																	}
																	?>" readonly>
		<input type="submit" id="getStatsSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		<?php if (isset($_GET["idUser"])) {
			echo 'document.getElementById("selectSeller").value =' . $_GET["idUser"] . ';';
		} ?>
		var dateFrom = "N/A",
			dateTo = "N/A";
		var showCash = false,
			showCard = false,
			showOther = false;
		var subtitleCharts = "Last 15 days";

		function getStats(update) {
			if (update == true) {
				flatpickrCalendars = flatpickr(".inputDate", {
					mode: "range",
					altInput: true,
					locale: "en",
					dateFormat: "Y-m-d",
					minDate: '2021-01-01',
					maxDate: '<?php echo date("Y-m-d"); ?>'
				});
			}

			document.getElementById('getStatsFrom').value = dateFrom;
			document.getElementById('getStatsTo').value = dateTo;
			document.getElementById('getStatsCash').value = showCash;
			document.getElementById('getStatsCard').value = showCard;
			document.getElementById('getStatsOther').value = showOther;
			document.getElementById('chartShow').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';
			document.getElementById('summaryPeriod').innerHTML = subtitleCharts;

			document.getElementById('getStatsSend').click();
		}

		function setFilters() {
			var createArrayDates = document.getElementById('fechaDesde').value.split(" a ");
			var date1 = "N/A",
				date2 = "N/A";

			if (createArrayDates != "") {
				date1 = createArrayDates[0];
				if (!createArrayDates[1]) {
					date2 = date1;
				} else {
					date2 = createArrayDates[1];
				}
			}

			var checkboxCash = document.getElementById('checkboxCash').checked;
			var checkboxCard = document.getElementById('checkboxCard').checked;
			var checkboxOther = document.getElementById('checkboxOther').checked;

			document.getElementById('btnClearFilters').style.display = '';
			dateFrom = date1;
			dateTo = date2;
			showCash = checkboxCash;
			showCard = checkboxCard;
			showOther = checkboxOther;
			if (date1 != "N/A" && date2 != "N/A") {
				subtitleCharts = dateFrom + " - " + dateTo;
			}

			getStats(false);
		}

		function clearFilters() {
			dateFrom = "N/A", dateTo = "N/A";
			showCash = false, showCard = false, showOther = false;
			subtitleCharts = "Last 15 days";
			document.getElementById('fechaDesde').value = "";
			document.getElementById('checkboxCash').checked = false;
			document.getElementById('checkboxCard').checked = false;
			document.getElementById('checkboxOther').checked = false;
			getStats(false);
			document.getElementById('btnClearFilters').style.display = 'none';
		}

		function selectSeller() {
			var sellerSelected = document.getElementById('selectSeller').value;

			document.getElementById('getStatsSellerId').value = sellerSelected;
			getStats(false);
		}

		function generateChart() {
			Highcharts.chart("chartShow", {
				data: {
					table: "chartTable"
				},
				chart: {
					type: "line"
				},
				title: {
					text: "Sales"
				},
				subtitle: {
					text: subtitleCharts
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

		function getStatsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-stats-sellers.php',
				data: $('#getStatsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['tabla_ventas'] == "sin registros") {
						document.getElementById('chartShow').innerHTML = "<b>No records found</b>";
					} else if (response['tabla_ventas'] != "") {
						var tableFinal = "<tr><td>Date</td>";

						if (showCash == true) {
							tableFinal += "<td>Cash sales</td>";
						}
						if (showCard == true) {
							tableFinal += "<td>Card sales</td>";
						}
						if (showOther == true) {
							tableFinal += "<td><?php echo $metodoPagoPersonalizado; ?> sales</td>";
						}

						tableFinal += "<td>Total income</td></tr>" + response['tabla_ventas'];

						document.getElementById('chartTable').innerHTML = tableFinal;

						document.getElementById('boxesTotalSales').innerHTML = "$" + thousands_separators(response['stats_total']);
						document.getElementById('boxesCashSales').innerHTML = "$" + thousands_separators(response['stats_efectivo']);
						document.getElementById('boxesCardSales').innerHTML = "$" + thousands_separators(response['stats_tarjeta']);
						document.getElementById('boxesOtherSales').innerHTML = "$" + thousands_separators(response['stats_other']);
						generateChart();
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>