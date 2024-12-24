<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trvsol_pos";
$conn2 = new mysqli($servername, $username, $password);

$sql = "CREATE DATABASE IF NOT EXISTS trvsol_pos";
$conn2->query($sql);

$authorizeEntry = false;
$backupsList = "<td>No backup copies found</td><td></td><td></td>";

$directory = "include/backups/";
$scanBackups = scandir($directory);

if (isset($scanBackups[2])) {
	$backupsList = "";
	for ($x = 2; $x < count($scanBackups); ++$x) {
		$backupsList .= '<tr>
	<td>' . substr($scanBackups[$x], 0, -4) . '</td>
	</tr>';
	}
}

$conn = new mysqli($servername, $username, $password, $dbname);

$sql2 = "CREATE TABLE IF NOT EXISTS trvsol_configuration(
	id int(11) NOT NULL AUTO_INCREMENT,
	configName text NOT NULL,
	value text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
$conn->query($sql2);

$sql = "SELECT * FROM trvsol_configuration WHERE configName='businessName' OR configName='templateInvoice' OR configName='templateDayReport'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["value"] == "") {
			$authorizeEntry = true;
		}
	}
} else {
	$authorizeEntry = true;
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Initial configuration</title>

	<?php include_once "include/head-tracking.php"; ?>
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
</head>

<body onload="startCreation()">
	<nav class="navbar">
		<div class="navbar-brand">
			<a class="navbar-item" href="/trv"><img src="/trv/media/logo.png" style="width: 100%;max-height: 4rem;"></a>

			<a class="navbar-burger" data-target="headerMobile" onclick="this.classList.toggle('is-active');document.getElementById('headerMobile').classList.toggle('is-active');">
				<span></span>
				<span></span>
				<span></span>
			</a>
		</div>

		<div id="headerMobile" class="navbar-menu">
			<div class="navbar-end">
				<div class="navbar-item">
					<a class="button backgroundDark" href="/trv/prepare-system.php"><i class="fas fa-screwdriver-wrench"></i> Configure system</a>
				</div>
			</div>
		</div>
	</nav><br>

	<div class="contentBox">
		<?php if ($authorizeEntry == true) { ?>
			<div class="box">
				<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

				<div class="fade" id="step1">
					<h3 class="is-size-5 has-text-centered">Welcome</h3>
					<hr><br>

					<p style="text-align: justify">Thank you for choosing the POS System from <b>TRV Solutions</b>, we are sure it will be of great help when it comes to controlling all the sales and movements of your business.
						<br>Before you start using it, it is necessary to perform some initial configurations, click the <b>"Next"</b> button to begin.
					</p>

					<div class="notification is-info is-light">This project is now open source and is not actively maintained. Although it is well tested, you may encounter slight errors. We invite you to contribute to the project on our <a href="https://github.com/trvthomas/POS" target="_blank">GitHub repository</a></div>
				</div>

				<div class="fade" id="step2" style="display: none">
					<h3 class="is-size-5 has-text-centered">Restore Backup</h3>
					<hr><br>

					<p>If there are backup copies, they will be displayed below. If you want to <b>restore the data</b> of your POS System, locate the backup copy and import it into the database, otherwise click "Next".</p>

					<table class="table is-striped is-fullwidth">
						<tr>
							<th>Date (AAAA-MM-DD-HH-MM)</th>
						</tr>

						<?php echo $backupsList; ?>
					</table>
				</div>

				<div class="fade" id="step3" style="display: none">
					<h3 class="is-size-5 has-text-centered">Information of your business</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Company name</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. TRV Solutions" id="inputConfigName">
									<span class="icon is-small is-left"><i class="fas fa-shop"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Administrator e-mail address</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. jhondoe@gmail.com" id="inputConfigEmail">
									<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="fade" id="step4" style="display: none">
					<h3 class="is-size-5 has-text-centered">Sales and sales summary receipts</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<p class="has-text-centered"><b>Sales Receipt</b></p>
							<div style="width: 100%;"><textarea id="inputConfigSale"><h1 style= "text-align: center">YOUR BUSINESS NAME</h1><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Date and time of purchase</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Receipt #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Attended by</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Description</strong></td><td style="width: 47.9567%; text-align: right;"><span style="margin-right: 15px;"><strong>Price</strong></span></td></tr></tbody></table><p>{{trv_products}}</p><hr /><p><strong>Payment method: </strong>{{trv_payment_method}}</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Subtotal</strong></td><td style="width: 47.9567%;">${{trv_subtotal}}</td></tr><tr><td style="width: 47.8365%;"><strong>Discounts</strong></td><td style="width: 47.9567%;">-${{trv_discount}}</td></tr><tr><td style="width: 47.8365%;"><strong>TOTAL</strong></td><td style="width: 47.9567%;"><strong>${{trv_total}}</strong></td></tr><tr><td style="width: 47.8365%;"><strong>Received</strong></td><td style="width: 47.9567%;">${{trv_change_received}}</td></tr><tr><td style="width: 47.8365%;"><strong>Change</strong></td><td style="width: 47.9567%;">${{trv_change}}</td></tr></tbody></table><p style="text-align: left;"><strong>Additional notes:</strong> {{trv_notes}}</p><h2 style="text-align: center;">Thank you for your purchase, come back soon</h2></textarea></div>
						</div>

						<div class="column">
							<p class="has-text-centered"><b>Sales Summary</b></p>
							<div style="width: 100%;"><textarea id="inputConfigClose"><h2 style="text-align: center;">Sales summary</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Date and time of entry</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Date and time of closure</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Seller</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Initial cash: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> sales made</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Cash sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Card sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Other payment method sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_other_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Total income</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Reports</h2><p>{{trv_daysumm_reports}}</p></textarea></div>
						</div>
					</div>
				</div>

				<div class="fade" id="step5" style="display: none">
					<h3 class="is-size-5 has-text-centered">Main user (administrator)</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Username</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. Admin, Thomas, Anna" id="inputConfigUsername">
									<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Password</label>
								<div class="control has-icons-left has-icons-right">
									<input type="password" class="input" placeholder="Create a password" id="inputConfigPass">
									<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
									<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('inputConfigPass')"><i class="fas fa-eye" id="showPassBtninputConfigPass"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="columns">
				<div class="column">
					<button class="button backgroundDark is-fullwidth is-invisible" id="buttonPrevious" onclick="nextStep(-1)"><i class="fas fa-chevron-left"></i> Back</button>
				</div>

				<div class="column has-text-right">
					<button class="button backgroundDark is-fullwidth" id="buttonNext" onclick="nextStep(1)">Next <i class="fas fa-chevron-right"></i></button>
					<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="finishConfig()">Finish <i class="fas fa-circle-check"></i></button>
				</div>
			</div>
		<?php } else { ?>
			<div class="box has-text-centered">
				<h1>You cannot perform this configuration</h1>
				<p><a href="/trv/home.php">Return to the home page</a></p>
			</div>
		<?php } ?>
	</div>

	<br>
	<footer>&copy; <?php echo date("Y") ?>, TRV Solutions - <a style="color: #fff" href="https://www.trvsolutions.com" target="_blank">www.trvsolutions.com</a> - POS System</footer>

	<form method="POST" action="/trv/include/prepare-system.php" style="display: none" id="prepareSystemForm" onsubmit="return prepareSystemReturn();">
		<input name="prepareSystemBusinessName" id="prepareSystemBusinessName" readonly>
		<input name="prepareSystemBusinessEmail" id="prepareSystemBusinessEmail" readonly>
		<input name="prepareSystemSaleTemplate" id="prepareSystemSaleTemplate" readonly>
		<input name="prepareSystemCloseTemplate" id="prepareSystemCloseTemplate" readonly>
		<input name="prepareSystemUsername" id="prepareSystemUsername" readonly>
		<input name="prepareSystemPassword" id="prepareSystemPassword" readonly>

		<input type="submit" id="prepareSystemSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
	<script>
		function startCreation() {
			createProgressBar(true, JSON.stringify([{
					icon: "circle-info",
					title: "Welcome"
				},
				{
					icon: "clock-rotate-left",
					title: "Backups"
				},
				{
					icon: "store",
					title: "Business info."
				},
				{
					icon: "brush",
					title: "Receipt design"
				},
				{
					icon: "user-gear",
					title: "Main user"
				},
			]));

			createDesigns();
		}

		function showPass(idInput) {
			var getInput = document.getElementById(idInput);
			if (getInput.type === "password") {
				getInput.type = "text";
				document.getElementById("showPassBtn" + idInput).className = "fas fa-eye-slash";
			} else {
				getInput.type = "password";
				document.getElementById("showPassBtn" + idInput).className = "fas fa-eye";
			}
		}

		function createDesigns() {
			var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | hr | trv_vars template | preview | code";

			tinymce.init({
				selector: "#inputConfigSale",
				plugins: "preview paste save code template table hr advlist image lists wordcount",
				menubar: "",
				toolbar: toolbarBtns,
				toolbar_sticky: true,
				templates: [{
					title: "General",
					description: "General template, 100% customizable",
					content: '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="/trv/media/logo.png" alt="" width="20%" height="auto" /></p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Date and time of purchase</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Receipt #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Attended by</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Description</strong></td><td style="width: 47.9567%; text-align: right;"><span style="margin-right: 15px;"><strong>Price</strong></span></td></tr></tbody></table><p>{{trv_products}}</p><hr /><p><strong>Payment method: </strong>{{trv_payment_method}}</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Subtotal</strong></td><td style="width: 47.9567%;">${{trv_subtotal}}</td></tr><tr><td style="width: 47.8365%;"><strong>Discounts</strong></td><td style="width: 47.9567%;">-${{trv_discount}}</td></tr><tr><td style="width: 47.8365%;"><strong>TOTAL</strong></td><td style="width: 47.9567%;"><strong>${{trv_total}}</strong></td></tr><tr><td style="width: 47.8365%;"><strong>Received</strong></td><td style="width: 47.9567%;">${{trv_change_received}}</td></tr><tr><td style="width: 47.8365%;"><strong>Change</strong></td><td style="width: 47.9567%;">${{trv_change}}</td></tr></tbody></table><p style="text-align: left;"><strong>Additional notes:</strong> {{trv_notes}}</p><h2 style="text-align: center;">Thank you for your purchase, come back soon</h2>'
				}],
				height: 600,
				toolbar_mode: "wrap",
				language: "en",
				placeholder: "Design the sales receipt using this editor. Remember to use the variables.",
				setup: function(editor) {
					editor.ui.registry.addMenuButton("trv_vars", {
						text: "Variable",
						fetch: function(callback) {
							var items = [{
									type: "menuitem",
									text: "Date and time of purchase (text)",
									onAction: function() {
										editor.insertContent("{{trv_date_purchase}}");
									}
								},
								{
									type: "menuitem",
									text: "Receipt number (e.g. 450 o PRE450)",
									onAction: function() {
										editor.insertContent("{{trv_num_invoice}}");
									}
								},
								{
									type: "menuitem",
									text: "Seller name (e.g. Jhon)",
									onAction: function() {
										editor.insertContent("{{trv_seller}}");
									}
								},
								{
									type: "menuitem",
									text: "Products list (text, list)",
									onAction: function() {
										editor.insertContent("{{trv_products}}");
									}
								},
								{
									type: "menuitem",
									text: "Payment method (e.g. Cash)",
									onAction: function() {
										editor.insertContent("{{trv_payment_method}}");
									}
								},
								{
									type: "menuitem",
									text: "Subtotal (e.g. 50)",
									onAction: function() {
										editor.insertContent("{{trv_subtotal}}");
									}
								},
								{
									type: "menuitem",
									text: "Discounts (e.g. 5)",
									onAction: function() {
										editor.insertContent("{{trv_discount}}");
									}
								},
								{
									type: "menuitem",
									text: "Total (e.g. 45)",
									onAction: function() {
										editor.insertContent("{{trv_total}}");
									}
								},
								{
									type: "menuitem",
									text: "Change - Received (e.g. 10 Card $40)",
									onAction: function() {
										editor.insertContent("{{trv_change_received}}");
									}
								},
								{
									type: "menuitem",
									text: "Change - Change (e.g. 5)",
									onAction: function() {
										editor.insertContent("{{trv_change}}");
									}
								},
								{
									type: "menuitem",
									text: "Notes (text)",
									onAction: function() {
										editor.insertContent("{{trv_notes}}");
									}
								}
							];
							callback(items);
						}
					});
				}
			});

			//Day summary
			tinymce.init({
				selector: "#inputConfigClose",
				plugins: "preview paste save code template table hr advlist image lists wordcount",
				menubar: "",
				toolbar: toolbarBtns,
				toolbar_sticky: true,
				templates: [{
					title: "General",
					description: "General template, 100% customizable",
					content: '<p><img style="display: block; margin-left: auto; margin-right: auto;" src="/trv/media/logo.png" alt="" width="20%" height="auto" /></p><h2 style="text-align: center;">Sales summary</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Date and time of entry</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Date and time of closure</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Seller</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Initial cash: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> sales made</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Cash sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Card sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Other payment method sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_other_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Total income</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Reports</h2><p>{{trv_daysumm_reports}}</p>'
				}],
				height: 600,
				toolbar_mode: "wrap",
				language: "en",
				placeholder: "Design the sales summary ticket using this editor. Remember to use the variables.",
				setup: function(editor) {
					editor.ui.registry.addMenuButton("trv_vars", {
						text: "Variable",
						fetch: function(callback) {
							var items = [{
									type: "menuitem",
									text: "Date and time of start of shift (e.g. 01/01/2023 9:00 am)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_enter}}");
									}
								},
								{
									type: "menuitem",
									text: "Date and time of end of shift (e.g. 01/01/2023 9:00 pm)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_exit}}");
									}
								},
								{
									type: "menuitem",
									text: "Seller name (e.g. Jhon)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_seller}}");
									}
								},
								{
									type: "menuitem",
									text: "Initial cash (e.g. 50 Shift Jhon: $52)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_cash_base}}");
									}
								},
								{
									type: "menuitem",
									text: "Num. of sales made (e.g. 15)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_number_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Cash sales (e.g. 50)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_cash_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Card sales (e.g. 50)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_card_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Total income (e.g. 100)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_total_sales}}");
									}
								},
								{
									type: "menuitem",
									text: "Reports (text)",
									onAction: function() {
										editor.insertContent("{{trv_daysumm_reports}}");
									}
								}
							];
							callback(items);
						}
					});
				}
			});
		}

		function finishConfig() {
			var businessName = document.getElementById('inputConfigName').value;
			var businessEmail = document.getElementById('inputConfigEmail').value;
			var templateSale = tinymce.get('inputConfigSale').getContent();
			var templateCloseCash = tinymce.get('inputConfigClose').getContent();
			var usernameUser = document.getElementById('inputConfigUsername').value;
			var passwordUser = document.getElementById('inputConfigPass').value;

			if (businessName == "" || templateSale == "" || templateCloseCash == "" || usernameUser == "" || passwordUser == "") {
				newNotification('Complete all fields', 'error');
			} else if (businessEmail.includes('@') == false && businessEmail.includes('.') == false) {
				newNotification('Invalid e-mail', 'error');
			} else {
				document.getElementById('prepareSystemBusinessName').value = businessName;
				document.getElementById('prepareSystemBusinessEmail').value = businessEmail;
				document.getElementById('prepareSystemSaleTemplate').value = templateSale;
				document.getElementById('prepareSystemCloseTemplate').value = templateCloseCash;
				document.getElementById('prepareSystemUsername').value = usernameUser;
				document.getElementById('prepareSystemPassword').value = passwordUser;

				document.getElementById('prepareSystemSend').click();
				openLoader();
			}
		}

		function prepareSystemReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/prepare-system.php',
				data: $('#prepareSystemForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['configuracion_aplicada'] == true) {
						window.location = "/trv/home.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>