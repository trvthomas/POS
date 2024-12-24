<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$codigoExiste = false;
	$voucherAgregado = false;
	
	if(isset($_POST["addVoucherCode"]) && isset($_POST["addVoucherAvailable"]) && isset($_POST["addVoucherMinimum"]) && isset($_POST["addVoucherValue"]) && isset($_POST["addVoucherExpiration"]) && isset($_POST["addVoucherPayment"])){
	$sql2 = "SELECT * FROM trvsol_vouchers WHERE code= '" . strtoupper($_POST["addVoucherCode"]) . "'";
	$result2 = $conn->query($sql2);
	if($result2->num_rows > 0){
	$codigoExiste = true;
	}else{
	$sql = "INSERT INTO trvsol_vouchers (code, totalAvailable, minimumQuantity, value, paymentMethods, expiration)
	VALUES ('" . strtoupper($_POST["addVoucherCode"]) . "', '" . $_POST["addVoucherAvailable"] . "', '" . $_POST["addVoucherMinimum"] . "', '" . $_POST["addVoucherValue"] . "', '" . $_POST["addVoucherPayment"] . "', '" . $_POST["addVoucherExpiration"] . "')";
	if($conn->query($sql) === TRUE){
	$voucherAgregado = true;
	
	//Actualice estadísticas
	$sql9 = "SELECT * FROM trvsol_vouchers";
	$result9 = $conn->query($sql9);
	if($result9->num_rows > 0){
	while($row9 = $result9->fetch_assoc()){
	$arrayVouchers = array();
    
	for($x = 1;$x <= 12;++$x){
    $days = date('t', mktime(0, 0, 0, $x, 1, date("Y")));
    for($x2 = 1;$x2 <= $days;++$x2){
	$pushArray4 = array(
	'month' => $x,
	'date' => date("Y") . '-' . $x . '-' . $x2,
	'uses' => 0
	);
	$arrayVouchers[] = $pushArray4;
	}}
	
	$sql10 = "SELECT * FROM trvsol_vouchers_stats WHERE year=" . date("Y") . " AND voucherId=" . $row9["id"];
	$result10 = $conn->query($sql10);
	if($result10->num_rows == 0){
	$sql11 = "INSERT INTO trvsol_vouchers_stats (year, voucherId, estadisticas)
	VALUES ('" . date("Y") . "', '" . $row9["id"] . "', '" . json_encode($arrayVouchers) . "')";
	$conn->query($sql11);
	}}}}}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'codigo_existe' => $codigoExiste,
	'voucher_creado' => $voucherAgregado
	);
	echo json_encode(convertJson($varsSend));
?>