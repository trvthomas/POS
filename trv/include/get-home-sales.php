<?php include_once "DBData.php";

$existeError = false;
$numberSales = "Error";
$totalSales = "Error";
$goal = "Error";

if (isset($_POST["getInfoToken"]) && $_POST["getInfoToken"] == "pos4862") {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);

		$numberSales = number_format($decoded[date("d") - 1]["numberSales"], 0, ",", ".");
		$totalSales = number_format(($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]), 0, ",", ".");
		$goal = number_format($decoded[date("d") - 1]["goal"], 0, ",", ".");

		//Calculate with yesterday sales
		if (date("d") - 2 >= 0) {
			$numberYesterday = $decoded[date("d") - 2]["numberSales"];
			$totalYesterday = $decoded[date("d") - 2]["cashSales"] + $decoded[date("d") - 2]["cardSales"] + $decoded[date("d") - 2]["otherSales"];
			$goalToday = $decoded[date("d") - 1]["goal"];

			if ($numberYesterday == 0) {
				$numberYesterday = 1;
			}
			if ($totalYesterday == 0) {
				$totalYesterday = 1;
			}
			if ($goalToday == 0) {
				$goalToday = 1;
			}

			$calcPercentageNumber = (($decoded[date("d") - 1]["numberSales"] * 100) / $numberYesterday) - 100;
			$calcPercentageTotal = ((($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]) * 100) / $totalYesterday) - 100;
			$calcPercentageGoal = round((($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]) * 100) / $goalToday);

			$colorNumber = "";
			$colorTotal = "";
			$colorGoal = "";

			if ($calcPercentageNumber >= 100) {
				$calcPercentageNumber = "+100";
				$colorNumber = "has-text-success";
			} else {
				$calcPercentageNumber = number_format($calcPercentageNumber, 0, ",", ".");
			}
			if ($calcPercentageTotal >= 100) {
				$calcPercentageTotal = "+100";
				$colorTotal = "has-text-success";
			} else {
				$calcPercentageTotal = number_format($calcPercentageTotal, 0, ",", ".");
			}
			if ($calcPercentageGoal >= 100) {
				$calcPercentageGoal = 100;
				$colorGoal = "has-text-success";
			}

			if ($calcPercentageNumber <= 0) {
				$colorNumber = "has-text-danger";
			}
			if ($calcPercentageTotal <= 0) {
				$colorTotal = "has-text-danger";
			}

			$numberSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorNumber . '">' . $calcPercentageNumber . '% compared with <b>yesterday</b></p>';
			$totalSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorTotal . '">' . $calcPercentageTotal . '% compared with <b>yesterday</b></p>';
			$goal .= '<p class= "is-size-7 has-text-weight-normal ' . $colorGoal . '">' . number_format($calcPercentageGoal, 0, ",", ".") . '% of the goal</p>';
		}
	} else {
		$existeError = true;
	}
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'sales' => $numberSales,
	'sales_money' => $totalSales,
	'sales_goal' => $goal
);
echo json_encode(convertJson($varsSend));
?>