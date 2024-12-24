<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Massively Edit Products</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onload="startCreation()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/products.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
			<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

			<div class="fade" id="step1">
				<h3 class="is-size-5 has-text-centered">Drag or select the Excel file</h3>
				<hr><br>

				<div class="columns">
					<div class="column is-two-fifths">
						<a class="button is-fullwidth backgroundDark" onclick="downloadTemplate()"><i class="fas fa-file-excel"></i> Download template</a>

						<hr>
						<h3 class="is-size-5 has-text-centered" style="margin-bottom: 0">Category Codes</h3>
						<p class="has-text-centered">In the Excel template, in the <b>"Category"</b> field, write the corresponding code. The list is shown below.</p>

						<div class="list has-visible-pointer-controls">
							<?php
							$sql = "SELECT * FROM trvsol_categories";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo '<div class="list-item">
										<div class="list-item-image">
										<figure class="image is-64x64"><div class= "categoryColorImage" style= "background-color: ' . $row["color"] . ';color: ' . $row["color_txt"] . '"><span>' . strtoupper(substr($row["nombre"], 0, 2)) . '</span></div></figure>
										</div>
										
										<div class="list-item-content">
										<div class="list-item-title">' . $row["nombre"] . '</div>
										</div>
										
										<div class="list-item-controls">
										<div class="level">
										<div class="level-item has-text-centered">
										<div>
											<p class="is-size-5">Code</p>
											<p class="is-size-3">' . $row["id"] . '</p>
										</div>
										</div>
										</div>
										</div>
									</div>';
								}
								echo "</table>";
							} else {
								echo "There was an error";
							}
							?>
						</div>

					</div>

					<div class="column">
						<form action="/trv/media/uploads/upload-excel-products-edition.php" method="POST" enctype="multipart/form-data" id="uploadExcelForm">
							<input type="file" name="excelFile" accept=".xlsx">
						</form>
					</div>
				</div>
			</div>

			<div class="fade" id="step2" style="display: none">
				<h3 class="is-size-5 has-text-centered">Review the information to import</h3>
				<p class="has-text-centered">Review the data to import below, if <b>there are duplicate barcodes</b> select what you want to do with them.</p>
				<hr><br>

				<div class="columns is-centered">
					<div class="column is-half">
						<div class="field has-text-centered">
							<label class="label">Action to take with duplicate barcodes</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="inputRepeatedBarcodes">
										<option value="1">Do not upload products</option>
										<option value="2">Assign another random barcode</option>
									</select>
								</span>

								<span class="icon is-small is-left"><i class="fas fa-question"></i></span>
							</div>
						</div>
					</div>
				</div>

				<hr>
				<p class="has-text-centered">Found <b><span id="verifyNumberProds">ERROR</span> items</b></p>
				<div class="table-container" id="checkProductsDiv"></div>

				<div class="has-text-centered">
					<button class="button backgroundDark" onclick="editProducts()"><i class="fas fa-circle-check"></i> Import information</button>
					<br><button class="button is-danger is-inverted is-small" onclick="cancelImport()"><i class="fas fa-circle-xmark"></i> Cancel and reset</button>
				</div>
			</div>

			<div class="fade" id="step3" style="display: none">
				<h3 class="is-size-5 has-text-centered">Import Result</h3>
				<hr><br>

				<div class="level">
					<div class="level-item has-text-centered">
						<div>
							<p class="heading">Detected rows</p>
							<p class="title" id="totalNumberProds">0</p>
						</div>
					</div>
					<div class="level-item has-text-centered">
						<div>
							<p class="heading has-text-success">Imported rows</p>
							<p class="title has-text-success" id="numberProdsEdited">0</p>
						</div>
					</div>
				</div>

				<div class="has-text-centered">
					<a href="/trv/admin/products.php" class="button backgroundDark"><i class="fas fa-chevron-left"></i> Go back</a>
				</div>
			</div>
		</div>

		<div class="columns is-hidden">
			<div class="column">
				<button class="button backgroundDark is-fullwidth is-invisible" id="buttonPrevious" onclick="nextStep(-1)"><i class="fas fa-chevron-left"></i> Back</button>
			</div>

			<div class="column has-text-right">
				<button class="button backgroundDark is-fullwidth" id="buttonNext" onclick="nextStep(1)">Next <i class="fas fa-chevron-right"></i></button>
				<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="addProduct()">Create <i class="fas fa-circle-plus"></i></button>
			</div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form action="/trv/admin/include/edit-massive-download-template.php" method="POST" style="display: none">
		<input name="downloadTemplateToken" value="exz27" readonly>
		<input id="downloadTemplateSend" type="submit" value="Send">
	</form>

	<form action="/trv/media/uploads/import-products-edition.php" method="POST" style="display: none" id="importProdsForm" onsubmit="return importProdsReturn();">
		<input id="editProductsFileName" name="editProductsFileName" readonly>
		<input id="editProductsActionRepeated" name="editProductsActionRepeated" readonly>
		<input id="importProductsSend" type="submit" value="Send">
	</form>

	<form action="/trv/media/uploads/cancel-import-products.php" method="POST" style="display: none" id="cancelImportProdsForm" onsubmit="return cancelImportProdsReturn();">
		<input id="cancelFileName" name="cancelFileName" readonly>
		<input id="cancelSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		function startCreation() {
			createProgressBar(false, JSON.stringify([{
					icon: "file-excel",
					title: "Upload"
				},
				{
					icon: "clipboard-list",
					title: "Verify"
				},
				{
					icon: "circle-check",
					title: "Results"
				}
			]));
		}

		function downloadTemplate() {
			document.getElementById('downloadTemplateSend').click();
			newNotification("Downloading file, please wait", "success");
		}

		function editProducts() {
			var c = confirm("Please confirm this action");

			if (c == true) {
				var actionRepeated = document.getElementById('inputRepeatedBarcodes').value;

				document.getElementById('editProductsActionRepeated').value = actionRepeated;
				document.getElementById('importProductsSend').click();

				openLoader();
			}
		}

		function cancelImport() {
			var c = confirm("Are you sure you want to cancel the operation?");

			if (c == true) {
				document.getElementById('cancelSend').click();
				openLoader();
			}
		}

		function importProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/import-products-edition.php',
				data: $('#importProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['productos_editados'] != "" && response['productos_totales'] != "") {
						nextStep(1);
						document.getElementById('numberProdsEdited').innerHTML = response['productos_editados'];
						document.getElementById('totalNumberProds').innerHTML = response['productos_totales'];
					}
					closeLoader();
				}
			});

			return false;
		}

		function cancelImportProdsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/cancel-import-products.php',
				data: $('#cancelImportProdsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					jumpStep(1);
					closeLoader();
				}
			});

			return false;
		}

		$(document).ready(function(e) {
			$("#uploadExcelForm").on('change', (function(e) {
				openLoader();

				$.ajax({
					url: "/trv/media/uploads/upload-excel-products-edition.php",
					type: "POST",
					data: new FormData(this),
					dataType: 'json',
					contentType: false,
					processData: false,
					success: function(data) {
						if (data['error_archivo'] == true) {
							newNotification('The file is not compatible or is too large', 'error');
						} else if (data['products_found'] <= 0) {
							newNotification('No products found, check the file', 'error');
						} else if (data['url_excel'] != "" && data['products_list'] != "") {
							nextStep(1);

							document.getElementById('checkProductsDiv').innerHTML = data['products_list'];
							document.getElementById('verifyNumberProds').innerHTML = data['products_found'];
							document.getElementById('editProductsFileName').value = data['url_excel'];
							document.getElementById('cancelFileName').value = data['url_excel'];
						}

						closeLoader();
						document.getElementById('uploadExcelForm').reset();
					}
				});
			}));
		});
	</script>
</body>

</html>