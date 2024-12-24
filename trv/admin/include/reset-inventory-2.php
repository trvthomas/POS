<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$infoActualizada = false;
	$credentialsCorrect = false;
	
	if(isset($_POST["resetInventory2Code"])){
	$sql = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "' AND securityCode= '" . $_POST["resetInventory2Code"] . "' AND admin=1";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	$credentialsCorrect = true;
	
	$sql4 = "UPDATE trvsol_users SET securityCode='' WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "'";
	$conn->query($sql4);
	}else{
	$sql2 = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "' AND securityCode= '" . $_POST["resetInventory2Code"] . "' AND admin=1";
	$result2 = $conn->query($sql2);
	
	if($result2->num_rows > 0){
	$credentialsCorrect = true;
	
	$sql4 = "UPDATE trvsol_users SET securityCode='' WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "'";
	$conn->query($sql4);
	}else{
	$credentialsCorrect = false;
	}}
	
	if($credentialsCorrect == true){
	$sql = "UPDATE trvsol_products SET stock='0'";
	if($conn->query($sql) === TRUE){
	$infoActualizada = true;
	}else{
	$existeError = true;
	}}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'codigo_incorrecto' => $credentialsCorrect,
	'inventario_reseteado' => $infoActualizada
	);
	echo json_encode(convertJson($varsSend));
?>