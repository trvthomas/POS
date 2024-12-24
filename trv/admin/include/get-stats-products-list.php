<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$listaProductos = "";
	
	if(isset($_POST["getListFrom"]) && isset($_POST["getListTo"])){
	if($_POST["getListFrom"] == "N/A" && $_POST["getListTo"] == "N/A"){
	$fecha1 = date('Y-m-d', strtotime("-14 days"));
	$fecha2 = date('Y-m-d', strtotime(date("Y-m-d")));
	}else{
	$fecha1 = date('Y-m-d', strtotime($_POST["getListFrom"]));
	$fecha2 = date('Y-m-d', strtotime($_POST["getListTo"]));
	}
	
	$sql = "SELECT * FROM trvsol_products ORDER BY activo DESC, categoryID ASC, display_order ASC";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	$imageProduct = "/trv/media/imagen-no-disponible.png";
	if($row["imagen"] != ""){ $imageProduct = $row["imagen"]; }
	
	$cantidadesVendidasProd = 0;
	
	$sql2 = "SELECT * FROM trvsol_products_stats WHERE productId=" . $row["id"];
	$result2 = $conn->query($sql2);
	if($result2->num_rows > 0){
	while($row2 = $result2->fetch_assoc()){
	$decoded = json_decode($row2["estadisticas"], true);
	
	for($x = 0;$x < count($decoded);++$x){
    $dateRead = date('Y-m-d', strtotime($decoded[$x]["date"]));
	
    if($dateRead >= $fecha1 && $dateRead <= $fecha2){
	$cantidadesVendidasProd += $decoded[$x]["quantitiesSold"];
    }}}
	
	$sql3 = "SELECT * FROM trvsol_categories WHERE id=" . $row["categoryID"];
	$result3 = $conn->query($sql3);
	
	if($result3->num_rows > 0){
	$row3 = $result3->fetch_assoc();
	
	$finalPrice = '$' . number_format($row["precio"], 0, ",", ".");
	if($row["variable_price"] == 1){ $finalPrice = 'Precio variable'; }
	
	$listaProductos .= '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><img src="' . $imageProduct . '" class= "is-rounded" style= "border: 2px solid ' . $row3["color"] . ';"></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light">' . $finalPrice . '</span> <span class="tag is-rounded"><i class="fas fa-barcode"></i> ' . $row["barcode"] . '</span></div>
		<div class="list-item-description"><span class="tag is-rounded" style= "background-color: ' . $row3["color"] . '; color: ' . $row3["color_txt"] . ';">' . $row3["nombre"] . '</span></div>
		</div>
		
		<div class="list-item-controls">
		<div class= "columns has-text-centered">
		<div class= "column">
			<div class= "block">
			<h4 class= "is-size-6 has-text-grey">Unidades vendidas</h4>
			<p class= "is-size-5 has-text-success"><b>' . $cantidadesVendidasProd . '</b></p>
			</div>
		</div>
		</div>
		</div>
	</div>';
	}}}}else{
	$listaProductos = "<p class= 'has-text-centered is-size-5'><b>No se encontraron productos</b></p>";
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'lista_productos' => $listaProductos
	);
	echo json_encode(convertJson($varsSend));
?>