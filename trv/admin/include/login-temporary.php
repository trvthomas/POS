<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";
	
	$existeError = false;
	$sesionIniciada = false;
	$credencialesInvalid = false;
	
	if(isset($_POST["loginUsername"]) && isset($_POST["loginPass"])){
	$sql = "SELECT * FROM trvsol_users WHERE username= '" . strtolower($_POST["loginUsername"]) . "' AND password= '" . $_POST["loginPass"] . "' AND admin=1";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	$row = $result->fetch_assoc();
	
	$sesionIniciada = true;
	
	setcookie($prefixCoookie . "TemporaryIdUser", $row["id"], time() + 3600, "/");
	setcookie($prefixCoookie . "TemporaryUsernameUser", $row["username"], time() + 3600, "/");
	}else{
	$credencialesInvalid = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'sesion_iniciada' => $sesionIniciada,
	'credenciales_invalid' => $credencialesInvalid
	);
	echo json_encode(convertJson($varsSend));
?>