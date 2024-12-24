<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";

use Mexitek\PHPColors\Color;

$showCategories = "";
//Categories
$sql3 = "SELECT * FROM trvsol_categories";
$result3 = $conn->query($sql3);

if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		$secondColor = "#fff";
		$originalColor = new Color($row3["color"]);
		if ($originalColor->isLight()) {
			$secondColor = $originalColor->darken(35);
		} else {
			$secondColor = $originalColor->lighten(35);
		}

		$onclickCategory = "getProdsCategory(" . $row3["id"] . ", '" . $row3["nombre"] . "', '')";

		$showCategories .= '<div class= "column is-one-third-tablet is-half-mobile">
		<div class= "box p-4 boxShadowHover is-clickable" style= "background-color: ' . $row3["color"] . ';" onclick= "' . $onclickCategory . '">
		<div class= "columns is-mobile">
		<div class= "column is-narrow is-size-5" style= "background-color: #' . $secondColor . '; border-radius: 6px 0 0 6px;">
			<span>' . $row3["emoji"] . '</span>
		</div>
		
		<div class= "column has-text-left" style= "border: 2px solid #' . $secondColor . '; color: #' . $secondColor . '; border-radius: 0 6px 6px 0;">
			<h4 class= "is-size-5 mb-1">' . $row3["nombre"] . '</h4>
		</div>
		</div>
		</div>
	</div>';
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Statistics of Sold Quantities</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
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

