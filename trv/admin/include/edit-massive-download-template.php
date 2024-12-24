<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/prods-export/SimpleXLSXGen.php";

use Shuchkin\SimpleXLSXGen;

$existeError = false;

if (isset($_POST["downloadTemplateToken"]) && $_POST["downloadTemplateToken"] == "exz27") {
	$data = [
		['<center><b>INSTRUCTIONS FOR MASSIVE PRODUCT UPLOAD</b></center>'],
		[''],
		['In this file you will find a template to massively upload products to your catalog in your POS system.'],
		['<b>Please read these instructions carefully to avoid conflicts when importing products.</b>'],
		[''],
		['When you enter the “Product List” sheet you will find the corresponding fields for importing items. It consists of 5 columns:'],
		[''],
		['', '--->', '--->', '--->', '--->', ''],
		['<center><b>ID (DO NOT CHANGE)</b></center>', '<center><b>Product name</b></center>', '<center><b>Selling price</b></center>', '<center><b>Purchase price</b></center>', '<center><b>Category</b></center>', '<center><b>Barcode</b></center>'],
		['<b>Please DO NOT MODIFY this column.</b>', 'Enter the name of the item.', 'Enter the selling price including taxes.', 'Enter the purchase price of the item.', 'Enter the corresponding category code.', 'Product barcode.'],
		['<b>This is the identifier for each of the products.</b>', '', 'DO NOT include dots or commas.', 'DO NOT include dots or commas.', '<b>Log in to your POS System to see the available codes.</b>', 'Preferably without spaces or special characters.'],
		[''],
		[''],
		['<center><b>RECOMMENDATIONS</b></center>'],
		[''],
		['Please do not modify the headers, otherwise the product update will be skipped.'],
		[''],
		['Do not leave blank spaces, all fields are mandatory. If the selling price is 0, write that value in the box.'],
		[''],
		['Check the category of the products to be added, if it does not exist, the item(s) will not be modified.']
	];

	$data2 = [
		['<center><b>ID (DO NOT CHANGE)</b></center>', '<center><b>Product name</b></center>', '<center><b>Selling price</b></center>', '<center><b>Purchase price</b></center>', '<center><b>Category</b></center>', '<center><b>Barcode</b></center>']
	];

	$sql = "SELECT * FROM trvsol_products";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			array_push($data2, ["<left>" . $row["id"] . "</left>", "<left>" . $row["nombre"] . "</left>", "<right>" . $row["precio"] . "</right>", "<right>" . $row["purchasePrice"] . "</right>", "<right>" . $row["categoryID"] . "</right>", "<right>" . $row["barcode"] . "</right>"]);
		}
	}

	$xlsx = new SimpleXLSXGen();
	$xlsx->addSheet($data, 'Instructions');
	$xlsx->addSheet($data2, 'Products list');
	$xlsx->setDefaultFont('Arial');
	$xlsx->setDefaultFontSize(12);
	$xlsx->downloadAs('edicion-masiva-productos-' . date("Y-m-d-h-ia") . '.xlsx');
} else {
	$existeError = true;
}

if ($existeError == true) {
	echo "There was an error generating the template<br><a href='/trv/home.php'>Return to home</a>";
}
?>