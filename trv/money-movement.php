<?php include_once "include/verifySession.php";

$metodoPagoPersonalizado = "";

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'newPaymentMethod'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	$metodoPagoPersonalizado = $row["value"];
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Cash adjustment</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Cash adjustment</h3>
		<p>Record cash inflows or outflows for items such as payment to suppliers, exchange surpluses, among others</p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div id="movementMethodSelect">
				<p class="has-text-centered"><b>Select the payment method with which you want to make the adjustment</b></p>

				<div class="columns is-multiline is-centered has-text-centered">
					<div class="column is-one-third">
						<div class="box is-shadowless is-clickable pastel-bg-green" onclick="cashMovementSelect('E')">
							<span class="icon is-large"><i class="fas fa-coins fa-2x"></i></span>
							<p><b>Cash</b></p>
						</div>
					</div>

					<div class="column is-one-third">
						<div class="box is-shadowless is-clickable pastel-bg-purple" onclick="cashMovementSelect('T')">
							<span class="icon is-large"><i class="fas fa-credit-card fa-2x"></i></span>
							<p><b>Card</b></p>
						</div>
					</div>

					<?php if ($metodoPagoPersonalizado != "") { ?>
						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable pastel-bg-cyan" onclick="cashMovementSelect('O')">
								<span class="icon is-large"><i class="fas fa-wallet fa-2x"></i></span>
								<p><b><?php echo $metodoPagoPersonalizado; ?></b></p>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="has-text-centered" id="movementMethodActions" style="display: none;">
				<div class="tabs is-centered is-boxed">
					<ul>
						<li id="tab1" onclick="selectTab(1)" class="is-active"><a><span class="icon is-small"><i class="fas fa-sign-in-alt"></i></span><span>Inflow</span></a></li>
						<li id="tab2" onclick="selectTab(2)"><a><span class="icon is-small"><i class="fas fa-sign-out-alt"></i></span><span>Outflow</span></a></li>
					</ul>
				</div>

				<div class="fade" id="divTab1">
					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Inflow value</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50, 300" id="entradaDineroInput" min="0" oninput="document.getElementById('valorEntradaVerify').innerHTML= thousands_separators(this.value);">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="level">
								<div class="level-item has-text-centered">
									<div>
										<p class="is-size-4">Confirm the value</p>
										<p class="is-size-2">$<span id="valorEntradaVerify">0</span></p>
									</div>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Concept</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. Exchange surplus" id="entradaComentarioInput" maxlength="100">
									<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
								</div>
							</div>
						</div>
					</div>

					<div class="field">
						<div class="control"><button class="button backgroundDark is-fullwidth" onclick="registrar('ENTER')"><i class="fas fa-sign-in-alt"></i> Register inflow</button></div>
					</div>
				</div>

				<div class="fade" id="divTab2" style="display: none">
					<div class="columns">
						<div class="column">
							<div class="field">
								<label class="label">Outflow value</label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50, 300" id="salidaDineroInput" min="0" oninput="document.getElementById('valorSalidaVerify').innerHTML= thousands_separators(this.value);">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="level">
								<div class="level-item has-text-centered">
									<div>
										<p class="is-size-4">Confirm the value</p>
										<p class="is-size-2">$<span id="valorSalidaVerify">0</span></p>
									</div>
								</div>
							</div>
						</div>

						<div class="column">
							<div class="field">
								<label class="label">Concept</label>
								<div class="control has-icons-left">
									<input type="text" class="input" placeholder="e.g. Payment to supplier" id="salidaComentarioInput" maxlength="100">
									<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
								</div>
							</div>
						</div>
					</div>

					<div class="field">
						<div class="control"><button class="button backgroundDark is-fullwidth" onclick="registrar('EXIT')"><i class="fas fa-sign-out-alt"></i> Register outflow</button></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form method="POST" action="/trv/include/new-money-movement.php" style="display: none" id="newMovementForm" onsubmit="return newMovementReturn();">
		<input name="newMovementPayment" id="newMovementPayment" readonly>
		<input name="newMovementType" id="newMovementType" readonly>
		<input name="newMovementValue" id="newMovementValue" readonly>
		<input name="newMovementDescription" id="newMovementDescription" readonly>
		<input name="newMovementOtherName" value="<?php echo $metodoPagoPersonalizado; ?>" readonly>
		<input type="submit" id="newMovementSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		var cashMovementMethod = "";

		function cashMovementSelect(paymentMethod) {
			cashMovementMethod = paymentMethod;
			document.getElementById('movementMethodActions').style.display = 'block';
			document.getElementById('movementMethodSelect').style.display = 'none';
		}

		function selectTab(idTab) {
			for (var x = 1; x <= 2; x++) {
				document.getElementById('divTab' + x).style.display = 'none';
				document.getElementById('tab' + x).classList.remove('is-active');
			}
			document.getElementById('divTab' + idTab).style.display = 'block';
			document.getElementById('tab' + idTab).classList.add('is-active');
		}

		function registrar(entryType) {
			var valor = 0,
				concepto;
			if (entryType == "ENTER") {
				var valor = document.getElementById('entradaDineroInput').value;
				var concepto = document.getElementById('entradaComentarioInput').value;
			} else if (entryType == "EXIT") {
				var valor = document.getElementById('salidaDineroInput').value;
				var concepto = document.getElementById('salidaComentarioInput').value;
			}
			valor++;
			valor--;

			if (valor <= 0) {
				newNotification('Check the value', 'error');
			} else {
				document.getElementById('newMovementPayment').value = cashMovementMethod;
				document.getElementById('newMovementType').value = entryType;
				document.getElementById('newMovementValue').value = valor;
				document.getElementById('newMovementDescription').value = concepto;
				document.getElementById('newMovementSend').click();

				openLoader();
			}
		}

		function newMovementReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/new-money-movement.php',
				data: $('#newMovementForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else {
						newNotification("Adjustment registered", "success");
						window.location = "/trv/reports.php";
					}
				}
			});

			return false;
		}
	</script>
</body>

</html>