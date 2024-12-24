<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$tablaVentas = "";
	$cantidadesVendidas = 0;
	
	if(isset($_POST["getStatsFrom"]) && isset($_POST["getStatsTo"]) && isset($_POST["getStatsProdId"])){
	if($_POST["getStatsProdId"] == ""){
	$tablaVentas .= "especificar producto";
	}else{
	if($_POST["getStatsFrom"] == "N/A" && $_POST["getStatsTo"] == "N/A"){
	$fecha1 = date('Y-m-d', strtotime("-14 days"));
	$fecha2 = date('Y-m-d', strtotime(date("Y-m-d")));
	}else{
	$fecha1 = date('Y-m-d', strtotime($_POST["getStatsFrom"]));
	$fecha2 = date('Y-m-d', strtotime($_POST["getStatsTo"]));
	}
	
	$sql = "SELECT * FROM trvsol_products_stats WHERE productId=" . $_POST["getStatsProdId"];
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	$decoded = json_decode($row["estadisticas"], true);
	
	for($x = 0;$x < count($decoded);++$x){
    $dateRead = date('Y-m-d', strtotime($decoded[$x]["date"]));
	
    if($dateRead >= $fecha1 && $dateRead <= $fecha2){
	$tablaVentas .= "<tr><td>" . $decoded[$x]["date"] . "</td> <td>" . $decoded[$x]["quantitiesSold"] . "</td>";
	
	$cantidadesVendidas += $decoded[$x]["quantitiesSold"];
    }}}}else{
	$tablaVentas .= "sin registros";
	}}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'tabla_ventas' => $tablaVentas,
	'stats_cantidades' => $cantidadesVendidas
	);
	echo json_encode(convertJson($varsSend));
?>