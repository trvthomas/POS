<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$changeTicketsActive = "";
$changeTicketsTemplate = "";
$changeTicketsDefaultPrint = "";
$changeTicketsExpireDays = "";
$printingAuto = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'changeTickets' OR configName= 'changeTicketsTemplate' OR configName= 'changeTicketsPrintDefault' OR configName= 'changeTicketsExpireDays' OR configName= 'printingAuto'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "changeTickets") {
			$changeTicketsActive = $row["value"];
		} else if ($row["configName"] == "changeTicketsTemplate") {
			$changeTicketsTemplate = $row["value"];
		} else if ($row["configName"] == "changeTicketsPrintDefault") {
			$changeTicketsDefaultPrint = $row["value"];
		} else if ($row["configName"] == "changeTicketsExpireDays") {
			$changeTicketsExpireDays = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$printingAuto = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Gift Tickets Configuration</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
	<script src="/trv/include/libraries/tinymce/tinymce.min.js"></script>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Gift Tickets</h3>
		<p>Modify the default values of the gift tickets</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<p class="has-text-centered">Gift tickets are shown at the <b>end of the sales receipt</b> and can be used to exchange items when they are given as gifts, for example.
				<br>Below, configure the <b>design of the ticket</b> and select <b>how many copies you want to print by default</b>, this number can be modified at the time of creating a sale.
			</p>

			<div class="buttons is-centered">
				<button class="button is-success" onclick="saveChanges()"><i class="fas fa-floppy-disk"></i> Save changes</button>
				<button class="button backgroundDark" onclick="document.getElementById('overlayFiles').style.display= 'block';"><i class="fas fa-images"></i> Images manager</button>
			</div>

			<?php if ($printingAuto == 1) { ?>
				<div class="notification is-warning">The <b>automatic printing mode is active</b>, these settings will only apply to the receipts <b>sent by e-mail</b>.</div>
			<?php } ?>

			<div class="columns">
				<div class="column">
					<label class="label">Gift ticket design</label>
					<div style="width: 100%;"><textarea id="editorTicket"><?php echo $changeTicketsTemplate; ?></textarea></div>
				</div>

				<div class="column">
					<label class="label">Number of tickets to print by default (max. 5)</label>
					<div class="field">
						<div class="control has-icons-left">
							<input type="number" class="input" placeholder="e.g. 1, 5" id="numberTicketsPrint" max="5" value="<?php echo $changeTicketsDefaultPrint; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>
					</div>

					<label class="label">Validity to make product changes (in days)</label>
					<div class="field">
						<div class="control has-icons-left">
							<input type="number" class="input" placeholder="e.g. 1, 5" id="numberTicketsExpireDays" value="<?php echo $changeTicketsExpireDays; ?>">
							<span class="icon is-small is-left"><i class="fas fa-calendar"></i></span>
						</div>
					</div>
					<p class="is-size-7"><b>Example</b>: If you write 30 (days) in the field above and the purchase was made on <b>November 1st</b>, the gift ticket will show the validity until <b>December 1st</b></p>
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

	<form method="POST" action="/trv/admin/include/edit-change-tickets.php" style="display: none" id="saveConfigForm" onsubmit="return saveConfigReturn();">
		<input name="saveConfigTemplate" id="saveConfigTemplate" readonly>
		<input name="saveConfigCopies" id="saveConfigCopies" readonly>
		<input name="saveConfigExpire" id="saveConfigExpire" readonly>
		<input type="submit" id="saveConfigSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var toolbarBtns = "undo redo | formatselect | bold italic underline strikethrough backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | image table | hr | trv_vars template | preview | code";

		tinymce.init({
			selector: "#editorTicket",
			plugins: "preview paste save code template table hr advlist image lists wordcount",
			menubar: "",
			toolbar: toolbarBtns,
			toolbar_sticky: true,
			templates: [{
				title: "General",
				description: "General template, 100% customizable",
				content: '<h2 style="text-align: center;">Gift Ticket</h2><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Date and time of purchase</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Receipt #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Attended by</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><p>{{trv_products}}</p>'
			}],
			height: 600,
			toolbar_mode: "wrap",
			language: "en",
			relative_urls: false,
			placeholder: "Design the gift ticket using this editor. Remember to use the variables.",
			setup: function(editor) {
				editor.ui.registry.addMenuButton("trv_vars", {
					text: "Variable",
					fetch: function(callback) {
						var items = [{
								type: "menuitem",
								text: "Date and time of purchase (e.g. 01/01/2023 9:00 am)",
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
								text: "List of products without price (text, list)",
								onAction: function() {
									editor.insertContent("{{trv_products}}");
								}
							}
						];
						callback(items);
					}
				});
			}
		});

		function saveChanges() {
			var ticketTemplate = tinymce.get('editorTicket').getContent();
			var ticketCopies = document.getElementById('numberTicketsPrint').value;
			ticketCopies++;
			ticketCopies--;
			var ticketExpireDays = document.getElementById('numberTicketsExpireDays').value;
			ticketExpireDays++;
			ticketExpireDays--;

			if (ticketTemplate == "" || ticketCopies < 0 || ticketCopies > 5 || ticketExpireDays < 0) {
				newNotification("Check the fields", "error");
			} else {
				document.getElementById('saveConfigTemplate').value = ticketTemplate;
				document.getElementById('saveConfigCopies').value = ticketCopies;
				document.getElementById('saveConfigExpire').value = ticketExpireDays;

				document.getElementById('saveConfigSend').click();
				openLoader();
			}
		}

		function saveConfigReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-change-tickets.php',
				data: $('#saveConfigForm').serialize(),
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