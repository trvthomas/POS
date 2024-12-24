<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$productoOculto = false;
$existeError = false;

if (isset($_POST["hidePId"]) && isset($_POST["hidePAction"])) {
	$sql2 = "UPDATE trvsol_products SET activo=" . $_POST["hidePAction"] . " WHERE id=" . $_POST["hidePId"];
	if ($conn->query($sql2) === TRUE) {
		$productoOculto = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'producto_oculto_mostrado' => $productoOculto
);
echo json_encode(convertJson($varsSend));
?>