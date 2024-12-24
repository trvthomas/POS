<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;

if (isset($_POST["cancelFileName"])) {
	unlink($_POST["cancelFileName"]);
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError
);
echo json_encode(convertJson($varsSend));
?>