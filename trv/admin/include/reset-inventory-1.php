<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	$urlWeb = "https://www.trvsolutions.com/pos/mails/new-mail-inventory-code.php";
	
	if(isset($_POST["resetInventoryPass"]) && connection_status() == 0){
	$credentialsCorrect = false;
	$securityCode = rand(1111, 9999);
	
	$sql = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "' AND password= '" . $_POST["resetInventoryPass"] . "' AND admin=1";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	$credentialsCorrect = true;
	
	$sql4 = "UPDATE trvsol_users SET securityCode='" . $securityCode . "' WHERE id= " . $_COOKIE[$prefixCoookie . "IdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "UsernameUser"] . "'";
	$conn->query($sql4);
	}else if(isset($_COOKIE[$prefixCoookie . "TemporaryIdUser"])){
	$sql2 = "SELECT * FROM trvsol_users WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "' AND password= '" . $_POST["resetInventoryPass"] . "' AND admin=1";
	$result2 = $conn->query($sql2);
	
	if($result2->num_rows > 0){
	$credentialsCorrect = true;
	
	$sql4 = "UPDATE trvsol_users SET securityCode='" . $securityCode . "' WHERE id= " . $_COOKIE[$prefixCoookie . "TemporaryIdUser"] . " AND username= '" . $_COOKIE[$prefixCoookie . "TemporaryUsernameUser"] . "'";
	$conn->query($sql4);
	}else{
	$credentialsCorrect = false;
	}}
	
	if($credentialsCorrect == true){
	$sql3 = "SELECT * FROM trvsol_configuration WHERE configName= 'adminEmail'";
	$result3 = $conn->query($sql3);
	
	if($result3->num_rows > 0){
	$row3 = $result3->fetch_assoc();
	
	$data = array('sendCodeEmail' => $row3["value"], 'sendCodeCode' => $securityCode);
	$options = array(
	'http' => array(
		'header'  => "Content-Type: application/x-www-form-urlencoded",
		'method'  => 'POST',
		'content' => http_build_query($data)
	));
	$context  = stream_context_create($options);
	$response = file_get_contents($urlWeb, false, $context);
	
	echo $response;
	}}else{
	echo '{"credenciales_incorrectas":true}';
	}}else{
	echo '{"errores":true}';
	}
?>