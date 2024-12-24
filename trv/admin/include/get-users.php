<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	
	$existeError = false;
	$listaUsuarios = "";
	
	if(isset($_POST["getUsersToken"]) && $_POST["getUsersToken"] == "admin38942"){
	$sql = "SELECT * FROM trvsol_users";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	$icons = "";
	
	if($row["admin"] == 1 && $row["id"] != $_COOKIE[$prefixCoookie . "IdUser"]){ $icons .= '<button class="button is-warning is-light" onclick= "setAdminUser(' . $row["id"] . ', 0)" id= "btnSetAdmin' . $row["id"] . '" title= "Revocar permisos de administrador"><i class="fas fa-user"></i></button>'; }
	else if($row["admin"] == 0){ $icons .= '<button class="button is-warning is-light" onclick= "setAdminUser(' . $row["id"] . ', 1)" id= "btnSetAdmin' . $row["id"] . '" title= "Establecer permisos de administrador"><i class="fas fa-user-gear"></i></button>'; }
	
	if($row["inventory"] == 1 && $row["id"] != $_COOKIE[$prefixCoookie . "IdUser"]){ $icons .= '<button class="button is-info is-light" onclick= "setInventoryUser(' . $row["id"] . ', 0)" id= "btnSetInventory' . $row["id"] . '" title= "Revocar permisos de personal de inventario"><i class="fas fa-user-lock"></i></button>'; }
	else if($row["inventory"] == 0){ $icons .= '<button class="button is-info is-light" onclick= "setInventoryUser(' . $row["id"] . ', 1)" id= "btnSetInventory' . $row["id"] . '" title= "Establecer permisos de personal de inventario"><i class="fas fa-boxes"></i></button>'; }
	
	$txtAdmin = "";
	if($row["admin"] == 1){ $txtAdmin .= '<span class="tag is-rounded is-warning is-light"><b>Administrador</b></span> '; }
	if($row["inventory"] == 1){ $txtAdmin .= '<span class="tag is-rounded is-info is-light"><b>Personal de inventario</b></span>'; }
	
	$listaUsuarios .= '<div class="list-item">
		<div class="list-item-content">
		<div class="list-item-title">' . $row["username"] . '</div>
		<div class="list-item-description"><span class="tag is-rounded is-success is-light"><b>Vendedor</b></span> ' . $txtAdmin . '</div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		<a class="button" href="/trv/admin/edit-user.php?id=' . $row["id"] . '" title= "Editar"><i class="fas fa-edit"></i> Editar</a>
		' . $icons . '
		<button class="button is-danger is-light" onclick= "deleteUser(' . $row["id"] . ')" id= "btnDeleteUser' . $row["id"] . '" title= "Eliminar"><i class="fas fa-trash-alt"></i></button>
		</div>
		</div>
	</div>';
	}}else{
	$existeError = true;
	}}else{
	$existeError = true;
	}
	
	$varsSend = array(
	'errores' => $existeError,
	'usuarios' => $listaUsuarios
	);
	echo json_encode(convertJson($varsSend));
?>