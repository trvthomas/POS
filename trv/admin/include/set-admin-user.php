<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$establecido = false;

if (isset($_POST["setadminUserId"]) && isset($_POST["setadminUserAction"])) {
	$sql = "UPDATE trvsol_users SET admin='" . $_POST["setadminUserAction"] . "' WHERE id=" . $_POST["setadminUserId"];
	if ($conn->query($sql) === TRUE) {
		$establecido = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'administrador_establecido' => $establecido
);
echo json_encode(convertJson($varsSend));
?>