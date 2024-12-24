<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$authorizeEntry = false;

$userName = "";
$userPass = "";
$userID = "";

if (isset($_GET["id"])) {
	$sql = "SELECT * FROM trvsol_users WHERE id=" . $_GET["id"];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$authorizeEntry = true;

		$userName = $row["username"];
		$userPass = $row["password"];
		$userID = $row["id"];
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Edit Seller</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<?php if ($authorizeEntry == true) { ?>
		<div class="contentBox loginBox">
			<div class="box">
				<a class="button is-small is-pulled-left backgroundNormal" href="/trv/admin/users.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

				<h3 class="is-size-5 has-text-centered">Edit Seller</h3>
				<hr><br>

				<div class="field">
					<label class="label has-text-centered">Username*</label>
					<div class="control has-icons-left">
						<input type="text" class="input" placeholder="e.g. Thomas, Anna" id="inputUsername" maxlength="100" value="<?php echo $userName; ?>">
						<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
					</div>
				</div>

				<div class="field">
					<label class="label has-text-centered">PIN</label>
					<div class="control has-icons-left has-icons-right">
						<input type="password" class="input" placeholder="User PIN" id="inputPassword" value="<?php echo $userPass; ?>">
						<span class="icon is-small is-left"><i class="fas fa-key"></i></span>
						<span class="icon is-small is-right" style="pointer-events: all; cursor: pointer;" onclick="showPass('inputPassword')"><i class="fas fa-eye" id="showPassBtninputPassword"></i></span>
					</div>
				</div>
			</div>

			<div class="columns">
				<div class="column">
					<button class="button backgroundDark is-fullwidth" id="buttonPublish" onclick="editUser()">Save changes <i class="fas fa-floppy-disk"></i></button>
				</div>
			</div>
		</div>

		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>

		<form method="POST" action="/trv/admin/include/edit-user.php" style="display: none" id="editUserForm" onsubmit="return editUserReturn();">
			<input name="editUserName" id="editUserName" readonly>
			<input name="editUserPass" id="editUserPass" readonly>
			<input name="editUserId" value="<?php echo $userID; ?>" readonly>

			<input type="submit" id="editUserSend" value="Send">
		</form>

		<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
		<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
		<script>
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

			function editUser() {
				var userName = document.getElementById('inputUsername').value;
				var userPass = document.getElementById('inputPassword').value;

				if (userName == "" || userPass == "") {
					newNotification('Check the fields', 'error');
				} else {
					document.getElementById('editUserName').value = userName;
					document.getElementById('editUserPass').value = userPass;

					document.getElementById('editUserSend').click();
					openLoader();
				}
			}

			function editUserReturn() {
				$.ajax({
					type: 'POST',
					url: '/trv/admin/include/edit-user.php',
					data: $('#editUserForm').serialize(),
					dataType: 'json',
					success: function(response) {
						if (response['errores'] == true) {
							newNotification('There was an error', 'error');
						} else if (response['usuario_editado'] == true) {
							window.location = "/trv/admin/users.php";
						}
						closeLoader();
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