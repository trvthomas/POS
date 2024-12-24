<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$organized = false;
	
	if(isset($_POST["organizeProdsArray"])){
	$decoded = json_decode($_POST["organizeProdsArray"], true);
	$organizeNum = 1;
	
	for($x= 0; $x < count($decoded); ++$x){
	$stmt = $conn->prepare("UPDATE trvsol_products SET display_order= ? WHERE id= ?");
	$stmt->bind_param("ii", $organizeNum, $decoded[$x]);
	if($stmt->execute()){
	$organized = true;
	++$organizeNum;
	}}}
	
	$varsSend = array(
	'organized' => $organized
	);
	echo json_encode(convertJson($varsSend));
?>