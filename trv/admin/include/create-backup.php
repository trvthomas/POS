<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$backupCreado = false;

include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/backup.php";

if (isset($_POST["createBackupToken"]) && $_POST["createBackupToken"] == "admin8kg2c") {
	generateBackup();
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'backup_creado' => $backupCreado
);
echo json_encode(convertJson($varsSend));
?>