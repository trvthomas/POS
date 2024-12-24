<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$errorImagen = false;
$imagenSubida = false;
$totalSubidas = 0;

if (isset($_FILES["image"])) {
	if (count($_FILES['image']['name']) > 0) {
		for ($idImg = 0; $idImg < count($_FILES['image']['name']); $idImg++) {
			$imgName = basename($_FILES["image"]["name"][$idImg]);
			$find = array(" ", "?", "¿", "!",  "¡",  "/", "á", "é", "í", "ó", "ú", "ñ");
			$target_file = date("Ymd-His") . str_replace($find, "", $imgName);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			if (file_exists($target_file)) {
				$uploadOk = 0;
			}
			if ($_FILES["image"]["size"][$idImg] > 4000000) {
				$uploadOk = 0;
			}
			if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
				$uploadOk = 0;
			}

			if ($uploadOk != 0) {
				if (move_uploaded_file($_FILES["image"]["tmp_name"][$idImg], $target_file)) {
					$sql = "INSERT INTO trvsol_admin_images (url, name)
	VALUES ('/trv/media/uploads/" . $target_file . "', '" . $_FILES["image"]["name"][$idImg] . "')";
					if ($conn->query($sql) === TRUE) {
						$imagenSubida = true;
						++$totalSubidas;
					}
				} else {
					$errorImagen = true;
				}
			} else {
				$errorImagen = true;
			}
		}
	}
} else {
	$errorImagen = true;
}

$varsSend = array(
	'error_imagen' => $errorImagen,
	'cantidad_imagenes_subidas' => $totalSubidas,
	'imagen_subida' => $imagenSubida
);
echo json_encode(convertJson($varsSend));
?>