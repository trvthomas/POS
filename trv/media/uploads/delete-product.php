<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$productoEliminado = false;
$existeError = false;

if (isset($_POST["deletePId"])) {
	$sql = "SELECT * FROM trvsol_products WHERE id=" . $_POST["deletePId"];
	$result2 = $conn->query($sql);

	if ($result2->num_rows > 0) {
		$row = $result2->fetch_assoc();
		$imgN1 = str_replace("/trv/media/uploads/", "", $row['imagen']);

		if (file_exists($imgN1) == 1) {
			unlink($imgN1);
		}

		$sql2 = "DELETE FROM trvsol_products WHERE id=" . $_POST["deletePId"];
		if ($conn->query($sql2) === TRUE) {
			$sql3 = "DELETE FROM trvsol_products_stats WHERE productId=" . $_POST["deletePId"];
			$conn->query($sql3);

			$productoEliminado = true;
		} else {
			$existeError = true;
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'producto_eliminado' => $productoEliminado
);
echo json_encode(convertJson($varsSend));
?>