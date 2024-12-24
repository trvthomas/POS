<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/stats.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Product Catalog</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onload="getProds(true)">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Products</h3>
				<p>Create and modify the products available for sale</p>
			</div>

			<div class="column is-one-third">
				<a class="button backgroundDark is-fullwidth" href="/trv/admin/new-product.php"><i class="fas fa-circle-plus"></i> New product</a>
			</div>
		</div>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="buttons is-centered">
				<a class="button is-light" href="/trv/admin/organize-products.php"><i class="fas fa-arrow-down-short-wide"></i> Sort products</a>
				<a class="button backgroundDark" href="/trv/admin/import-products.php"><i class="fas fa-file-excel"></i> Bulk Import</a>
				<a class="button backgroundDark" href="/trv/admin/edit-massive-products.php"><i class="fas fa-pencil"></i> Bulk Edit</a>
				<a class="button is-success" href="/trv/admin/categories.php"><i class="fas fa-table-cells-large"></i> Categories</a>
			</div>

			<h3 class="is-size-5 has-text-centered">Filters</h3>
			<hr>

			<div class="columns">
				<div class="column">
					<div class="field">
						<label class="label">Category</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="inputCategories" oninput="applyFilters()">
									<option value="0">All categories</option>
									<?php
									$sql = "SELECT * FROM trvsol_categories";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while ($row = $result->fetch_assoc()) {
											echo '<option value="' . $row["id"] . '">' . $row["nombre"] . '</option>';
										}
									}
									?>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-table-cells-large"></i></span>
						</div>
					</div>
				</div>

				<div class="column">
					<label class="label">Search product</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="Search by name or price" id="inputSearch" onkeyup="onup()">
							<span class="icon is-small is-left"><i class="fas fa-magnifying-glass"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" title="Search" onclick="applyFilters()"><i class="fas fa-magnifying-glass"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="has-text-centered"><button class="button backgroundDark" style="display: none" id="btnClearFilters" onclick="clearFilters()"><i class="fas fa-eraser"></i>Reset filters</button></div>
		</div>

		<div class="box">
			<nav class="panel filtersBox is-hidden" id="filtersPanel"><button class="button is-loading is-static is-large">Loading...</button></nav>

			<div class="list has-visible-pointer-controls" id="prodsList"></div>

			<nav class="pagination is-centered paginationBox" id="paginationPanel"></nav>
		</div>

		<div class="box">
			<div class="columns has-text-left">
				<div class="column">
					<div class="block">
						<span class="icon is-large is-pulled-left"><i class="fas fa-tshirt fa-2x"></i></span>
						<h4 class="is-size-6 has-text-grey">Total Products</h4>
						<p class="is-size-5 has-text-success"><b><span id="boxesTotalProds">0</span></b></p>
					</div>
				</div>

				<div class="column">
					<div class="block">
						<span class="icon is-large is-pulled-left"><i class="fas fa-boxes-stacked fa-2x"></i></span>
						<h4 class="is-size-6 has-text-grey">Inventory Value</h4>
						<p class="is-size-5"><b>$<span id="boxesInventoryValue">0</span></b></p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/get-products.php" style="display: none" id="getProdsForm" onsubmit="return getProdsReturn();">
		<input name="getProdsSearch" id="getProdsSearch" readonly>
		<input name="getProdsIdCategory" id="getProdsIdCategory" readonly>
		<input name="getProdsPage" id="getProdsPage" value="1" readonly>
		<input type="submit" id="getProdsSend" value="Send">
	</form>

	<form method="POST" action="/trv/admin/include/hide-show-product.php" style="display: none" id="productHideForm" onsubmit="return productHide();">
		<input id="hidePId" name="hidePId" readonly>
		<input id="hidePAction" name="hidePAction" readonly>
		<input id="hidePSend" type="submit" value="Send">
	</form>

	<form method="POST" action="/trv/media/uploads/delete-product.php" style="display: none" id="productDeleteForm" onsubmit="return productDelete();">
		<input id="deletePId" name="deletePId" readonly>
		<input id="deletePSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/filters-pagination.js"></script>
	<script>
		var searchTerm = "",
			idCategorySearch = 0;

		function getProds(updateFilters) {
			if (updateFilters == true) {
				createFiltersBox(false, '', false, false);
			}

			document.getElementById('getProdsSearch').value = searchTerm;
			document.getElementById('getProdsIdCategory').value = idCategorySearch;
			document.getElementById('prodsList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';

			document.getElementById('getProdsSend').click();
		}

		function onpageNextPage(actualPage) {
			document.getElementById('getProdsPage').value = actualPage;
			getProds(false);
		}

		function applyFilters() {
			var query = document.getElementById('inputSearch').value;
			var category = document.getElementById('inputCategories').value;

			document.getElementById('btnClearFilters').style.display = '';
			searchTerm = query;
			idCategorySearch = category;

			getProds(false);
		}

		function clearFilters() {
			searchTerm = "", idCategorySearch = 0;
			document.getElementById('inputSearch').value = "";
			document.getElementById('inputCategories').value = "0";
			getProds(false);
			document.getElementById('btnClearFilters').style.display = 'none';
		}

		function onup() {
			if (event.keyCode === 13) {
				applyFilters();
			}
		}

		function hideShowProduct(idProd, action) {
			document.getElementById('hidePAction').value = action;
			document.getElementById('hidePId').value = idProd;
			document.getElementById('hidePSend').click();

			document.getElementById('btnHideProduct' + idProd).disabled = true;
			document.getElementById('btnHideProduct' + idProd).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
		}

		function deleteProduct(idProd) {
			var c = confirm("Are you sure? This action cannot be undone and will delete all item statistics");

			if (c == true) {
				document.getElementById('deletePId').value = idProd;
				document.getElementById('deletePSend').click();

				document.getElementById('btnDeleteProduct' + idProd).disabled = true;
				document.getElementById('btnDeleteProduct' + idProd).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
			}
		}

		function getProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-products.php',
				data: $('#getProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['productos'] != "") {
						document.getElementById('prodsList').innerHTML = response['productos'];
						document.getElementById('boxesTotalProds').innerHTML = response['numero_productos'];
						document.getElementById('boxesInventoryValue').innerHTML = response['numero_valor_inventario'];

						hidePagination(response["ultima_pagina"]);
					}
				}
			});

			return false;
		}

		function productHide() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/hide-show-product.php',
				data: $('#productHideForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['producto_oculto_mostrado'] == true) {
						newNotification('Information updated', 'success');
					}
					getProds(false);
				}
			});

			return false;
		}

		function productDelete() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/delete-product.php',
				data: $('#productDeleteForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('Error deleting the product', 'error');
					} else if (response['producto_eliminado'] == true) {
						newNotification('Product deleted', 'success');
					}
					getProds(false);
				}
			});

			return false;
		}
	</script>
</body>

</html>