<body onload="getStats()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Statistics of Sold Quantities</h3>
		<p>Make adjustments to your product catalog based on the quantities sold of each product</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="tabs is-centered is-boxed">
				<ul>
					<li><a href="/trv/admin/statistics.php"><span class="icon is-small"><i class="fas fa-coins"></i></span><span>Total Sales</span></a></li>
					<li><a href="/trv/admin/statistics-users.php"><span class="icon is-small"><i class="fas fa-users"></i></span><span>Sales by User</span></a></li>
					<li class="is-active"><a href="/trv/admin/statistics-products.php"><span class="icon is-small"><i class="fas fa-tshirt"></i></span><span>Sold Quantities</span></a></li>
					<li><a href="/trv/admin/statistics-vouchers.php"><span class="icon is-small"><i class="fas fa-tags"></i></span><span>Coupons</span></a></li>
				</ul>
			</div>

			<h3 class="is-size-5 has-text-centered">Filters</h3>
			<hr>

			<div class="columns">
				<div class="column">
					<div class="field has-text-centered">
						<label class="label">Select the product</label>
						<div class="control">
							<div class="buttons">
								<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlaySelectProduct1').style.display= 'block';"><i class="fas fa-tshirt"></i> Select product</button>
								<button class="button is-fullwidth has-background-warning" onclick="getUnitsSoldList()"><i class="fas fa-list-ul"></i> View full list</button>
							</div>
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
					<h4 class="is-size-6">Units sold</h4>
					<h3 class="is-size-3"><i class="fas fa-hashtag fa-fw"></i> <span id="boxesUnitsSold">...</span></h3>
				</div>
			</div>
		</div>

		<div class="box">
			<div id="chartShow"></div>
			<table class="is-hidden" id="chartTable"></table>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlaySelectProduct1" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct1').style.display='none'"></span>

			<div class="trvModal-elements">
				<label class="label">Search products</b></label>
				<div class="field has-addons">
					<div class="control has-icons-left is-expanded">
						<input type="text" class="input" placeholder="Search by name or price" id="searchProductInput" onkeydown="onup2()">
						<span class="icon is-small is-left"><i class="fas fa-magnifying-glass"></i></span>
					</div>

					<div class="control">
						<button class="button backgroundDark" onclick="searchProduct()"><i class="fas fa-magnifying-glass"></i></button>
					</div>
				</div>

				<div class="columns is-mobile is-multiline is-centered mt-1">
					<?php echo $showCategories; ?>
				</div>
			</div>
		</div>
	</div>

	<div id="overlaySelectProduct2" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct2').style.display='none'"></span>

			<div class="trvModal-header">
				<button class="button is-small is-pulled-left" onclick="backCategorySelection()"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></button>
				<h3 class="is-size-3 mb-1" id="txtCategory">ERROR</h3>
			</div>

			<div class="trvModal-elements">
				<div class="columns is-multiline is-mobile is-centered mt-1 has-text-left" id="divProductsList"></div>
			</div>
		</div>
	</div>

	<div id="overlayListProds" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayListProds').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Sold Quantities</h3>
			</div>

			<div class="trvModal-elements">
				<span class="tag is-rounded is-light is-success is-medium" id="summaryPeriod2"></span>

				<div class="list has-visible-pointer-controls has-text-left box is-shadowless mt-2" id="listProds"></div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayNOMBRE').style.display='none'">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/get-stats-products.php" style="display: none" id="getStatsForm" onsubmit="return getStatsReturn();">
		<input name="getStatsFrom" id="getStatsFrom" readonly>
		<input name="getStatsTo" id="getStatsTo" readonly>
		<input name="getStatsProdId" id="getStatsProdId" value="<?php if (isset($_GET["idProd"])) {
																	echo $_GET["idProd"];
																} ?>" readonly>
		<input type="submit" id="getStatsSend" value="Send">
	</form>

	<form method="POST" action="/trv/admin/include/get-stats-products-list.php" style="display: none" id="getListForm" onsubmit="return getListReturn();">
		<input name="getListFrom" id="getListFrom" readonly>
		<input name="getListTo" id="getListTo" readonly>
		<input type="submit" id="getListSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/product-selection-1.php" style="display: none" id="prodSelection1Form" onsubmit="return prodSelection1Return();">
		<input name="prodSelection1Category" id="prodSelection1Category" readonly>
		<input name="prodSelection1Search" id="prodSelection1Search" readonly>
		<input type="submit" id="prodSelection1Send" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/get-products.js"></script>
	<script>
		var dateFrom = "N/A",
			dateTo = "N/A";
		var subtitleCharts = "Last 15 days";

		function onup2() {
			if (event.keyCode === 13) {
				searchProduct();
			}
		}

		function searchProduct() {
			var searchInput = document.getElementById('searchProductInput').value;

			if (searchInput != "") {
				getProdsCategory('', 'Search results', searchInput);
			}
		}

		function addProduct(idProd, nombreProd, precioProd, stockProd, priceVariable) {
			document.getElementById('getStatsProdId').value = idProd;
			document.getElementById('overlaySelectProduct2').style.display = 'none';

			getStats();
		}

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

		function generateChart() {
			Highcharts.chart("chartShow", {
				data: {
					table: "chartTable"
				},
				chart: {
					type: "line"
				},
				title: {
					text: "Sold units"
				},
				subtitle: {
					text: subtitleCharts
				},
				yAxis: {
					allowDecimals: false,
					title: {
						text: "Sold units"
					}
				},
				tooltip: {
					formatter: function() {
						return "<b>" + this.series.name + "</b><br>" + thousands_separators(this.point.y) + " units sold";
					}
				}
			});
		}

		function getUnitsSoldList() {
			document.getElementById('getListFrom').value = dateFrom;
			document.getElementById('getListTo').value = dateTo;
			document.getElementById('summaryPeriod2').innerHTML = subtitleCharts;

			document.getElementById('getListSend').click();
			openLoader();
		}

		function getStatsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-stats-products.php',
				data: $('#getStatsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['tabla_ventas'] == "sin registros") {
						document.getElementById('chartShow').innerHTML = "<p class= 'has-text-centered is-size-5'><b>No records found</b></p>";
					} else if (response['tabla_ventas'] == "especificar producto") {
						document.getElementById('chartShow').innerHTML = "<p class= 'has-text-centered is-size-5 has-text-danger'><b>Select a product</b></p>";
					} else if (response['tabla_ventas'] != "") {
						document.getElementById('chartTable').innerHTML = "<tr><td>Date</td> <td>Units sold</td></tr>" + response['tabla_ventas'];

						document.getElementById('boxesUnitsSold').innerHTML = thousands_separators(response['stats_cantidades']);
						generateChart();
					}
				}
			});

			return false;
		}

		function getListReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-stats-products-list.php',
				data: $('#getListForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['lista_productos'] != "") {
						document.getElementById('listProds').innerHTML = response['lista_productos'];
						closeLoader();
						document.getElementById('overlayListProds').style.display = "block";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>