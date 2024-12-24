<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";

$existeError = false;
$allFiles = "<p class='has-text-centered is-size-5'><b>No results found</b></p>";

if (isset($_POST["getImgsCode"]) && isset($_POST["getImgsCodeSearch"]) && $_POST["getImgsCode"] == "xo92Th794P") {
	$sql = "SELECT * FROM trvsol_admin_images WHERE url LIKE '%" . $_POST["getImgsCodeSearch"] . "%' OR name LIKE '%" . $_POST["getImgsCodeSearch"] . "%'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$allFiles = '<div class= "columns is-multiline is-mobile">';
		while ($row = $result->fetch_assoc()) {
			$onclick = "copyImg('" . $row['url'] . "')";
			$onclick2 = "deleteImg('" . $row['url'] . "', '" . $row["id"] . "')";

			$allFiles .= '<div class= "column is-half">
	<a href= "' . $row['url'] . '" target= "_blank" title= "View image"><img src= "' . $row['url'] . '" style= "max-width: 95%;"></a>
	<p style= "margin-top: 2px;">' . $row["name"] . '</p>
	<div class= "buttons is-centered">
	<button class= "button backgroundDark copyUrl" onclick= "codeCopied()" data-clipboard-text= "' . $row['url'] . '" title= "Copy link"><i class= "fas fa-link"></i></button>
	<button class= "button is-danger is-light" id= "btnDeleteImg' . $row["id"] . '" onclick= "' . $onclick2 . '" title= "Delete"><i class= "fas fa-trash-alt"></i></button>
	</div>
	</div>';
		}
		$allFiles .= '</div>';
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'files' => $allFiles
);
echo json_encode(convertJson($varsSend));
?>