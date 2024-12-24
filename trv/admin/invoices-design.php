<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$templateInvoice = "";
$templateDayReport = "";
$printingAuto = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'templateInvoice' OR configName= 'templateDayReport' OR configName= 'printingAuto'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "templateInvoice") {
			$templateInvoice = $row["value"];
		} else if ($row["configName"] == "templateDayReport") {
			$templateDayReport = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$printingAuto = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Edit the design of the receipts</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Receipts Design</h3>
		<p>Customize the design of sales receipts and day summaries, both printed and sent by e-mail</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="buttons is-centered">
				<button class="button is-success" onclick="saveChanges()"><i class="fas fa-floppy-disk"></i> Save changes</button>
				<button class="button backgroundDark" onclick="document.getElementById('overlayFiles').style.display= 'block';"><i class="fas fa-images"></i> Images manager</button>
			</div>

			<?php if ($printingAuto == 1) { ?>
				<div class="notification is-warning">The <b>automatic printing mode is active</b>, these settings will only apply to receipts <b>sent by e-mail and in PDF</b>.
					<br><a class="button is-warning is-inverted is-outlined" href="/trv/admin/invoices-design-auto.php">Modify print receipts</a>
				</div>
			<?php } ?>

			<div class="columns has-text-centered">
				<div class="column">
					<label class="label">Sales Receipt</label>
					<div style="width: 100%;"><textarea id="editorComprobante"><?php echo $templateInvoice; ?></textarea></div>
				</div>

				<div class="column">
					<label class="label">Sales Summary</label>
					<div style="width: 100%;"><textarea id="editorCierreCaja"><?php echo $templateDayReport; ?></textarea></div>
				</div>
			</div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayFiles" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayFiles').style.display='none'"></span>

			<div class="trvModal-elements">
				<iframe src="/trv/admin/images-manager.php" height="800" width="100%" style="border: none"></iframe>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/edit-invoices-design.php" style="display: none" id="editDesignForm" onsubmit="return editDesignReturn();">
		<input name="editDesignTemplateInvoice" id="editDesignTemplateInvoice" readonly>
		<input name="editDesignTemplateDaySummary" id="editDesignTemplateDaySummary" readonly>
		<input type="submit" id="editDesignSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | trv_vars hr template preview code";

		tinymce.init({
			selector: "#editorComprobante",
			plugins: "preview save code template table advlist image lists wordcount",
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
			relative_urls: false,
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
			selector: "#editorCierreCaja",
			plugins: "preview save code template table advlist image lists wordcount",
			menubar: "",
			toolbar: toolbarBtns,
			toolbar_sticky: true,
			templates: [{
					title: "General without custom payment method",
					description: "General template, 100% customizable",
					content: '<h2 style="text-align: center;">Sales summary</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Date and time of entry</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Date and time of closure</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Seller</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Initial cash: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> sales made</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Cash sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Card sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Total income</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Reports</h2><p>{{trv_daysumm_reports}}</p>'
				},
				{
					title: "General with custom payment method",
					description: "General template with custom payment method, if active. 100% customizable",
					content: '<h2 style="text-align: center;">Sales summary</h2><hr /><table style="border-collapse: collapse; width: 100%; height: 59px;" border="0"><tbody><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Date and time of entry</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_enter}}</td></tr><tr style="height: 17px;"><td style="width: 47.8365%; height: 17px;"><strong>Date and time of closure</strong></td><td style="width: 47.9567%; height: 17px;">{{trv_daysumm_exit}}</td></tr><tr style="height: 21px;"><td style="width: 47.8365%; height: 21px;"><strong>Seller</strong></td><td style="width: 47.9567%; height: 21px;">{{trv_daysumm_seller}}</td></tr></tbody></table><hr /><p>Initial cash: <strong>${{trv_daysumm_cash_base}}</strong></p><p><strong>{{trv_daysumm_number_sales}}</strong> sales made</p><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Cash sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_cash_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Card sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_card_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>{{trv_daysumm_other_name}} sales</strong></td><td style="width: 47.9567%;">${{trv_daysumm_other_sales}}</td></tr><tr><td style="width: 47.8365%;"><strong>Total income</strong></td><td style="width: 47.9567%;"><strong>${{trv_daysumm_total_sales}}</strong></td></tr></tbody></table><h2 style="text-align: center;">Reports</h2><p>{{trv_daysumm_reports}}</p>'
				}
			],
			height: 600,
			toolbar_mode: "wrap",
			language: "en",
			relative_urls: false,
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
								text: "Custom payment method name (if applicable) (e.g. Digital wallet)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_other_name}}");
								}
							},
							{
								type: "menuitem",
								text: "Custom payment method sales (if applicable) (e.g. 50)",
								onAction: function() {
									editor.insertContent("{{trv_daysumm_other_sales}}");
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

		function saveChanges() {
			var templateInvoice = tinymce.get('editorComprobante').getContent();
			var templateDayReport = tinymce.get('editorCierreCaja').getContent();

			if (templateInvoice == "" || templateDayReport == "") {
				newNotification("Templates cannot be empty", "error");
			} else {
				document.getElementById('editDesignTemplateInvoice').value = templateInvoice;
				document.getElementById('editDesignTemplateDaySummary').value = templateDayReport;

				document.getElementById('editDesignSend').click();
				openLoader();
			}
		}

		function editDesignReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-invoices-design.php',
				data: $('#editDesignForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['configuracion_guardada'] == true) {
						newNotification("Configuration updated", "success");
					}
					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>