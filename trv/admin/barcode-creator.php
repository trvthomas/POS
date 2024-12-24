<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Barcode Generator</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-checkradio.min.css">
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Barcode Generator</h3>
		<p>Generate, print, and download barcodes and QR codes quickly and easily with personalized information</p>

		<div class="box">
			<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<p class="has-text-centered">Select the code type, then enter the value in the box and click <b>Generate</b></p>

			<div class="columns has-text-centered">
				<div class="column">
					<div class="field">
						<label class="label">Code type</label>
						<div class="control has-icons-left">
							<span class="select is-fullwidth">
								<select id="selectCodeType">
									<option value="Code128">Code - 128</option>
									<option value="EAN8">EAN - 8</option>
									<option value="EAN13">EAN - 13</option>
									<option value="QRCode">QR COde</option>
								</select>
							</span>

							<span class="icon is-small is-left"><i class="fas fa-barcode"></i></span>
						</div>
					</div>

					<button class="button backgroundDark is-fullwidth" onclick="document.getElementById('overlayTemplateConfig').style.display= 'block';"><i class="fas fa-print"></i> Generate code template</button>
				</div>

				<div class="column">
					<div class="field">
						<label class="label">Code value</label>
						<div class="control has-icons-left is-expanded">
							<input type="text" class="input" placeholder="e.g. Text, link or URL" id="barrasInput" onkeydown="onup()">
							<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
						</div>
					</div>

					<div class="buttons is-centered">
						<button class="button backgroundDark is-fullwidth" onclick="generateCode()"><i class="fas fa-barcode"></i> Generate</button>
						<button class="button backgroundDark is-fullwidth" onclick="generateRandom()"><i class="fas fa-shuffle"></i> Generate random</button>
					</div>
				</div>
			</div>

			<div class="has-text-centered" id="generatedCode" style="display: none">
				<div id="printCode" class="barcodeBox">
					<img alt="Código de barras" id="imgCode" style="width: 200px;">
				</div>

				<p><b>Right-click</b> > <b>"Save image as..."</b> to save the code to your computer</p>
				<button class="button backgroundDark" onclick="printCode('printCode')"><i class="fas fa-print"></i> Print</button>
			</div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<div id="overlayTemplateConfig" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayTemplateConfig').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Configure barcode template</h3>
			</div>

			<div class="trvModal-elements">
				<p>This option allows you to print a template with all the <b>barcodes of your active products</b>.
					<br>Configure the template below:
				</p>

				<div class="columns has-text-centered">
					<div class="column">
						<div class="field">
							<label class="label">Template layout</label>
							<div class="control has-icons-left">
								<span class="select is-fullwidth">
									<select id="generateTemplateType" oninput="showDescriptionTemplate()">
										<option value="" selected disabled>Select</option>
										<option value="simple">Simple</option>
										<option value="detailed">Detailed</option>
										<option value="detailed2">Detailed with price</option>
									</select>
								</span>

								<span class="icon is-small is-left"><i class="fas fa-table-cells-large"></i></span>
							</div>
						</div>

						<p id="descriptionTemplate" style="margin-top: 2px;"></p>
					</div>

					<div class="column">
						<div class="field">
							<input type="checkbox" class="is-checkradio" id="generateTemplateShowBusinessName">
							<label class="label" for="generateTemplateShowBusinessName">Show business name as title</label>
						</div>

						<div class="field">
							<input type="checkbox" class="is-checkradio" id="generateTemplateShowDateTime">
							<label class="label" for="generateTemplateShowDateTime">Show creation date and time</label>
						</div>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayTemplateConfig').style.display='none'">Cancel</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="generateTemplate()">Generate</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlayTemplatePreview" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlayTemplatePreview').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Preview</h3>
			</div>

			<div class="trvModal-elements">
				<p>The printed result may vary from what is shown in this preview</p>

				<div id="barcodesTemplate" class="invoiceStyle mt-2" style="width: 90%;height: 500px;margin: auto;overflow: auto;"></div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="printCode('barcodesTemplate')">Print</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/admin/include/get-barcode-template.php" style="display: none" id="getTemplateForm" onsubmit="return getTemplateReturn();">
		<input name="getTemplateType" id="getTemplateType" readonly>
		<input name="getTemplateShowBusiness" id="getTemplateShowBusiness" readonly>
		<input name="getTemplateShowTime" id="getTemplateShowTime" readonly>
		<input type="submit" id="getTemplateSend" value="Send">
	</form>

	<form method="POST" action="/trv/admin/include/generate-pdf-barcode.php" style="display: none">
		<textarea name="getPDFTemplate" id="getPDFTemplate" readonly></textarea>
		<input type="submit" id="getPDFSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function generateCode() {
			document.getElementById('generatedCode').style.display = "none";

			var codeType = document.getElementById('selectCodeType').value;
			var valueCode = document.getElementById('barrasInput').value;

			if (valueCode == "") {
				newNotification('Enter the code value', 'error');
			} else if (navigator.onLine == false) {
				newNotification('You need an internet connection', 'error');
			} else {
				if (codeType == 'EAN8' && (valueCode.length > 7 || valueCode.length < 7)) {
					newNotification('Use exactly 7 characters', 'error');
				} else if (codeType == 'EAN13' && (valueCode.length > 12 || valueCode.length < 12)) {
					newNotification('Use exactly 12 characters', 'error');
				} else {
					openLoader();

					document.getElementById('imgCode').src = 'https://barcode.tec-it.com/barcode.ashx?code=' + codeType + '&data=' + valueCode + '&dpi=500';
					generateCodeShow();
				}
			}
		}

		function generateCodeShow() {
			document.getElementById('imgCode').onload = function() {
				document.getElementById('generatedCode').style.display = "block";
				closeLoader();
			}
		}

		function generateRandom() {
			var codeType = document.getElementById('selectCodeType').value;

			if (codeType == 'Code128' || codeType == 'QRCode') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 10000000001);
			} else if (codeType == 'EAN8') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 100000) + 999999;
			} else if (codeType == 'EAN13') {
				document.getElementById("barrasInput").value = Math.floor(Math.random() * 10000000000) + 99999999999;
			}
			generateCode();
		}

		function printCode(idPrinting) {
			var restorePage = document.body.innerHTML;
			var printContent = document.getElementById(idPrinting).innerHTML;
			document.body.innerHTML = printContent;
			window.print();
			document.body.innerHTML = restorePage;
			closeLoader();
		}

		function onup() {
			if (event.keyCode === 13) {
				generateCode();
			}
		}

		function showDescriptionTemplate() {
			var typeTemp = document.getElementById('generateTemplateType').value;

			if (typeTemp == "simple") {
				document.getElementById('descriptionTemplate').innerHTML = "Prints only the codes in <b>Code-128</b> format";
			} else if (typeTemp == "detailed") {
				document.getElementById('descriptionTemplate').innerHTML = "Prints the codes in <b>Code-128</b> format along with the product name";
			} else if (typeTemp == "detailed2") {
				document.getElementById('descriptionTemplate').innerHTML = "Prints the codes in <b>Code-128</b> format along with the product name and selling price";
			}
		}

		function generateTemplate() {
			var typeTemp = document.getElementById('generateTemplateType').value;
			var showBusiness = document.getElementById('generateTemplateShowBusinessName').checked;
			var showTime = document.getElementById('generateTemplateShowDateTime').checked;

			if (typeTemp == "") {
				newNotification('Select the template layout', 'error');
			} else {
				document.getElementById("getTemplateType").value = typeTemp;
				document.getElementById("getTemplateShowBusiness").value = showBusiness;
				document.getElementById("getTemplateShowTime").value = showTime;

				document.getElementById("getTemplateSend").click();
				openLoader();
				document.getElementById('overlayTemplateConfig').style.display = "none";
			}
		}

		function getTemplateReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-barcode-template.php',
				data: $('#getTemplateForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['plantilla_codigos'] != "") {
						document.getElementById('barcodesTemplate').innerHTML = response["plantilla_codigos"];
						document.getElementById('getPDFTemplate').value = response["plantilla_codigos_pdf"];
						document.getElementById('overlayTemplatePreview').style.display = "block";
					}

					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>