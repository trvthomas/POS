<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$tablaVentas = "";
	$totalVentas = 0;
	$efectivoVentas = 0;
	$tarjetaVentas = 0;
	$otroVentas = 0;
	
	if(isset($_POST["getStatsFrom"]) && isset($_POST["getStatsTo"]) && isset($_POST["getStatsCash"]) && isset($_POST["getStatsCard"]) && isset($_POST["getStatsOther"]) && isset($_POST["getStatsSellerId"])){
	if($_POST["getStatsFrom"] == "N/A" && $_POST["getStatsTo"] == "N/A"){
	$fecha1 = date('Y-m-d', strtotime("-14 days"));
	$fecha2 = date('Y-m-d', strtotime(date("Y-m-d")));
	}else{
	$fecha1 = date('Y-m-d', strtotime($_POST["getStatsFrom"]));
	$fecha2 = date('Y-m-d', strtotime($_POST["getStatsTo"]));
	}
	
	$sql = "SELECT * FROM trvsol_users_stats WHERE userId=" . $_POST["getStatsSellerId"];
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	$decoded = json_decode($row["estadisticas"], true);
	
	for($x = 0;$x < count($decoded);++$x){
    $dateRead = date('Y-m-d', strtotime($decoded[$x]["date"]));
	
    if($dateRead >= $fecha1 && $dateRead <= $fecha2){
	$totalSales = $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];
	
	$tablaVentas .= "<tr><td>" . $decoded[$x]["date"] . "</td>";
	
	if($_POST["getStatsCash"] == "true"){ $tablaVentas .= "<td>" . $decoded[$x]["cashSales"] . "</td>"; }
	if($_POST["getStatsCard"] == "true"){ $tablaVentas .= "<td>" . $decoded[$x]["cardSales"] . "</td>"; }
	if($_POST["getStatsOther"] == "true"){ $tablaVentas .= "<td>" . $decoded[$x]["otherSales"] . "</td>"; }
	
	$tablaVentas .= "<td>" . $totalSales . "</td> </tr>";
	
	$totalVentas += $totalSales;
	$efectivoVentas += $decoded[$x]["cashSales"];
	$tarjetaVentas += $decoded[$x]["cardSales"];
	$otroVentas += $decoded[$x]["otherSales"];
    }}}}else{
	$tablaVentas .= "sin registros";
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'tabla_ventas' => $tablaVentas,
	'stats_total' => $totalVentas,
	'stats_efectivo' => $efectivoVentas,
	'stats_tarjeta' => $tarjetaVentas,
	'stats_other' => $otroVentas
	);
	echo json_encode(convertJson($varsSend));
?>