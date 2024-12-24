<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$configLimiteDescuento = 0;
$configBorrarNum = 0;
$configPrefijo = 0;
$configNumeracion = 0;
$configNombreEmpresa = 0;
$configEmailAdmin = 0;

$configCheckPrecioMenor = 0;
$configCheckNegativeStock = 0;
$configCheckAutoEmail = 0;
$configCheckLowStockNotifications = 0;
$configCheckChangeTickets = 0;
$configCheckNewPaymentMethod = "";
$configCheckTRVCloudService = 0;
$configCheckTRVCloudToken = "ERROR";
$configCheckAutoPrinting = 0;
$configCheckPrinterName = "";
$configInvoicesSavingMonths = 4;

$sql = "SELECT * FROM trvsol_configuration";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "discountLimit") {
			$configLimiteDescuento = $row["value"];
		} else if ($row["configName"] == "deleteInvoiceNumbersAuto") {
			$configBorrarNum = $row["value"];
		} else if ($row["configName"] == "prefixNumInvoice") {
			$configPrefijo = $row["value"];
		} else if ($row["configName"] == "numInvoice") {
			$configNumeracion = $row["value"];
		} else if ($row["configName"] == "businessName") {
			$configNombreEmpresa = $row["value"];
		} else if ($row["configName"] == "adminEmail") {
			$configEmailAdmin = $row["value"];
		} else if ($row["configName"] == "changePriceLessOriginal") {
			$configCheckPrecioMenor = $row["value"];
		} else if ($row["configName"] == "allowNegativeInventory") {
			$configCheckNegativeStock = $row["value"];
		} else if ($row["configName"] == "sendAutoReports") {
			$configCheckAutoEmail = $row["value"];
		} else if ($row["configName"] == "newPaymentMethod") {
			$configCheckNewPaymentMethod = $row["value"];
		} else if ($row["configName"] == "lowStockNotification") {
			$configCheckLowStockNotifications = $row["value"];
		} else if ($row["configName"] == "changeTickets") {
			$configCheckChangeTickets = $row["value"];
		} else if ($row["configName"] == "trvCloudActive") {
			$configCheckTRVCloudService = $row["value"];
		} else if ($row["configName"] == "trvCloudToken") {
			$configCheckTRVCloudToken = $row["value"];
		} else if ($row["configName"] == "printingAuto") {
			$configCheckAutoPrinting = $row["value"];
		} else if ($row["configName"] == "printingAutoPrinterName") {
			$configCheckPrinterName = $row["value"];
		} else if ($row["configName"] == "saveInvoicesForMonths") {
			$configInvoicesSavingMonths = $row["value"];
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Settings</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Settings</h3>
		<p>Customize the behavior of your POS system to suit your business</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="columns">
				<div class="column">
					<h4 class="is-size-5 has-text-info">General</h4>

					<label class="label">Discount limit that the seller can apply (enter 0 to disable)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 50, 300" id="discountLimit" value="<?php echo $configLimiteDescuento; ?>">
							<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('discountLimit', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="changePriceLessOriginal" onclick="modifySetting('changePriceLessOriginal', true)" <?php if ($configCheckPrecioMenor == 1) {
																																								echo "checked";
																																							} ?>>
						<label class="label" for="changePriceLessOriginal">Do not allow changing the price of a product to a lower value</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="allowNegativeInventory" onclick="modifySetting('allowNegativeInventory', true)" <?php if ($configCheckNegativeStock == 1) {
																																								echo "checked";
																																							} ?>>
						<label class="label" for="allowNegativeInventory">Allow sale of items with negative stock</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="sendAutoReports" onclick="modifySetting('sendAutoReports', true)" <?php if ($configCheckAutoEmail == 1) {
																																				echo "checked";
																																			} ?>>
						<label class="label" for="sendAutoReports">Send day summaries by email automatically when the shift is ended</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="lowStockNotification" onclick="modifySetting('lowStockNotification', true)" <?php if ($configCheckLowStockNotifications == 1) {
																																							echo "checked";
																																						} ?>>
						<label class="label" for="lowStockNotification">Send daily notifications about low stock items via email</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="changeTickets" onclick="modifySetting('changeTickets', true)" <?php if ($configCheckChangeTickets == 1) {
																																			echo "checked";
																																		} ?>>
						<label class="label" for="changeTickets">Enable gift tickets</label>
					</div>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="newPaymentMethodCheck" onclick="newPaymentMethodCheck()" <?php if ($configCheckNewPaymentMethod != "") {
																																		echo "checked";
																																	} ?>>
						<label class="label" for="newPaymentMethodCheck">Custom payment method</label>
					</div>

					<div class="fade" id="newPaymentMethodDiv" style="<?php if ($configCheckNewPaymentMethod == "") {
																			echo "display: none";
																		} ?>">
						<label class="label">Payment method name</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. Digital wallet, PayPal, Check" id="newPaymentMethod" maxlength="20" value="<?php echo $configCheckNewPaymentMethod; ?>">
								<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('newPaymentMethod', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>

					<h4 class="is-size-5 has-text-info">Printing</h4>
					<div class="field">
						<input type="checkbox" class="is-checkradio" id="printingAuto" onclick="modifySetting('printingAuto', true); if(this.checked == true){ document.getElementById('btnAutoPrinting').style.display= 'block'; }else{ document.getElementById('btnAutoPrinting').style.display= 'none'; }" <?php if ($configCheckAutoPrinting == 1) {
																																																																													echo "checked";
																																																																												} ?>>
						<label class="label" for="printingAuto">Automatic Printing</label>
					</div>

					<div id="btnAutoPrinting" style="<?php if ($configCheckAutoPrinting == 0) {
															echo "display: none";
														} ?>">
						<div class="notification is-info">
							Automatic printing will only work with thermal printers connected via USB cable, installed with the driver <b>"Generic / Text Only,"</b> and shared.
						</div>

						<label class="label">Shared printer name</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="Enter the exact name of the shared printer" id="printingAutoPrinterName" maxlength="100" value="<?php echo $configCheckPrinterName; ?>">
								<span class="icon is-small is-left"><i class="fas fa-print"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('printingAutoPrinterName', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>

						<a class="button backgroundDark is-fullwidth fade" href="/trv/admin/invoices-design-auto.php">Print settings</a>
					</div>
				</div>

				<div class="column">
					<h4 class="is-size-5 has-text-info">Sales Receipts</h4>

					<label class="label">Delete numbering automatically (leave empty to disable)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 100, 250" id="deleteInvoiceNumbersAuto" value="<?php echo $configBorrarNum; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('deleteInvoiceNumbersAuto', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Add prefix to numbering (leave empty to disable)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="e.g. TRV, INV" id="prefixNumInvoice" maxlength="6" value="<?php echo $configPrefijo; ?>">
							<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('prefixNumInvoice', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Current numbering</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 100, 250" id="numInvoice" value="<?php echo $configNumeracion; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('numInvoice', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>

					<label class="label">Receipt saving period (in months)</label>
					<div class="field has-addons">
						<div class="control has-icons-left is-expanded">
							<input type="number" class="input" placeholder="e.g. 1, 4, 10" id="invoicesSaving" value="<?php echo $configInvoicesSavingMonths; ?>">
							<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
						</div>

						<div class="control">
							<button class="button backgroundDark" onclick="modifySetting('invoicesSaving', false)"><i class="fas fa-circle-check"></i></button>
						</div>
					</div>
					<p class="is-size-7">The longer the saving period, the more information will be retained, which may slow down the system and the computer.</p>

					<h4 class="is-size-5 has-text-info">Cloud Syncing</h4>

					<div class="field">
						<input type="checkbox" class="is-checkradio" id="trvCloudActive" onclick="trvCloudActive()" <?php if ($configCheckTRVCloudService == 1) {
																														echo "checked";
																													} ?>>
						<label class="label" for="trvCloudActive">Enable cloud syncing</label>
					</div>

					<div class="fade has-text-centered" id="trvCloudInfoDiv" style="<?php if ($configCheckTRVCloudService != 1) {
																						echo "display: none";
																					} ?>">
						<p>To configure and associate this device with the cloud service, <b>go to <a href="https://www.trvsolutions.com/pos" target="_blank">www.trvsolutions.com/pos</a></b> and create an account or log in.
							<br>Then click on the <b>"Associate new system"</b> button and scan the following QR code.
						</p>

						<img src="https://barcode.tec-it.com/barcode.ashx?code=QRCode&data=<?php echo $configCheckTRVCloudToken; ?>&dpi=500" style="width: 120px;margin-bottom: 0;">
						<p><?php echo $configCheckTRVCloudToken; ?></p>
					</div>
				</div>
			</div>

			<div class="has-text-centered">
				<h4 class="is-size-5 has-text-info">Business Information</h4>

				<div class="columns">
					<div class="column">
						<label class="label">Business name</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. TRV Solutions" id="businessName" value="<?php echo $configNombreEmpresa; ?>">
								<span class="icon is-small is-left"><i class="fas fa-store"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('businessName', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>

					<div class="column">
						<label class="label">Administrator email</label>
						<div class="field has-addons">
							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="e.g. jhondoe@gmail.com" id="adminEmail" value="<?php echo $configEmailAdmin; ?>">
								<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
							</div>

							<div class="control">
								<button class="button backgroundDark" onclick="modifySetting('adminEmail', false)"><i class="fas fa-circle-check"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayTermsCloudService" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayTermsCloudService').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Service Discontinued</h3>
			</div>

			<div class="trvModal-elements">
				<p>Thank you for your interest in using this service. Unfortunately, cloud syncing is now discontinued and no longer works for this version.
					<br>We greatly appreciate your support and hope to bring you new solutions soon.
				</p>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="document.getElementById('overlayTermsCloudService').style.display='none'">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/edit-configuration.php" style="display: none" id="editConfigForm" onsubmit="return editConfigReturn();">
		<input name="editConfigId" id="editConfigId" readonly>
		<input name="editConfigValue" id="editConfigValue" readonly>
		<input type="submit" id="editConfigSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/cloudService.js"></script>
	<script>
		function modifySetting(idSetting, isCheckbox) {
			var valueConfig = 0;

			if (isCheckbox == true) {
				valueConfig = document.getElementById(idSetting).checked;
				if (valueConfig == true) {
					valueConfig = 1;
				} else {
					valueConfig = 0;
				}
			} else {
				valueConfig = document.getElementById(idSetting).value;
			}

			if (idSetting == "numInvoice") {
				valueConfig++;
				valueConfig--;
			}
			if (idSetting == "numInvoice" && valueConfig < 0) {
				newNotification('The sales numbering is incorrect', 'error');
			} else {
				document.getElementById('editConfigId').value = idSetting;
				document.getElementById('editConfigValue').value = valueConfig;

				document.getElementById('editConfigSend').click();
				openLoader();
			}
		}

		function newPaymentMethodCheck() {
			var checkboxPayment = document.getElementById('newPaymentMethodCheck').checked;

			if (checkboxPayment == true) {
				document.getElementById('newPaymentMethodDiv').style.display = 'block';
				newNotification('Enter the name of the payment method', 'success');
			} else {
				document.getElementById('newPaymentMethodDiv').style.display = 'none';
				document.getElementById('newPaymentMethod').value = '';
				modifySetting('newPaymentMethod', false);
			}
		}

		function trvCloudActive() {
			var checkboxTRVCloud = document.getElementById('trvCloudActive').checked;

			if (checkboxTRVCloud == true) {
				document.getElementById('overlayTermsCloudService').style.display = 'block';
			} else {
				document.getElementById('trvCloudInfoDiv').style.display = 'none';
				modifySetting('trvCloudActive', false);
			}
		}

		function editConfigReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/edit-configuration.php',
				data: $('#editConfigForm').serialize(),
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