<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$authorizeEntry = false;

$voucherCode = "";
$voucherAvailable = "";
$voucherMinimum = "";
$voucherValue = "";
$voucherExpiration = "";
$voucherPayment = "";
$voucherID = "";

$metodoPagoPersonalizado = "";

if (isset($_GET["id"])) {
	$sql = "SELECT * FROM trvsol_vouchers WHERE id=" . $_GET["id"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$authorizeEntry = true;

		$voucherCode = $row["code"];
		$voucherAvailable = $row["totalAvailable"];
		$voucherMinimum = $row["minimumQuantity"];
		$voucherValue = $row["value"];
		$voucherExpiration = $row["expiration"];
		$voucherPayment = $row["paymentMethods"];
		$voucherID = $row["id"];
	}

	$sql2 = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
	$result2 = $conn->query($sql2);
	if ($result2->num_rows > 0) {
		$row2 = $result2->fetch_assoc();

		$metodoPagoPersonalizado = $row2["value"];
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Edit Coupon</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-steps.min.css">
	<link rel="stylesheet" href="/trv/include/libraries/flatpickr.min.css">
	<script src="/trv/include/libraries/flatpickr.js"></script>
	<script src="/trv/include/libraries/flatpickr-es.js"></script>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body onload="startCreation()">
	<?php include_once "include/header.php"; ?>

	<?php if ($authorizeEntry == true) { ?>
		<div class="contentBox">
			<div class="box">
				<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/vouchers.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>
				<ul class="steps has-content-centered has-gaps" style="margin-bottom: 0;" id="progressBarDiv"></ul>

				<div class="fade" id="step1">
					<h3 class="is-size-5 has-text-centered">General information</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Coupon code*</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. 10OFF, SUMMERSALE" id="inputVoucherCode" onkeyup="this.value = this.value.toUpperCase();" value="<?php echo $voucherCode; ?>">
									<span class="icon is-small is-left"><i class="fas fa-tag"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Discount percentage*</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 10, 50" id="inputVoucherValue" value="<?php echo $voucherValue; ?>">
									<span class="icon is-small is-left"><i class="fas fa-percent"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="fade" id="step2" style="display: none">
					<h3 class="is-size-5 has-text-centered">General settings</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Available quantity for use*</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 10, 50" id="inputVoucherAvailable" value="<?php echo $voucherAvailable; ?>">
									<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Minimum amount to redeem the coupon</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50, 300" id="inputVoucherMinimum" value="<?php echo $voucherMinimum; ?>">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="fade" id="step3" style="display: none">
					<h3 class="is-size-5 has-text-centered">Advanced settings</h3>
					<hr><br>

					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Validity, valid until*</label>
								<div class="control has-icons-left">
									<input type="date" class="input" id="inputVoucherExpiration" value="<?php echo $voucherExpiration; ?>">
									<span class="icon is-small is-left"><i class="fas fa-calendar-day"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<label class="label">Enabled payment methods to use the coupon</label>

							<div class="field">
								<input type="checkbox" class="is-checkradio" id="voucherPaymentsCash" <?php if (strpos($voucherPayment, "a:E:p")) {
																											echo "checked";
																										} ?>>
								<label class="label" for="voucherPaymentsCash">Cash</label>
							</div>

							<div class="field">
								<input type="checkbox" class="is-checkradio" id="voucherPaymentsCard" <?php if (strpos($voucherPayment, "a:T:p")) {
																											echo "checked";
																										} ?>>
								<label class="label" for="voucherPaymentsCard">Card</label>
							</div>

							<div class="field">
								<input type="checkbox" class="is-checkradio" id="voucherPaymentsMulti" <?php if (strpos($voucherPayment, "a:M:p")) {
																											echo "checked";
																										} ?>>
								<label class="label" for="voucherPaymentsMulti">Multipayment</label>
							</div>

							<div class="field" <?php if ($metodoPagoPersonalizado == "") {
													echo 'style= "display: none"';
												} ?>>
								<input type="checkbox" class="is-checkradio" id="voucherPaymentsOther" <?php if (strpos($voucherPayment, "a:O:p")) {
																											echo "checked";
																										} ?>>
								<label class="label" for="voucherPaymentsOther"><?php echo $metodoPagoPersonalizado; ?></label>
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
					<button class="button backgroundDark is-fullwidth is-hidden" id="buttonPublish" onclick="addVoucher()">Add <i class="fas fa-circle-plus"></i></button>
				</div>
			</div>
		</div>

		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

		<form method="POST" action="/trv/admin/include/edit-voucher.php" style="display: none" id="editVoucherForm" onsubmit="return editVoucherReturn();">
			<input name="editVoucherCode" id="editVoucherCode" readonly>
			<input name="editVoucherAvailable" id="editVoucherAvailable" readonly>
			<input name="editVoucherMinimum" id="editVoucherMinimum" readonly>
			<input name="editVoucherValue" id="editVoucherValue" readonly>
			<input name="editVoucherExpiration" id="editVoucherExpiration" readonly>
			<input name="editVoucherPayment" id="editVoucherPayment" readonly>
			<input name="editVoucherID" value="<?php echo $voucherID; ?>" readonly>

			<input type="submit" id="editVoucherSend" value="Send">
		</form>

		<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
		<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
		<script defer type="text/javascript" src="/trv/include/create-element.js"></script>
		<script>
			function startCreation() {
				createProgressBar(true, JSON.stringify([{
						icon: "circle-info",
						title: "General info."
					},
					{
						icon: "gear",
						title: "Settings"
					},
					{
						icon: "calendar-day",
						title: "Advanced"
					}
				]));

				flatpickrCalendars = flatpickr("#inputVoucherExpiration", {
					altInput: true,
					locale: "en",
					dateFormat: "Y-m-d",
					minDate: '<?php echo date("Y-m-d"); ?>'
				});
			}

			function editVoucher() {
				var voucherCode = document.getElementById('inputVoucherCode').value;
				var voucherAvailable = document.getElementById('inputVoucherAvailable').value;
				var voucherMinimum = document.getElementById('inputVoucherMinimum').value;
				var voucherValue = document.getElementById('inputVoucherValue').value;
				var voucherExpiration = document.getElementById('inputVoucherExpiration').value;
				var voucherPaymentCash = document.getElementById('voucherPaymentsCash').checked;
				var voucherPaymentCard = document.getElementById('voucherPaymentsCard').checked;
				var voucherPaymentMulti = document.getElementById('voucherPaymentsMulti').checked;
				var voucherPaymentOther = document.getElementById('voucherPaymentsOther').checked;
				voucherAvailable++;
				voucherAvailable--;
				voucherMinimum++;
				voucherMinimum--;

				if (voucherCode == "" || voucherAvailable <= 0 || voucherMinimum < 0 || voucherValue == "" || voucherExpiration == "" || (voucherPaymentCash == false && voucherPaymentCard == false && voucherPaymentMulti == false && voucherPaymentOther == false)) {
					newNotification('Complete and verify all fields', 'error');
				} else {
					var paymentsAccepted = "";
					if (voucherPaymentCash == true) {
						paymentsAccepted += " a:E:p ";
					}
					if (voucherPaymentCard == true) {
						paymentsAccepted += " a:T:p ";
					}
					if (voucherPaymentMulti == true) {
						paymentsAccepted += " a:M:p ";
					}
					if (voucherPaymentOther == true) {
						paymentsAccepted += " a:O:p";
					}

					document.getElementById('editVoucherCode').value = voucherCode;
					document.getElementById('editVoucherAvailable').value = voucherAvailable;
					document.getElementById('editVoucherMinimum').value = voucherMinimum;
					document.getElementById('editVoucherValue').value = voucherValue;
					document.getElementById('editVoucherExpiration').value = voucherExpiration;
					document.getElementById('editVoucherPayment').value = paymentsAccepted;

					document.getElementById('editVoucherSend').click();
					openLoader();
				}
			}

			function editVoucherReturn() {
				$.ajax({
					type: 'POST',
					url: '/trv/admin/include/edit-voucher.php',
					data: $('#editVoucherForm').serialize(),
					dataType: 'json',
					success: function(response) {
						console.log(response);
						if (response['errores'] == true) {
							newNotification('There was an error', 'error');
							closeLoader();
						} else if (response['codigo_existe'] == true) {
							newNotification('The code is already in use', 'error');
							closeLoader();
						} else if (response['voucher_editado'] == true) {
							window.location = "/trv/admin/vouchers.php";
						}
					}
				});

				return false;
			}
		</script>
	<?php } else { ?>
		<h1 class="is-size-1 has-text-centered">There was an error</h1>
	<?php } ?>
</body>

</html>