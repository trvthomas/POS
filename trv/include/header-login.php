<?php include_once "DBData.php";

$businessName = "TRV Solutions";

$sql = "SELECT configName, value FROM trvsol_configuration WHERE configName= 'businessName'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	if ($row["value"] != "") {
		$businessName = $row["value"];
	}
	if (strlen($businessName) > 20) {
		$businessName = substr($businessName, 0, 15) . "...";
	}

	$onclickNav = "this.classList.toggle('is-active');document.getElementById('headerMobile').classList.toggle('is-active');";

	echo '<nav class="navbar">
	<div class="navbar-brand">
	<a class="navbar-item" href="/trv"><img src="/trv/media/logo.png" style="width: auto;max-height: 4rem;"></a>
	<p style= "display: flex;align-items: center;"><b>' . $businessName . '</b></p>
	</div>
	</nav><br>';
}
?>