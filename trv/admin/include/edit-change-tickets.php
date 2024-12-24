<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$configuracionGuardada = false;

if (isset($_POST["saveConfigTemplate"]) && isset($_POST["saveConfigCopies"]) && isset($_POST["saveConfigExpire"])) {
	$sql = "UPDATE trvsol_configuration SET value='" . $_POST["saveConfigTemplate"] . "' WHERE configName='changeTicketsTemplate'";
	if ($conn->query($sql) === TRUE) {
		$sql2 = "UPDATE trvsol_configuration SET value='" . $_POST["saveConfigCopies"] . "' WHERE configName='changeTicketsPrintDefault'";
		if ($conn->query($sql2) === TRUE) {
			$sql3 = "UPDATE trvsol_configuration SET value='" . $_POST["saveConfigExpire"] . "' WHERE configName='changeTicketsExpireDays'";
			if ($conn->query($sql3) === TRUE) {
				$configuracionGuardada = true;
			}
		}
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'configuracion_guardada' => $configuracionGuardada
);
echo json_encode(convertJson($varsSend));
?>