<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$numberSales = 0;
	$totalSales = 0;
	$averageSales = 0;
	$chartTable = "";
	$bestSellerProducts = "<p class= 'has-text-centered is-size-5'><b>No se encontraron resultados</b></p>";
	
	$averageSalesArray = array();
	
	$fecha1 = date('Y-m-d', strtotime("-5 days"));
	$fecha2 = date('Y-m-d', strtotime(date("Y-m-d")));
	
	if(isset($_POST["getInfoToken"]) && $_POST["getInfoToken"] == "pos4862"){
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	$row = $result->fetch_assoc();
	
	$decoded = json_decode($row["estadisticas"], true);
	for($x= 0; $x < count($decoded); ++$x){
	$numberSales += $decoded[$x]["numberSales"];
	$totalSales += $decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"];
	array_push($averageSalesArray, ($decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"]));
	
	//Create chart
	$dateRead = date('Y-m-d', strtotime($decoded[$x]["date"]));
	if($dateRead >= $fecha1 && $dateRead <= $fecha2){ $chartTable .= "<tr><td>" . $decoded[$x]["date"] . "</td> <td>" . ($decoded[$x]["cashSales"] + $decoded[$x]["cardSales"] + $decoded[$x]["otherSales"]) . "</td></tr>"; }
	}
	
	//Calculate average
	$calcNumAverage = 0;
	$calcAverage = 0;
	
	for($x3= 0; $x3 < count($averageSalesArray); ++$x3){
	$calcAverage += $averageSalesArray[$x3];
	++$calcNumAverage;
	}
	
	if($calcNumAverage == 0){ $calcNumAverage = 1; }
	
	$averageSales = round($calcAverage / $calcNumAverage);
	
	//Calculate with last month sales
	$numberYesterday = 0;
	$totalYesterday = 0;
	
	$calcMonth = date("m") - 1;
	$calcYear = date("Y");
	if($calcMonth == 0){ $calcMonth = 12; $calcYear = date("Y") - 1; }
	
	$sql2 = "SELECT * FROM trvsol_stats WHERE mes=" . $calcMonth . " AND year=" . $calcYear;
	$result2 = $conn->query($sql2);
	
	if($result2->num_rows > 0){
	$row2 = $result2->fetch_assoc();
	
	$decoded2 = json_decode($row["estadisticas"], true);
	for($x= 0; $x < count($decoded2); ++$x){
	$numberYesterday += $decoded2[$x]["numberSales"];
	$totalYesterday += ($decoded2[$x]["cashSales"] + $decoded2[$x]["cardSales"] + $decoded2[$x]["otherSales"]);
	}
	
	if($numberYesterday == 0){ $numberYesterday = 1; }
	if($totalYesterday == 0){ $totalYesterday = 1; }
	
	$calcPercentageNumber = (($numberSales * 100) / $numberYesterday) - 100;
	$calcPercentageTotal = (($totalSales * 100) / $totalYesterday) - 100;
	
	$colorNumber = "";
	$colorTotal = "";
	
	if($calcPercentageNumber >= 100){ $calcPercentageNumber = "+100"; $colorNumber = "has-text-success"; }else{ $calcPercentageNumber = number_format($calcPercentageNumber, 0, ",", "."); }
	if($calcPercentageTotal >= 100){ $calcPercentageTotal = "+100"; $colorTotal = "has-text-success"; }else{ $calcPercentageTotal = number_format($calcPercentageTotal, 0, ",", "."); }
	
	if($calcPercentageNumber <= 0){ $colorNumber = "has-text-danger"; }
	if($calcPercentageTotal <= 0){ $colorTotal = "has-text-danger"; }
	
	$numberSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorNumber . '">' . $calcPercentageNumber . '% comparando el <b>mes anterior</b></p>';
	$totalSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorTotal . '">' . $calcPercentageTotal . '% comparando el <b>mes anterior</b></p>';
	}
	
	//Best seller products
	$sql3 = "SELECT nombre, imagen, categoryID, ventasMensuales FROM trvsol_products ORDER BY ventasMensuales DESC LIMIT 3";
	$result3 = $conn->query($sql3);
	
	if($result3->num_rows > 0){
	$bestSellerProducts = "";
	while($row3 = $result3->fetch_assoc()){
	$imagenProd = "/trv/media/imagen-no-disponible.png";
	if($row3["imagen"] != ""){ $imagenProd = $row3["imagen"]; }
	
	$sql4 = "SELECT color FROM trvsol_categories WHERE id=" . $row3["categoryID"];
	$result4 = $conn->query($sql4);
	
	if($result4->num_rows > 0){
	$row4 = $result4->fetch_assoc();
	
	$bestSellerProducts .= '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imagenProd . '" class= "is-rounded" style= "border: 2px solid ' . $row4["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row3["nombre"] . '</div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Unidades vendidas</h4>
			<p class= "is-size-5 has-text-success"><b>' . $row3["ventasMensuales"] . '</b></p>
			</div>
		</div>
		</div>
		</div>
	</div>';
	}}}}else{
	$existeError = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'sales' => $numberSales,
	'sales_money' => $totalSales,
	'sales_average' => $averageSales,
	'sales_table' => $chartTable,
	'sales_products' => $bestSellerProducts
	);
	echo json_encode(convertJson($varsSend));
?>