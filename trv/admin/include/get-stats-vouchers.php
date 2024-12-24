<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$tablaVentas = "";
	$totalUsos = 0;
	
	if(isset($_POST["getStatsFrom"]) && isset($_POST["getStatsTo"]) && isset($_POST["getStatsVoucherId"])){
	if($_POST["getStatsFrom"] == "N/A" && $_POST["getStatsTo"] == "N/A"){
	$fecha1 = date('Y-m-d', strtotime("-14 days"));
	$fecha2 = date('Y-m-d', strtotime(date("Y-m-d")));
	}else{
	$fecha1 = date('Y-m-d', strtotime($_POST["getStatsFrom"]));
	$fecha2 = date('Y-m-d', strtotime($_POST["getStatsTo"]));
	}
	
	if($_POST["getStatsVoucherId"] != ""){
	$sql = "SELECT * FROM trvsol_vouchers_stats WHERE voucherId=" . $_POST["getStatsVoucherId"];
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	$decoded = json_decode($row["estadisticas"], true);
	
	for($x = 0;$x < count($decoded);++$x){
    $dateRead = date('Y-m-d', strtotime($decoded[$x]["date"]));
	
    if($dateRead >= $fecha1 && $dateRead <= $fecha2){
	$tablaVentas .= "<tr><td>" . $decoded[$x]["date"] . "</td> <td>" . $decoded[$x]["uses"] . "</td></tr>";
	
	$totalUsos += $decoded[$x]["uses"];
    }}}}else{
	$tablaVentas .= "sin registros";
	}}else{
	$tablaVentas .= "sin registros";
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'tabla_ventas' => $tablaVentas,
	'stats_uses' => $totalUsos
	);
	echo json_encode(convertJson($varsSend));
?>