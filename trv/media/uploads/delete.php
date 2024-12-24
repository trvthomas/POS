<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$imgDeleted = false;

if (isset($_POST['deleteImgUrl'])) {
	$imgName = str_replace("/trv/media/uploads/", "", $_POST['deleteImgUrl']);

	if (file_exists($imgName) == 1) {
		unlink($imgName);

		$sql = "DELETE FROM trvsol_admin_images WHERE url= '" . $_POST['deleteImgUrl'] . "'";
		if ($conn->query($sql) === TRUE) {
			$imgDeleted = true;
		} else {
			$existeError = true;
		}
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'foto_eliminada' => $imgDeleted
);
echo json_encode(convertJson($varsSend));
?>