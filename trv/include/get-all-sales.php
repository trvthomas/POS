<?php include_once "DBData.php";

$existeError = false;
$numberSales = "Error";
$totalSales = "Error";
$cashSales = "Error";
$cardSales = "Error";
$otherSales = "Error";
$goal = "Error";
$initialCash = "Error";

$getNumInitialCash = "Error";

if (isset($_POST["getInfoToken"]) && $_POST["getInfoToken"] == "pos4862") {
	$sql = "SELECT * FROM trvsol_stats WHERE mes=" . date("m") . " AND year=" . date("Y");
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$decoded = json_decode($row["estadisticas"], true);

		$numberSales = number_format($decoded[date("d") - 1]["numberSales"], 0, ",", ".");
		$totalSales = number_format(($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]), 0, ",", ".");
		$cashSales = number_format($decoded[date("d") - 1]["cashSales"], 0, ",", ".");
		$cardSales = number_format($decoded[date("d") - 1]["cardSales"], 0, ",", ".");
		$otherSales = number_format($decoded[date("d") - 1]["otherSales"], 0, ",", ".");
		$goal = number_format($decoded[date("d") - 1]["goal"], 0, ",", ".");
		$getNumInitialCash = $decoded[date("d") - 1]["initialCash"];

		if (strlen($getNumInitialCash) > 10) {
			$initialCash = substr($getNumInitialCash, 0, 10) . '... <button class="button is-small" style= "vertical-align: middle" title= "Show the complete information" onclick= "showAllBase()" id= "showAllBaseBtn"><span class="icon is-small"><i class="fas fa-eye"></i></span></button>';
		} else {
			$initialCash = $getNumInitialCash;
		}

		//Compare with yesterday sales
		if (date("d") - 2 >= 0) {
			$numberYesterday = $decoded[date("d") - 2]["numberSales"];
			$totalYesterday = $decoded[date("d") - 2]["cashSales"] + $decoded[date("d") - 2]["cardSales"] + $decoded[date("d") - 2]["otherSales"];
			$cashYesterday = $decoded[date("d") - 2]["cashSales"];
			$cardYesterday = $decoded[date("d") - 2]["cardSales"];
			$otherYesterday = $decoded[date("d") - 2]["otherSales"];
			$goalToday = $decoded[date("d") - 1]["goal"];

			if ($numberYesterday == 0) {
				$numberYesterday = 1;
			}
			if ($totalYesterday == 0) {
				$totalYesterday = 1;
			}
			if ($cashYesterday == 0) {
				$cashYesterday = 1;
			}
			if ($cardYesterday == 0) {
				$cardYesterday = 1;
			}
			if ($otherYesterday == 0) {
				$otherYesterday = 1;
			}
			if ($goalToday == 0) {
				$goalToday = 1;
			}

			$calcPercentageNumber = (($decoded[date("d") - 1]["numberSales"] * 100) / $numberYesterday) - 100;
			$calcPercentageTotal = ((($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]) * 100) / $totalYesterday) - 100;
			$calcPercentageCash = (($decoded[date("d") - 1]["cashSales"] * 100) / $cashYesterday) - 100;
			$calcPercentageCard = (($decoded[date("d") - 1]["cardSales"] * 100) / $cardYesterday) - 100;
			$calcPercentageOther = (($decoded[date("d") - 1]["otherSales"] * 100) / $otherYesterday) - 100;
			$calcPercentageGoal = round((($decoded[date("d") - 1]["cashSales"] + $decoded[date("d") - 1]["cardSales"] + $decoded[date("d") - 1]["otherSales"]) * 100) / $goalToday);

			$colorNumber = "";
			$colorTotal = "";
			$colorCash = "";
			$colorCard = "";
			$colorOther = "";
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
			if ($calcPercentageCash >= 100) {
				$calcPercentageCash = "+100";
				$colorCash = "has-text-success";
			} else {
				$calcPercentageCash = number_format($calcPercentageCash, 0, ",", ".");
			}
			if ($calcPercentageCard >= 100) {
				$calcPercentageCard = "+100";
				$colorCard = "has-text-success";
			} else {
				$calcPercentageCard = number_format($calcPercentageCard, 0, ",", ".");
			}
			if ($calcPercentageOther >= 100) {
				$calcPercentageOther = "+100";
				$colorOther = "has-text-success";
			} else {
				$calcPercentageOther = number_format($calcPercentageOther, 0, ",", ".");
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
			if ($calcPercentageCash <= 0) {
				$colorCash = "has-text-danger";
			}
			if ($calcPercentageCard <= 0) {
				$colorCard = "has-text-danger";
			}
			if ($calcPercentageOther <= 0) {
				$colorOther = "has-text-danger";
			}

			$numberSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorNumber . '">' . $calcPercentageNumber . '% compared with <b>yesterday</b></p>';
			$totalSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorTotal . '">' . $calcPercentageTotal . '% compared with <b>yesterday</b></p>';
			$cashSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorCash . '">' . $calcPercentageCash . '% compared with <b>yesterday</b></p>';
			$cardSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorCard . '">' . $calcPercentageCard . '% compared with <b>yesterday</b></p>';
			$otherSales .= '<p class= "is-size-7 has-text-weight-normal ' . $colorOther . '">' . $calcPercentageOther . '% compared with <b>yesterday</b></p>';
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
	'sales_cash' => $cashSales,
	'sales_card' => $cardSales,
	'sales_other' => $otherSales,
	'sales_goal' => $goal,
	'sales_initial' => $initialCash,
	'sales_initial_complete' => $getNumInitialCash
);
echo json_encode(convertJson($varsSend));
?>