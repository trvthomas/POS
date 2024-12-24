<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$configuracionGuardada = false;
	
	if(isset($_POST["editConfigId"]) && isset($_POST["editConfigValue"])){
	$sql = "SELECT * FROM trvsol_configuration WHERE configName='" . $_POST["editConfigId"] . "'";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	$sql2 = "UPDATE trvsol_configuration SET value='" . $_POST["editConfigValue"] . "' WHERE configName='" . $_POST["editConfigId"] . "'";
	if($conn->query($sql2) === TRUE){
	$configuracionGuardada = true;
	}}else{
	$existeError = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'configuracion_guardada' => $configuracionGuardada
	);
	echo json_encode(convertJson($varsSend));
?>