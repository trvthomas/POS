<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$codigoExiste = false;
	$productoEditado = false;
	
	if(isset($_POST["editProdId"]) && isset($_POST["editProdName"]) && isset($_POST["editProdImage"]) && isset($_POST["editProdPrice"]) && isset($_POST["editProdBarcode"]) && isset($_POST["editProdCategory"]) && isset($_POST["editProdPurchasePrice"]) && isset($_POST["editProdIsVariable"]) && isset($_POST["editProdArrayPrices"])){
	$strtotupperString = strtoupper($_POST["editProdBarcode"]);
	
	$stmt2 = $conn->prepare("SELECT * FROM trvsol_products WHERE barcode= ? AND NOT id= ?");
	$stmt2->bind_param("si", $strtotupperString, $_POST["editProdId"]);
	$stmt2->execute();
	$result2 = $stmt2->get_result();
	
	if($result2->num_rows > 0){
	$codigoExiste = true;
	}else{
	$prodTags = "";
	
	$wordsProd = explode(" ", strtoupper($_POST["editProdName"]));
	for($x= 0; $x < count($wordsProd); ++$x){
	$prodTags .= $wordsProd[$x] . ", ";
	}
	
	$stmt = $conn->prepare("UPDATE trvsol_products SET nombre= ?, precio= ?, variable_price= ?, array_prices= ?, imagen= ?, barcode= ?, categoryID= ?, purchasePrice= ?, tags= ? WHERE id= ?");
	$stmt->bind_param("siisssiisi", $_POST["editProdName"], $_POST["editProdPrice"], $_POST["editProdIsVariable"], $_POST["editProdArrayPrices"], $_POST["editProdImage"], $strtotupperString, $_POST["editProdCategory"], $_POST["editProdPurchasePrice"], $prodTags, $_POST["editProdId"]);
	if($stmt->execute()){
	$productoEditado = true;
	}}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'codigo_existe' => $codigoExiste,
	'producto_editado' => $productoEditado
	);
	echo json_encode(convertJson($varsSend));
?>