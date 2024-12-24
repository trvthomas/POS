<?php include_once include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";
include_once "versionInstalled.php";

//DO NOT TOUCH

//Update: 2.2.1
$stmt = $conn->prepare("SELECT * FROM trvsol_configuration WHERE configName= 'version'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	if ($row["value"] != $versionInstalled) {
		executeUpdate();

		$stmt2 = $conn->prepare("UPDATE trvsol_configuration SET value= ? WHERE configName= 'version'");
		$stmt2->bind_param("s", $versionInstalled);
		$stmt2->execute();
	}
}

//Tables to update, create, etc.
function executeUpdate()
{
	global $conn;

	// $stmt3 = $conn->prepare("UPDATE trvsol_products SET array_prices= '[]'");
	// $stmt3->execute();
}

//Redirect
header('Location:/trv/login.php?db-optimized=true');
?>