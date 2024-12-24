<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$usuarioExiste = false;
	$usuarioAgregado = false;
	
	if(isset($_POST["addUserName"]) && isset($_POST["addUserPass"])){
	$sql2 = "SELECT * FROM trvsol_users WHERE username= '" . ucfirst($_POST["addUserName"]) . "'";
	$result2 = $conn->query($sql2);
	if($result2->num_rows > 0){
	$usuarioExiste = true;
	}else{
	$sql = "INSERT INTO trvsol_users (username, password)
	VALUES ('" . ucfirst($_POST["addUserName"]) . "', '" . $_POST["addUserPass"] . "')";
	if($conn->query($sql) === TRUE){
	$usuarioAgregado = true;
	
	//Actualice las estadísticas
	$sql6 = "SELECT * FROM trvsol_users";
	$result6 = $conn->query($sql6);
	if($result6->num_rows > 0){
	while($row6 = $result6->fetch_assoc()){
	$arrayUsers = array();
    
	for($x = 1;$x <= 12;++$x){
    $days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
    for($x2 = 1;$x2 <= $days;++$x2){
	$pushArray3 = array(
	'month' => $x,
	'date' => date("Y") . '-' . $x . '-' . $x2,
	'cashSales' => 0,
	'cardSales' => 0,
	'otherSales' => 0
	);
	$arrayUsers[] = $pushArray3;
	}}
	
	$sql7 = "SELECT * FROM trvsol_users_stats WHERE year=" . date("Y") . " AND userId=" . $row6["id"];
	$result7 = $conn->query($sql7);
	if($result7->num_rows == 0){
	$sql8 = "INSERT INTO trvsol_users_stats (year, userId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row6["id"] . "', '" . json_encode($arrayUsers) . "')";
	$conn->query($sql8);
	}}}}}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'nombre_existe' => $usuarioExiste,
	'usuario_creado' => $usuarioAgregado
	);
	echo json_encode(convertJson($varsSend));
?>