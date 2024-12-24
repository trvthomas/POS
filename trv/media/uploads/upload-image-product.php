<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/convertJson.php";
$urlImg = "";
$errorImagen = false;

if (isset($_FILES["productImage"])) {
	$imgName = basename($_FILES["productImage"]["name"]);
	$find = array(" ", "?", "¿", "!",  "¡",  "/", "á", "é", "í", "ó", "ú", "ñ");
	$target_file = date("Ymd-His") . str_replace($find, "", $imgName);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	$imageDimensions = @getimagesize($_FILES["productImage"]["tmp_name"]);
	$imageWidth = $imageDimensions[0];
	$imageHeight = $imageDimensions[1];

	if (file_exists($target_file)) {
		$uploadOk = 0;
	}
	if ($_FILES["productImage"]["size"] > 4000000) {
		$uploadOk = 0;
	}
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		$uploadOk = 0;
	}
	if ($imageWidth < 500 || $imageHeight < 500 || $imageWidth > 5000 || $imageHeight > 5000) {
		$uploadOk = 0;
	}

	if ($uploadOk != 0) {
		if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
			if ($_POST["uploadImageDeleteURL"] != "") {
				$imgNameDel = str_replace("/trv/media/uploads/", "", $_POST['uploadImageDeleteURL']);
				if (file_exists($imgNameDel) == 1) {
					unlink($imgNameDel);
				}
			}

			$urlImg = "/trv/media/uploads/" . $target_file;
		}
	} else {
		$errorImagen = true;
	}
} else {
	$errorImagen = true;
}

$varsSend = array(
	'error_imagen' => $errorImagen,
	'url_imagen' => $urlImg
);
echo json_encode(convertJson($varsSend));
?>