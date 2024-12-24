<?php include_once "include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Reports</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onload="getReports()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<h3 class="is-size-5">Informes</h3>
		<p>Create reports to be added to the end-of-the-shift summary, <b>the sales values will not be modified</b></p>

		<div class="box">
			<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<br><br><label class="label">Add a report</label>
			<div class="field has-addons">
				<div class="control has-icons-left is-expanded">
					<input type="text" class="input" placeholder="e.g. Refund, Payment to supplier" id="informesInput" maxlength="150" autofocus>
					<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
				</div>

				<div class="control">
					<button class="button backgroundDark" onclick="crearInforme()"><i class="fas fa-circle-plus iconInButton"></i> Add</button>
				</div>
			</div>

			<hr>
			<h3 class="is-size-4">Today's reports</h3>
			<div id="informesDiv" style="max-height: 400px;overflow: auto;"></div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<form method="POST" action="/trv/include/get-reports.php" style="display: none" id="getReportsForm" onsubmit="return getReportsReturn();">
		<input name="getReportsID" value="seller189" readonly>
		<input type="submit" id="getReportsSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/new-report.php" style="display: none" id="newReportTextForm" onsubmit="return newReportTextReturn();">
		<input name="newReportText" id="newReportText" readonly>
		<input type="submit" id="newReportTextSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function getReports() {
			document.getElementById('informesDiv').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getReportsSend').click();
		}

		function crearInforme() {
			var informe = document.getElementById('informesInput').value;

			if (informe == "") {
				newNotification('Write the report', 'error');
			} else {
				document.getElementById('newReportText').value = informe;
				document.getElementById('newReportTextSend').click();

				openLoader();
			}
		}

		function getReportsReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/get-reports.php',
				data: $('#getReportsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else {
						document.getElementById('informesDiv').innerHTML = response["reportes"];
					}
				}
			});

			return false;
		}

		function newReportTextReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/new-report.php',
				data: $('#newReportTextForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response["reporte_creado"] == true) {
						newNotification("Report added", "success");
						getReports();

						document.getElementById('informesInput').value = "";
					}

					closeLoader();
				}
			});

			return false;
		}
	</script>
</body>

</html>