<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$categoriaEliminada = false;
$existeError = false;

if (isset($_POST["categoryDeleteId"])) {
	$sql = "DELETE FROM trvsol_categories WHERE id=" . $_POST["categoryDeleteId"];
	if ($conn->query($sql) === TRUE) {
		$sql2 = "UPDATE trvsol_products SET categoryID='1' WHERE categoryID=" . $_POST["categoryDeleteId"];
		$conn->query($sql2);

		$categoriaEliminada = true;
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'categoria_eliminada' => $categoriaEliminada
);
echo json_encode(convertJson($varsSend));
?>