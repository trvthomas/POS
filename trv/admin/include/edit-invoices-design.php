<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$configuracionGuardada = false;

if (isset($_POST["editDesignTemplateInvoice"]) && isset($_POST["editDesignTemplateDaySummary"])) {
	$sql = "UPDATE trvsol_configuration SET value='" . $_POST["editDesignTemplateInvoice"] . "' WHERE configName='templateInvoice'";
	if ($conn->query($sql) === TRUE) {
		$sql2 = "UPDATE trvsol_configuration SET value='" . $_POST["editDesignTemplateDaySummary"] . "' WHERE configName='templateDayReport'";
		if ($conn->query($sql2) === TRUE) {
			$configuracionGuardada = true;
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