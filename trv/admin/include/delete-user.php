<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$usuarioEliminado = false;
$existeError = false;

if (isset($_POST["userDeleteId"])) {
	$sql = "DELETE FROM trvsol_users WHERE id=" . $_POST["userDeleteId"];
	if ($conn->query($sql) === TRUE) {
		$sql2 = "DELETE FROM trvsol_users_stats WHERE userId=" . $_POST["userDeleteId"];
		$conn->query($sql2);

		$usuarioEliminado = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'usuario_eliminado' => $usuarioEliminado
);
echo json_encode(convertJson($varsSend));
?>