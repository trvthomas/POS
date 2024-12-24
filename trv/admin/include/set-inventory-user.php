<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$establecido = false;

if (isset($_POST["setInventoryUserId"]) && isset($_POST["setInventoryUserAction"])) {
	$sql = "UPDATE trvsol_users SET INVENTORY='" . $_POST["setInventoryUserAction"] . "' WHERE id=" . $_POST["setInventoryUserId"];
	if ($conn->query($sql) === TRUE) {
		$establecido = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'inventario_establecido' => $establecido
);
echo json_encode(convertJson($varsSend));
?>