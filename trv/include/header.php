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

	$btnCloseCash = "";
	if (date("H") >= 18) {
		$btnCloseCash = '<a class="button is-light" href= "/trv/close-day.php"><i class="fas fa-moon"></i> End shift</a>';
	}

	$onclickNav = "this.classList.toggle('is-active');document.getElementById('headerMobile').classList.toggle('is-active');";

	echo '<nav class="navbar">
	<div class="navbar-brand">
	<a class="navbar-item" href="/trv"><img src="/trv/media/logo.png" style="width: auto;max-height: 4rem;"></a>
	<p style= "display: flex;align-items: center;"><b>' . $businessName . '</b></p>
	
	<a class="navbar-burger" data-target="headerMobile" onclick= "' . $onclickNav . '">
	<span></span>
	<span></span>
	<span></span>
	</a>
	</div>
	
	<div id="headerMobile" class="navbar-menu">
	<div class="navbar-start">
	<a class="navbar-item" href= "/trv"><i class="fas fa-house"></i> Home</a>
	<a class="navbar-item" href= "/trv/price-check.php"><i class="fas fa-tags"></i> Price check</a>
	
	<div class="navbar-item has-dropdown is-hoverable">
	<a class="navbar-link">Sales control</a>
	
	<div class="navbar-dropdown">
	<a class="navbar-item" href= "/trv/sales.php"><i class="fas fa-coins"></i> Day statistics</a>
	<a class="navbar-item" href= "/trv/money-movement.php"><i class="fas fa-money-bill-wave"></i> Cash adjustment</a>
	<a class="navbar-item" href= "/trv/reports.php"><i class="fas fa-pencil-alt"></i> Reports</a>
	<a class="navbar-item" href= "/trv/close-day.php"><i class="fas fa-moon"></i> End shift</a>
	</div>
	</div>
	</div>
	
	<div class="navbar-end">
	<div class="navbar-item">
	<div class="buttons">
	' . $btnCloseCash . '
	<a class="button backgroundDark" href= "/trv/new-invoice.php"><i class="fas fa-receipt"></i> New sale (F1)</a>
	</div>
	</div>
	</div>
	</div>
	</nav><br>';
}
?>