<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBOptimization.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/updates/versionInstalled.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/convertJson.php";

$servername2 = "localhost";
$username2 = "root";
$password2 = "";
$conn2 = new mysqli($servername2, $username2, $password2);

$existeError = false;
$configuracionAplicada = false;

if (isset($_POST["prepareSystemBusinessName"]) && isset($_POST["prepareSystemBusinessEmail"]) && isset($_POST["prepareSystemSaleTemplate"]) && isset($_POST["prepareSystemCloseTemplate"]) && isset($_POST["prepareSystemUsername"]) && isset($_POST["prepareSystemPassword"])) {
	//Create DB
	$sql = "CREATE DATABASE IF NOT EXISTS trvsol_pos";
	$conn2->query($sql);

	//Connect to DB
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "trvsol_pos";
	$conn = new mysqli($servername, $username, $password, $dbname);

	//Create tables
	//trvsol_admin_images
	$conn->query($dbOptimize_trvsol_admin_images);

	//trvsol_categories
	$conn->query($dbOptimize_trvsol_categories);
	//Insert trvsol_categories
	$sql4 = "INSERT INTO trvsol_categories (id, nombre, color, color_txt)
	VALUES (1, 'No category', '#e6e7e8', '#19191a')";
	$conn->query($sql4);

	//trvsol_invoices
	$conn->query($dbOptimize_trvsol_invoices);

	//trvsol_products
	$conn->query($dbOptimize_trvsol_products);

	//trvsol_products_stats
	$conn->query($dbOptimize_trvsol_products_stats);

	//trvsol_stats
	$conn->query($dbOptimize_trvsol_stats);

	//trvsol_users
	$conn->query($dbOptimize_trvsol_users);
	//Insert trvsol_users
	$sql10 = "INSERT INTO trvsol_users (id, username, password, inventory, admin)
	VALUES (1, '" . $_POST["prepareSystemUsername"] . "', '" . $_POST["prepareSystemPassword"] . "', '1', '1')";
	$conn->query($sql10);

	//trvsol_users_stats
	$conn->query($dbOptimize_trvsol_users_stats);

	//trvsol_vouchers
	$conn->query($dbOptimize_trvsol_vouchers);

	//trvsol_vouchers_stats
	$conn->query($dbOptimize_trvsol_vouchers_stats);

	//trvsol_inventory
	$conn->query($dbOptimize_trvsol_inventory);

	//trvsol_configuration
	$conn->query($dbOptimize_trvsol_configuration);
	//Insert trvsol_configuration
	$templateMonth = '<h2 style="text-align: center;">Monthly report</h2><h2 style="text-align: center;">{{trv_monthy_name}}</h2><hr /><p><strong>{{trv_monthy_sales_number}}</strong> sales made</p><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Cash sales</strong></td><td style="width: 47.9567%;">${{trv_monthy_sales_cash}}</td></tr><tr><td style="width: 47.8365%;"><strong>Card sales</strong></td><td style="width: 47.9567%;">${{trv_monthy_sales_card}}</td></tr><tr><td style="width: 47.8365%;"><strong>Other payment method sales (if configured): {{trv_monthly_name_other_payment}})</strong></td><td style="width: 47.9567%;">${{trv_monthy_sales_other}}</td></tr><tr><td style="width: 47.8365%;"><strong>Total income</strong></td><td style="width: 47.9567%;"><strong>${{trv_monthy_sales_total}}</strong></td></tr></tbody></table><hr /><h2 style="text-align: center;">Best-selling products</h2><p>{{trv_monthy_best_selling_prod}}</p><hr /><h2 style="text-align: center;">Comparison with the previous month</h2><table style="border-collapse: collapse; width: 100%; height: 254.719px; border-color: #34495e; border-style: solid;" border="1"><tbody><tr style="height: 19.5938px;"><td style="width: 23%; text-align: center; height: 19.5938px;"><strong>&nbsp;</strong></td><td style="width: 23%; text-align: center; height: 19.5938px;"><strong>{{trv_monthy_name}}</strong></td><td style="width: 23%; text-align: center; height: 19.5938px;"><strong>{{trv_monthy_name_past}}</strong></td><td style="width: 23%; text-align: center; height: 19.5938px;"><strong>Difference</strong></td></tr><tr style="height: 39.1875px;"><td style="width: 23%; height: 39.1875px;"><strong>Sales made</strong></td><td style="width: 23%; height: 39.1875px;">{{trv_monthy_sales_number}}</td><td style="width: 23%; height: 39.1875px;">{{trv_monthy_diff_sales_number_past}}</td><td style="width: 23%; height: 39.1875px;">{{trv_monthy_diff_sales_number_diff}}</td></tr><tr style="height: 39.1875px;"><td style="width: 23%; height: 39.1875px;"><strong>Cash sales</strong></td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_sales_cash}}</td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_diff_sales_cash_past}}</td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_diff_sales_cash_diff}}</td></tr><tr style="height: 39.1875px;"><td style="width: 23%; height: 39.1875px;"><strong>Card sales</strong></td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_sales_card}}</td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_diff_sales_card_past}}</td><td style="width: 23%; height: 39.1875px;">${{trv_monthy_diff_sales_card_diff}}</td></tr><tr style="height: 78.375px;"><td style="width: 23%; height: 78.375px;"><strong>Other payment method sales</strong></td><td style="width: 23%; height: 78.375px;">${{trv_monthy_sales_other}}</td><td style="width: 23%; height: 78.375px;">${{trv_monthy_diff_sales_other_past}}</td><td style="width: 23%; height: 78.375px;">${{trv_monthy_diff_sales_other_diff}}</td></tr><tr style="height: 39.1875px;"><td style="width: 23%; height: 39.1875px;"><strong>Total income</strong></td><td style="width: 23%; height: 39.1875px;"><strong>${{trv_monthy_sales_total}}</strong></td><td style="width: 23%; height: 39.1875px;"><strong>${{trv_monthy_diff_sales_total_past}}</strong></td><td style="width: 23%; height: 39.1875px;"><strong>${{trv_monthy_diff_sales_total_diff}}</strong></td></tr></tbody></table><p>&nbsp;</p><div style="width: 100%; background-color: #d8ecf3; border-left: 5px solid #3ca1c3; padding: 10px;"><p><span style="color: #19191a;"><strong>Monthly tip:</strong> {{trv_tip}}</span></p></div>';
	$templateChangeTickets = '<h2 style="text-align: center;">Gift ticket</h2><hr /><table style="border-collapse: collapse; width: 100%;" border="0"><tbody><tr><td style="width: 47.8365%;"><strong>Date and time of purchase</strong></td><td style="width: 47.9567%;">{{trv_date_purchase}}</td></tr><tr><td style="width: 47.8365%;"><strong>Receipt #</strong></td><td style="width: 47.9567%;">{{trv_num_invoice}}</td></tr><tr><td style="width: 47.8365%;"><strong>Attended by</strong></td><td style="width: 47.9567%;">{{trv_seller}}</td></tr></tbody></table><hr /><p>{{trv_products}}</p>';

	$alphabet = "abcdefghijklmnopqrstuvwxyz";
	$tokenTRVCloud = $alphabet[rand(0, 25)] . rand(0, 99) . $alphabet[rand(0, 25)] . $alphabet[rand(0, 25)] . rand(0, 99) . $alphabet[rand(0, 25)] . rand(0, 99);

	$sql14 = "INSERT INTO trvsol_configuration (configName, value)
	VALUES ('businessName', '" . $_POST["prepareSystemBusinessName"] . "'),
	('templateInvoice', '" . $_POST["prepareSystemSaleTemplate"] . "'),
	('templateDayReport', '" . $_POST["prepareSystemCloseTemplate"] . "'),
	('templateMonthlyReport', '$templateMonth'),
	('numInvoice', '1'),
	('prefixNumInvoice', ''),
	('adminEmail', '" . $_POST["prepareSystemBusinessEmail"] . "'),
	('sendAutoReports', '0'),
	('allowNegativeInventory', '1'),
	('deleteInvoiceNumbersAuto', ''),
	('discountLimit', '0'),
	('changePriceLessOriginal', '0'),
	('version', '" . $versionInstalled . "'),
	('newPaymentMethod', ''),
	('lowStockNotification', '0'),
	('changeTickets', '0'),
	('changeTicketsTemplate', '$templateChangeTickets'),
	('changeTicketsPrintDefault', '1'),
	('changeTicketsExpireDays', '30'),
	('trvCloudActive', '0'),
	('trvCloudToken', '" . $tokenTRVCloud . "'),
	('printingAuto', '0'),
	('printingAutoPrinterName', ''),
	('printingHeadingInfo', ''),
	('printingFooterThanksMsg', 'Thank you for your purchase, come back soon'),
	('printingFooterInfo', ''),
	('printingFooterBarcode', '0'),
	('printingOpenDrawer', '1'),
	('printingOpenDrawerCard', '0'),
	('saveInvoicesForMonths', '4')";
	$conn->query($sql14);

	$configuracionAplicada = true;
} else {
	$existeError = true;
}

$varsSend = array(
	'errores' => $existeError,
	'configuracion_aplicada' => $configuracionAplicada
);
echo json_encode(convertJson($varsSend));
?>