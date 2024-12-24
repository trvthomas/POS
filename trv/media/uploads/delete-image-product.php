<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";

$existeError = false;
$imgDeleted = false;

if (isset($_POST['deleteImageURL'])) {
	$imgName = str_replace("/trv/media/uploads/", "", $_POST['deleteImageURL']);

	if (file_exists($imgName)) {
		unlink($imgName);
	}
	$imgDeleted = true;
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'img_deleted' => $imgDeleted
);
echo json_encode(convertJson($varsSend));
?>