<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$usuarioEditado = false;
	
	if(isset($_POST["editUserName"]) && isset($_POST["editUserPass"]) && isset($_POST["editUserId"]) && $_POST["editUserId"] != $_COOKIE[$prefixCoookie . "IdUser"]){
	$sql = "UPDATE trvsol_users SET username='" . ucfirst($_POST["editUserName"]) . "', password='" . $_POST["editUserPass"] . "' WHERE id=" . $_POST["editUserId"];
	if($conn->query($sql) === TRUE){
	$usuarioEditado = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'usuario_editado' => $usuarioEditado
	);
	echo json_encode(convertJson($varsSend));
?>