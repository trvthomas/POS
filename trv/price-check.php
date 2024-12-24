<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Price check</title>

	<?php include_once "include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-quickview.min.css">
	<script type="text/javascript" src="/trv/include/libraries/bulma-quickview.min.js"></script>

	<?php if (isset($_GET["hide_header"])) { ?>
		<style>
			body,
			html {
				background-color: var(--modal-background-color);
			}
		</style>
	<?php } ?>
</head>

<body>
	<?php if (!isset($_GET["hide_header"])) {
		include_once "include/header.php";
	} ?>

	<div class="contentBox">
		<?php if (!isset($_GET["hide_header"])) { ?>
			<h3 class="is-size-5">Price check</h3>
			<p>Check the price and other information of a product</p>
		<?php } ?>

		<div class="box mb-2">
			<?php if (!isset($_GET["hide_header"])) { ?>
				<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
				<br><br>
			<?php } ?>

			<label class="label">Search product</label>
			<div class="field has-addons">
				<div class="control has-icons-left is-expanded">
					<input type="text" class="input" placeholder="Search by name or barcode" id="busquedaInput" onkeydown="onup()" onkeyup="this.value = this.value.toUpperCase();" autofocus>
					<span class="icon is-small is-left"><i class="fas fa-magnifying-glass"></i></span>
				</div>

				<div class="control">
					<button class="button backgroundDark" onclick="searchProduct()"><i class="fas fa-magnifying-glass iconInButton"></i></button>
				</div>
			</div>

			<div class="has-text-centered"><button class="button backgroundDark" onclick="showAllProducts()"><i class="fas fa-eye iconInButton"></i> Show all products</button></div>

		</div>

		<div class="box list has-visible-pointer-controls" id="divResults">
			<p class='has-text-centered is-size-5 has-text-success'><b>Make a search</b></p>
		</div>
	</div>

	<?php if (!isset($_GET["hide_header"])) {
		include_once "include/footer.php";
	} ?>

	<form method="POST" action="/trv/include/search-price.php" style="display: none" id="searchPriceForm" onsubmit="return searchPriceReturn();">
		<input name="searchPriceQuery" id="searchPriceQuery" readonly>
		<input type="submit" id="searchPriceSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var searchQuery = "";

		function onup() {
			if (event.keyCode === 13) {
				searchProduct();
			}
		}

		function searchProduct() {
			searchQuery = document.getElementById('busquedaInput').value;

			document.getElementById('searchPriceQuery').value = searchQuery;
			document.getElementById('searchPriceSend').click();

			document.getElementById('busquedaInput').select();
			openLoader();
		}

		function showAllProducts() {
			document.getElementById('busquedaInput').value = "";
			searchProduct();
		}

		function searchPriceReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/search-price.php',
				data: $('#searchPriceForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else {
						document.getElementById('divResults').innerHTML = response['resultados'];
						closeLoader();
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>