<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/stats.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Coupons</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<link rel="stylesheet" href="/trv/include/libraries/bulma-list.css">
</head>

<body onload="getInfo()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<div class="columns">
			<div class="column">
				<h3 class="is-size-5">Coupons</h3>
				<p>Engage your customers by creating discount coupons</p>
			</div>

			<div class="column is-one-third">
				<a class="button backgroundDark is-fullwidth" href="/trv/admin/new-voucher.php"><i class="fas fa-circle-plus"></i> New coupon</a>
			</div>
		</div>

		<div class="box">
			<a class="button is-small backgroundNormal" href="/trv/admin/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

			<div class="list has-visible-pointer-controls" id="vouchersList"></div>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

	<form method="POST" action="/trv/admin/include/get-vouchers.php" style="display: none" id="getVouchersForm" onsubmit="return getVouchersReturn();">
		<input name="getVouchersToken" value="admin38942" readonly>
		<input type="submit" id="getVouchersSend" value="Send">
	</form>

	<form method="POST" action="/trv/admin/include/delete-voucher.php" style="display: none" id="voucherDeleteForm" onsubmit="return voucherDelete();">
		<input id="voucherDeleteId" name="voucherDeleteId" readonly>
		<input id="voucherDeleteSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		function getInfo() {
			document.getElementById('vouchersList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';
			document.getElementById('getVouchersSend').click();
		}

		function deleteVoucher(idVoucher) {
			var c = confirm("Are you sure? This action cannot be undone and will delete all coupon statistics");

			if (c == true) {
				document.getElementById('voucherDeleteId').value = idVoucher;
				document.getElementById('voucherDeleteSend').click();

				document.getElementById('btnDeleteVoucher' + idVoucher).disabled = true;
				document.getElementById('btnDeleteVoucher' + idVoucher).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
			}
		}

		function getVouchersReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/get-vouchers.php',
				data: $('#getVouchersForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['vouchers'] != "") {
						document.getElementById('vouchersList').innerHTML = response['vouchers'];
					}
				}
			});

			return false;
		}

		function voucherDelete() {
			$.ajax({
				type: 'POST',
				url: '/trv/admin/include/delete-voucher.php',
				data: $('#voucherDeleteForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['voucher_eliminado'] == true) {
						newNotification('Coupon deleted', 'success');
					}
					getInfo();
				}
			});

			return false;
		}
	</script>
</body>

</html>