<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/stats.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Sort Products</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
	<script type="text/javascript" src="/trv/include/libraries/sortable.min.js"></script>
</head>

<body onload="getProds()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Sort products</h3>
				<p>Modify the display order of the products</p>
			</div>
		</div>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/products.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="columns has-text-centered">
				<div class="column">
					<div class="field">
						<label class="label">Select category</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="inputCategories" oninput="getProds()">
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

				<div class="column is-one-third">
					<label class="label is-invisible">ABC</label>
					<button class="button backgroundDark is-fullwidth" onclick="saveOrganization()"><i class="fas fa-floppy-disk"></i> Save changes</button>
				</div>
			</div>
		</div>

		<div class="box">
			<div class="notification is-light is-success">Select and drag the items to organize them, when finished click the <b>"Save changes"</b> button</div>
			<div class="columns is-multiline is-mobile is-centered mt-1 has-text-left" id="prodsList"></div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/organize-get-products.php" style="display: none" id="getProdsForm" onsubmit="return getProdsReturn();">
		<input name="getProdsIdCategory" id="getProdsIdCategory" readonly>
		<input type="submit" id="getProdsSend" value="Send">
	</form>

	<form method="POST" action="/trv/admin/include/organize-products.php" style="display: none" id="organizeProdsForm" onsubmit="return organizeProdsReturn();">
		<input id="organizeProdsArray" name="organizeProdsArray" readonly>
		<input id="organizeProdsSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function getProds() {
			var valueCategory = document.getElementById('inputCategories').value;

			document.getElementById('getProdsIdCategory').value = valueCategory;
			document.getElementById('prodsList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';

			document.getElementById('getProdsSend').click();
		}

		var arrayProducts = [];
		var elementSortable = document.getElementById('prodsList');
		var sortable = new Sortable(elementSortable, {
			animation: 150,
			onEnd: function(evt) {
				if (evt.oldIndex != evt.newIndex) {
					var numberAt = arrayProducts[evt.oldIndex];
					arrayProducts.splice(evt.oldIndex, 1);
					arrayProducts.splice(evt.newIndex, 0, numberAt);
				}
			}
		});

		function saveOrganization() {
			document.getElementById('organizeProdsArray').value = JSON.stringify(arrayProducts);
			document.getElementById('organizeProdsSend').click();
			openLoader();
		}

		function getProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/organize-get-products.php',
				data: $('#getProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['productos'] != "") {
						document.getElementById('prodsList').innerHTML = response['productos'];
						arrayProducts = JSON.parse(response['array']);
					}
				}
			});

			return false;
		}

		function organizeProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/organize-products.php',
				data: $('#organizeProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['organized'] == true) {
						newNotification("Changes updated", "success");
						getProds();
					} else {
						newNotificationError();
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>