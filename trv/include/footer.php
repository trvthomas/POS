<?php include_once "DBData.php";

$sql = "SELECT configName, value FROM trvsol_configuration WHERE configName= 'version'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	$versionSystem = 0;
	$row = $result->fetch_assoc();
	$versionSystem = $row["value"];

	$isFixed = "";
	if (isset($footerFixed) && $footerFixed == true) {
		$isFixed = ' style= "position: absolute; bottom: 0; left: 0;"';
	}

	echo '<br><footer' . $isFixed . '>&copy; ' . date("Y") . ', TRV Solutions - <a style= "color: #fff" href= "https://www.trvsolutions.com" target= "_blank">www.trvsolutions.com</a> - POS System Version ' . $versionSystem . '</footer>
	<script>
	document.onkeydown = function(){
	switch(event.keyCode){
	case 112:
	if(event.keyCode == 112){
	window.location = "/trv/new-invoice.php";
	}
	
	if(event.keyCode){
	event.returnValue = false;
	event.keyCode = 0;
	return false;
	}}}
	</script>';
}
?>