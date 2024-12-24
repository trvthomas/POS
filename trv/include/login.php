<?php include_once "DBData.php";

$existeError = false;
$sesionIniciada = false;
$credencialesInvalid = false;

if (isset($_POST["loginUsername"]) && isset($_POST["loginPass"]) && isset($_POST["loginCashBase"])) {
	$sql = "SELECT * FROM trvsol_users WHERE username= '" . strtolower($_POST["loginUsername"]) . "' AND password= '" . $_POST["loginPass"] . "'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$sql2 = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_assoc();

			$decoded = json_decode($row2["estadisticas"], true);
			if ($decoded[date("d") - 1]["entryDate"] == "") {
				$decoded[date("d") - 1]["entryDate"] = date("d-m-Y h:i a");
			}
			$decoded[date("d") - 1]["seller"] .= $row["username"] . " ";

			if ($decoded[date("d") - 1]["initialCash"] == "") {
				$decoded[date("d") - 1]["initialCash"] = number_format($_POST["loginCashBase"], 0, ",", ".");
			} else {
				$decoded[date("d") - 1]["initialCash"] .= " Shift " . substr($_POST["loginUsername"], 0, 4) . ".: $" . number_format($_POST["loginCashBase"], 0, ",", ".");
			}

			$decoded[date("d") - 1]["reports"] .= "<b>New shift: " . $row["username"] . "</b><br>";

			//Stablish goal
			if (date("d") - 3 >= 0) {
				$averageSales = round((($decoded[date("d") - 3]["cashSales"] + $decoded[date("d") - 3]["cardSales"] + $decoded[date("d") - 3]["otherSales"]) + ($decoded[date("d") - 2]["cashSales"] + $decoded[date("d") - 2]["cardSales"] + $decoded[date("d") - 2]["otherSales"])) / 2, -2);
				$decoded[date("d") - 1]["goal"] = $averageSales;
			}

			$sql3 = "UPDATE trvsol_stats SET estadisticas='" . json_encode($decoded) . "' WHERE mes=" . date("m") . " AND year=" . date("Y");
			$conn->query($sql3);

			$sesionIniciada = true;

			setcookie($prefixCoookie . "IdUser", $row["id"], time() + (86400 * 30), "/");
			setcookie($prefixCoookie . "UsernameUser", $row["username"], time() + (86400 * 30), "/");
			setcookie($prefixCoookie . "DateEnter", date("Y-m-d"), time() + (86400 * 30), "/");
			setcookie($prefixCoookie . "DateDay", date("d"), time() + (86400 * 30), "/");
		}
	} else {
		$credencialesInvalid = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'sesion_iniciada' => $sesionIniciada,
	'credenciales_invalid' => $credencialesInvalid
);
echo json_encode(convertJson($varsSend));
?>