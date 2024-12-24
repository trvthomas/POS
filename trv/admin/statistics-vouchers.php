<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Coupons Statistics</title>

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

<body onload="getStats()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Coupons Statistics</h3>
		<p>Learn about the effectiveness of discount coupons through daily usage statistics</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="tabs is-centered is-boxed">
				<ul>
					<li><a href="/trv/admin/statistics.php"><span class="icon is-small"><i class="fas fa-coins"></i></span><span>Total Sales</span></a></li>
					<li><a href="/trv/admin/statistics-users.php"><span class="icon is-small"><i class="fas fa-users"></i></span><span>Sales by User</span></a></li>
					<li><a href="/trv/admin/statistics-products.php"><span class="icon is-small"><i class="fas fa-tshirt"></i></span><span>Sold Quantities</span></a></li>
					<li class="is-active"><a href="/trv/admin/statistics-vouchers.php"><span class="icon is-small"><i class="fas fa-tags"></i></span><span>Coupons</span></a></li>
				</ul>
			</div>

			<h3 class="is-size-5 has-text-centered">Filters</h3>
			<hr>

			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label">Select the coupon</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="selectVoucher" oninput="selectVoucher()">
									<?php
									$sql = "SELECT id, code FROM trvsol_vouchers";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo '<option value="' . $row["id"] . '">' . $row["code"] . '</option>';
										}
									} else {
										echo '<option value="" disabled selected>No coupons available</option>';
									}
									?>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-tag"></i></span>
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
				<div class="box is-shadowless pastel-bg-green">
					<h4 class="is-size-6">Total redemptions</h4>
					<h3 class="is-size-3"><i class="fas fa-hashtag fa-fw"></i> <span id="boxesUses">...</span></h3>
				</div>
			</div>
		</div>

		<div class="box">
			<div id="chartShow"></div>
			<table class="is-hidden" id="chartTable"></table>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/get-stats-vouchers.php" style="display: none" id="getStatsForm" onsubmit="return getStatsReturn();">
		<input name="getStatsFrom" id="getStatsFrom" readonly>
		<input name="getStatsTo" id="getStatsTo" readonly>
		<input name="getStatsVoucherId" id="getStatsVoucherId" value="<?php
																		if (isset($_GET["idVoucher"])) {
																			echo $_GET["idVoucher"];
																		} else {
																			$sql2 = "SELECT id FROM trvsol_vouchers";
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
		<?php if (isset($_GET["idVoucher"])) {
			echo 'document.getElementById("selectVoucher").value =' . $_GET["idVoucher"] . ';';
		} ?>
		var dateFrom = "N/A",
			dateTo = "N/A";
		var subtitleCharts = "Last 15 days";

		function getStats() {
			flatpickrCalendars = flatpickr(".inputDate", {
				mode: "range",
				altInput: true,
				locale: "en",
				dateFormat: "Y-m-d",
				minDate: '2021-01-01',
				maxDate: '<?php echo date("Y-m-d"); ?>'
			});

			document.getElementById('getStatsFrom').value = dateFrom;
			document.getElementById('getStatsTo').value = dateTo;
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

			document.getElementById('btnClearFilters').style.display = '';
			dateFrom = date1;
			dateTo = date2;
			if (date1 != "N/A" && date2 != "N/A") {
				subtitleCharts = dateFrom + " - " + dateTo;
			}

			getStats();
		}

		function clearFilters() {
			dateFrom = "N/A", dateTo = "N/A";
			subtitleCharts = "Last 15 days";
			document.getElementById('fechaDesde').value = "";
			getStats();
			document.getElementById('btnClearFilters').style.display = 'none';
		}

		function selectVoucher() {
			var voucherSelected = document.getElementById('selectVoucher').value;

			document.getElementById('getStatsVoucherId').value = voucherSelected;
			getStats();
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
					text: "Coupon redemptions"
				},
				subtitle: {
					text: subtitleCharts
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: "Total coupon redemptions"
					}
				},
				tooltip: {
					formatter: function() {
						return "<b>" + this.series.name + "</b><br>" + thousands_separators(this.point.y) + " redemption(s)";
					}
				}
			});
		}

		function getStatsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-stats-vouchers.php',
				data: $('#getStatsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['tabla_ventas'] == "sin registros") {
						document.getElementById('chartShow').innerHTML = "<p class='has-text-centered is-size-5'><b>No records found</b></p>";
					} else if (response['tabla_ventas'] != "") {
						document.getElementById('chartTable').innerHTML = "<tr><td>Date</td> <td>Redemptions</td></tr>" + response['tabla_ventas'];

						document.getElementById('boxesUses').innerHTML = thousands_separators(response['stats_uses']);
						generateChart();
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>