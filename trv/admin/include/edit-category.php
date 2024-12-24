<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";
	
    use Mexitek\PHPColors\Color;
	
	$existeError = false;
	$categoriaActualizada = false;
	
	if(isset($_POST["categoryEditName"]) && isset($_POST["categoryEditColor"]) && isset($_POST["categoryEditEmoji"]) && isset($_POST["categoryEditID"])){
	$colorBG = new Color($_POST['categoryEditColor']);
	$colorTxt = "#19191a";
	if($colorBG->isDark() == true){ $colorTxt = "#fff"; }
	
	$ucfirstString = ucfirst($_POST["categoryEditName"]);
	$stmt;
	
	if($_POST["categoryEditEmoji"] != ""){
	$stmt = $conn->prepare("UPDATE trvsol_categories SET nombre= ?, color= ?, color_txt= ?, emoji= ? WHERE id= ?");
	$stmt->bind_param("ssssi", $ucfirstString, $_POST["categoryEditColor"], $colorTxt, $_POST["categoryEditEmoji"], $_POST["categoryEditID"]);
	}else{
	$stmt = $conn->prepare("UPDATE trvsol_categories SET nombre= ?, color= ?, color_txt= ? WHERE id= ?");
	$stmt->bind_param("sssi", $ucfirstString, $_POST["categoryEditColor"], $colorTxt, $_POST["categoryEditID"]);
	}
	
	if($stmt->execute()){
	$categoriaActualizada = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'categoria_editada' => $categoriaActualizada
	);
	echo json_encode(convertJson($varsSend));
?>