<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";
	
    use Mexitek\PHPColors\Color;
	
	$existeError = false;
	$categoriaAgregada = false;
	
	if(isset($_POST["categoryAddName"]) && isset($_POST["categoryAddColor"]) && isset($_POST["categoryAddEmoji"])){
	$colorBG = new Color($_POST['categoryAddColor']);
	$colorTxt = "#19191a";
	if($colorBG->isDark() == true){ $colorTxt = "#fff"; }
	
	$ucfirstString = ucfirst($_POST["categoryAddName"]);
	
	$stmt = $conn->prepare("INSERT INTO trvsol_categories (nombre, color, color_txt, emoji) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", $ucfirstString, $_POST["categoryAddColor"], $colorTxt, $_POST["categoryAddEmoji"]);
	if($stmt->execute()){
	$categoriaAgregada = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'categoria_agregada' => $categoriaAgregada
	);
	echo json_encode(convertJson($varsSend));
?>