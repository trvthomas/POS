<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$configuracionGuardada = false;
	
	if(isset($_POST["editDesignHeading"]) && isset($_POST["editDesignFooter"])){
	if($_POST["editDesignFooter"] == "only"){
	$sql = "UPDATE trvsol_configuration SET value='" . $_POST["editDesignHeading"] . "' WHERE configName='printingHeadingInfo'";
	if($conn->query($sql) === TRUE){
	$configuracionGuardada = true;
	}}else if($_POST["editDesignHeading"] == "only"){
	$sql = "UPDATE trvsol_configuration SET value='" . $_POST["editDesignFooter"] . "' WHERE configName='printingFooterInfo'";
	if($conn->query($sql) === TRUE){
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