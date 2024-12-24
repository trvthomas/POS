<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$listaCategorias = "";

if (isset($_POST["getCategoriesToken"]) && $_POST["getCategoriesToken"] == "admin38942") {
	$sql = "SELECT * FROM trvsol_categories";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$onclick = "editCategory(" . $row["id"] . ", '" . $row["nombre"] . "', '" . $row["color"] . "')";

			$icons = '<button class="button is-info is-light" onclick="' . $onclick . '" title= "Edit"><i class="fas fa-edit"></i> Edit</button>
	<button class="button is-danger is-light" on<click= "deleteCategory(' . $row["id"] . ')" id= "btnDeleteCategory' . $row["id"] . '" title= "Delete"><i class="fas fa-trash-alt"></i></button>';
			if ($row["id"] == 1) {
				$icons = "";
			}

			$listaCategorias .= '<div class="list-item">
		<div class="list-item-image">
		<figure class="image is-64x64"><div class= "categoryColorImage" style= "background-color: ' . $row["color"] . ';color: ' . $row["color_txt"] . '"><span>' . $row["emoji"] . '</span></div></figure>
		</div>
		
		<div class="list-item-content">
		<div class="list-item-title">' . $row["nombre"] . '</div>
		</div>
		
		<div class="list-item-controls">
		<div class= "buttons is-right">
		' . $icons . '
		</div>
		</div>
	</div>';
		}
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'categorias' => $listaCategorias
);
echo json_encode($varsSend);
?>