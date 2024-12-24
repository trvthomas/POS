<?php include_once "include/verifySession.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/libraries/PHPColors.php";

use Mexitek\PHPColors\Color;

$venderSinStock = 1;
$cambiarAMenorPrecio = 1;
$limiteDescuento = 0;
$metodoPagoPersonalizado = "";
$ticketsCambioActivos = 0;
$ticketsCambioDefault = 0;
$cloudServiceActive = 0;

$sql = "SELECT * FROM trvsol_configuration WHERE configName= 'allowNegativeInventory' OR configName= 'changePriceLessOriginal' OR configName= 'discountLimit' OR configName= 'newPaymentMethod' OR configName= 'changeTickets' OR configName= 'changeTicketsPrintDefault' OR configName= 'trvCloudActive'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($row["configName"] == "allowNegativeInventory") {
			$venderSinStock = $row["value"];
		} else if ($row["configName"] == "changePriceLessOriginal") {
			$cambiarAMenorPrecio = $row["value"];
		} else if ($row["configName"] == "discountLimit") {
			$limiteDescuento = $row["value"];
		} else if ($row["configName"] == "newPaymentMethod") {
			$metodoPagoPersonalizado = $row["value"];
		} else if ($row["configName"] == "changeTickets") {
			$ticketsCambioActivos = $row["value"];
		} else if ($row["configName"] == "changeTicketsPrintDefault") {
			$ticketsCambioDefault = $row["value"];
		} else if ($row["configName"] == "trvCloudActive") {
			$cloudServiceActive = $row["value"];
		}
	}
}

//Product & categories list
$showFeaturedProds = "";
$showCategories = "";

//Best sellers
$sql2 = "SELECT trvsol_products.*, trvsol_categories.color AS category_color FROM trvsol_products INNER JOIN trvsol_categories ON trvsol_products.categoryID = trvsol_categories.id WHERE trvsol_products.activo= 1 AND trvsol_products.ventasMensuales > 0 ORDER BY trvsol_products.ventasMensuales DESC LIMIT 4";
$result2 = $conn->query($sql2);

if ($result2->num_rows > 0) {
	while ($row2 = $result2->fetch_assoc()) {
		$onclick = "addProduct(" . $row2["id"] . ", '" . $row2["nombre"] . "', " . $row2["precio"] . ", " . $row2["stock"] . ", " . $row2["variable_price"] . ")";

		$imgProd = "/trv/media/imagen-no-disponible.png";
		if ($row2["imagen"] != "") {
			$imgProd = $row2["imagen"];
		}

		$showFeaturedProds .= '<div class= "column is-one-quarter-tablet is-half-mobile">
		<div class= "box p-2 is-shadowless is-clickable productButtonsNew" onclick= "' . $onclick . '" style= "--prod-border-color: ' . $row2["category_color"] . '; --prod-text-color: #19191a;">
		<div class= "has-text-centered"><img src= "' . $imgProd . '" alt= ""></div>
		
		<h4 class= "is-size-5">' . $row2["nombre"] . '</h4>
		<p><b>$' . number_format($row2["precio"], 0, ",", ".") . '</b></p>
		</div>
	</div>';
	}
}

//Categories
$sql3 = "SELECT * FROM trvsol_categories";
$result3 = $conn->query($sql3);

if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		$secondColor = "#fff";
		$originalColor = new Color($row3["color"]);
		if ($originalColor->isLight()) {
			$secondColor = $originalColor->darken(35);
		} else {
			$secondColor = $originalColor->lighten(35);
		}

		$onclickCategory = "getProdsCategory(" . $row3["id"] . ", '" . $row3["nombre"] . "', '')";

		$showCategories .= '<div class= "column is-one-third-tablet is-half-mobile">
		<div class= "box p-4 boxShadowHover is-clickable" style= "background-color: ' . $row3["color"] . ';" onclick= "' . $onclickCategory . '">
		<div class= "columns is-mobile">
		<div class= "column is-narrow is-size-5" style= "background-color: #' . $secondColor . '; border-radius: 6px 0 0 6px;">
			<span>' . $row3["emoji"] . '</span>
		</div>
		
		<div class= "column has-text-left" style= "border: 2px solid #' . $secondColor . '; color: #' . $secondColor . '; border-radius: 0 6px 6px 0;">
			<h4 class= "is-size-5 mb-1">' . $row3["nombre"] . '</h4>
		</div>
		</div>
		</div>
	</div>';
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>New sale</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body onbeforeunload="return confirmationExit()">
	<?php include_once "include/header.php"; ?>

	<div class="contentBox">
		<a class="button is-small is-pulled-left" href="/trv/home.php"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></a>

		<div class="buttons is-centered">
			<div id="actionButtons">
				<button class="button is-danger" onclick="window.location = '/trv/new-invoice.php';"><i class="fas fa-times"></i> Cancel sale</button>
				<button class="button backgroundDark" onclick="document.getElementById('overlayPriceList').style.display= 'block';"><i class="fas fa-tags"></i> Price check</button>
			</div>

			<div id="newSaleButton" style="display: none">
				<button class="button backgroundDark" onclick="window.location = '/trv/new-invoice.php';"><i class="fas fa-circle-plus"></i> New sale</button>
			</div>
		</div>

		<div class="columns">
			<div class="column is-three-fifths">
				<div id="sectionProductos">
					<div class="box fade has-text-centered mb-2">
						<h3 class="is-size-4" style="color: var(--dark-color)">Products</h3>
						<hr>

						<div class="field has-addons">
							<div class="control" style="width: 40%;">
								<label class="label">Change price</label>
							</div>

							<div class="control is-expanded">
								<label class="label">Search product</label>
							</div>
						</div>

						<div class="field has-addons">
							<div class="control has-icons-left" style="width: 40%;">
								<input type="number" class="input" placeholder="e.g. 50, 300" id="otroValorProdInput" step="1">
								<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
							</div>

							<div class="control has-icons-left is-expanded">
								<input type="text" class="input" placeholder="Search or scan barcode" id="codigosInput" onkeydown="onup()" onkeyup="this.value = this.value.toUpperCase();" autofocus>
								<span class="icon is-small is-left"><i class="fas fa-barcode"></i></span>
							</div>
						</div>

						<button class="button backgroundDark" onclick="openProdSelection()"><i class="fas fa-tshirt"></i> Products list</button>
						<button class="button is-light" onclick="showOtherProd()"><i class="fas fa-random"></i> Other product</button>
					</div>

					<div class="box fade has-text-left" id="boxResumenProductos" style="display: none">
						<div class="content" id="resumenProductos"></div>
					</div>
				</div>

				<div class="box fade has-text-centered" id="sectionCambio" style="display: none">
					<h3 class="is-size-4" style="color: var(--dark-color)">Extras</h3>
					<hr>

					<div class="fade" id="sectionCambioTxt">
						<i class="fas fa-coins fa-5x mb-2"></i>

						<h3 class="is-size-5 mb-0">Select a payment method</h3>
						<p>Select a payment method to display these options</p>
					</div>


					<div id="sectionCambioInput" style="display: none">
						<div class="columns">
							<div class="column">
								<div class="field">
									<label class="label">Received</label>
									<div class="control has-icons-left">
										<input type="number" class="input" placeholder="e.g. 50, 300" id="cambioInput" oninput="updateCambio()" step="1" min="0">
										<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
									</div>
								</div>
							</div>

							<div class="column">
								<div class="field">
									<label class="label">Optional notes</label>
									<div class="control has-icons-left">
										<input type="text" class="input" placeholder="e.g. Deposit, No change" id="notasInput" maxlength="200">
										<span class="icon is-small is-left"><i class="fas fa-comment-dots"></i></span>
									</div>
								</div>
							</div>

							<div class="column" <?php if ($ticketsCambioActivos != 1) {
													echo 'style= "display: none"';
												} ?>>
								<div class="field">
									<label class="label">Gift tickets to print</label>
									<div class="control has-icons-left">
										<input type="number" class="input" placeholder="e.g. 1, 5" id="numeroTicketsCambioPrint" min="0" max="5" value="<?php echo $ticketsCambioDefault; ?>">
										<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="box fade has-text-centered" id="sectionImpresiones" style="display: none">
					<h3 class="is-size-4" style="color: var(--dark-color)">Prints</h3>
					<hr>

					<div class="fade" id="sectionImpresionesTxt">
						<i class="fas fa-coins fa-5x mb-2"></i>

						<h3 class="is-size-5 mb-0">Select a payment method</h3>
						<p>Select a payment method to display these options</p>
					</div>


					<div id="sectionImpresionesButtons" style="display: none">
						<div id="optionsPrinting1">
							<div class="columns is-multiline is-centered">
								<div class="column is-half">
									<div class="box is-shadowless is-clickable pastel-bg-green" onclick="createInvoice(1)">
										<span class="icon is-large"><i class="fas fa-print fa-2x"></i></span>
										<p><b>Print (F9)</b></p>
									</div>
								</div>

								<div class="column is-half">
									<div class="box is-shadowless is-clickable pastel-bg-yellow" onclick="createInvoice(0)">
										<span class="icon is-large"><i class="fas fa-circle-plus fa-2x"></i></span>
										<p><b>Register without printing (F10)</b></p>
									</div>
								</div>
							</div>
						</div>

						<div id="optionsPrinting2" style="display: none">
							<div class="columns is-centered">
								<div class="column is-half">
									<div class="box is-shadowless is-clickable has-background-success-light" onclick="printInvoice()" id="btnOnlyPrint">
										<span class="icon is-large"><i class="fas fa-print fa-2x"></i></span>
										<p><b>Print</b></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="column">
				<div id="sectionPayment">
					<div class="box fade mb-2">
						<div class="columns has-text-left">
							<div class="column">
								<div class="block">
									<span class="icon is-large is-pulled-left"><i class="fas fa-hand-holding-dollar fa-2x"></i></span>
									<h4 class="is-size-6 has-text-grey">To charge</h4>
									<p class="is-size-5 has-text-success"><b>$<span id="resumenCobrar">0</span></b></p>
								</div>
							</div>

							<div class="column">
								<div class="block">
									<span class="icon is-large is-pulled-left"><i class="fas fa-arrow-right-arrow-left fa-2x"></i></span>
									<h4 class="is-size-6 has-text-grey">Change</h4>
									<p class="is-size-5"><b>$<span id="resumenCambio">0</span></b></p>
								</div>
							</div>
						</div>

						<div class="buttons is-centered" style="display: none" id="additionalButtons">
							<button class="button backgroundDark" onclick="changePayment()"><i class="fas fa-money-bill-transfer"></i> Change payment method</button>
							<button class="button is-warning" onclick="addMoreProds()"><i class="fas fa-circle-plus"></i> Add more products</button>
						</div>
					</div>

					<div class="box fade has-text-centered">
						<h3 class="is-size-4" style="color: var(--dark-color)">Payment Method</h3>
						<hr>

						<div id="methodSelection">
							<div class="columns is-multiline is-centered" style="word-break: break-word;">
								<div class="column is-half">
									<div class="box is-shadowless is-clickable pastel-bg-green" onclick="selectMethod('E')">
										<span class="icon is-large"><i class="fas fa-coins fa-2x"></i></span>
										<p><b>Cash payment (F5)</b></p>
									</div>
								</div>

								<div class="column is-half">
									<div class="box is-shadowless is-clickable pastel-bg-purple" onclick="selectMethod('T')">
										<span class="icon is-large"><i class="fas fa-credit-card fa-2x"></i></span>
										<p><b>Card payment (F6)</b></p>
									</div>
								</div>

								<div class="column is-half">
									<div class="box is-shadowless is-clickable pastel-bg-darkorange" onclick="selectMethod('M')">
										<span class="icon is-large"><i class="fas fa-money-check-alt fa-2x"></i></span>
										<p><b>Multi-payment (F7)</b></p>
									</div>
								</div>

								<?php if ($metodoPagoPersonalizado != "") { ?>
									<div class="column is-half">
										<div class="box is-shadowless is-clickable pastel-bg-cyan" onclick="selectMethod('O')">
											<span class="icon is-large"><i class="fas fa-wallet fa-2x"></i></span>
											<p><b><?php echo $metodoPagoPersonalizado; ?></b></p>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="buttons is-centered mt-2">
							<button class="button backgroundDark" style="display: none" id="discountSelection" onclick="openDiscountSelection()"><i class="fas fa-percentage"></i> Apply discount</button>
							<button class="button is-danger" style="display: none" id="discountDeletion" onclick="deleteDiscounts()"><i class="fas fa-percentage"></i> Remove discount</button>
						</div>

						<div id="optionsMultipago" style="display: none">
							<div class="block">
								<h4 class="is-size-6 has-text-grey">Missing</h4>
								<p class="is-size-5"><b>$<span id="faltanteMultipago">0</span></b></p>
							</div>

							<div class="field">
								<label class="label">Received in <b>cash <i class="fas fa-coins fa-sm"></i></b></label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50, 300" id="multipagoEfectivo" oninput="updateMultipago()" step="1" min="0">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>

							<div class="field">
								<label class="label">Received in <b>card <i class="fas fa-credit-card fa-sm"></i></b></label>
								<div class="control has-icons-left">
									<input type="number" class="input" placeholder="e.g. 50, 300" id="multipagoTarjeta" oninput="updateMultipago()" step="1" min="0">
									<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
								</div>
							</div>

							<?php if ($metodoPagoPersonalizado != "") { ?>
								<div class="field">
									<label class="label">Received in <b><?php echo $metodoPagoPersonalizado; ?> <i class="fas fa-wallet fa-sm"></i></b></label>
									<div class="control has-icons-left">
										<input type="number" class="input" placeholder="e.g. 50, 300" id="multipagoOther" oninput="updateMultipago()" step="1" min="0">
										<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="fade has-text-centered" id="recommendedActions" style="display: none">
					<div class="columns is-multiline is-centered">
						<div class="column is-full">
							<a href="/trv/new-invoice.php">
								<div class="box pastel-bg-green">
									<h3 class="is-size-5"><i class="is-pulled-left fas fa-receipt fa-2x"></i> New sale (F1)</h3>
								</div>
							</a>
						</div>

						<div class="column is-full">
							<a href="/trv/reports.php">
								<div class="box">
									<h3 class="is-size-5"><i class="is-pulled-left fas fa-pencil-alt fa-2x"></i> Reports</h3>
								</div>
							</a>
						</div>

						<div class="column is-full">
							<a href="/trv/sales.php">
								<div class="box pastel-bg-cyan">
									<h3 class="is-size-5"><i class="is-pulled-left fas fa-coins fa-2x"></i> Day statistics</h3>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include_once "include/footer.php"; ?>

	<div id="overlaySelectProduct1" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct1').style.display='none'"></span>

			<div class="trvModal-elements">
				<div class="columns is-multiline is-mobile is-centered mt-1">
					<?php echo $showFeaturedProds; ?>
				</div>

				<label class="label">Search products</b></label>
				<div class="field has-addons">
					<div class="control has-icons-left is-expanded">
						<input type="text" class="input" placeholder="Search by name or price" id="searchProductInput" onkeydown="onup2()">
						<span class="icon is-small is-left"><i class="fas fa-magnifying-glass"></i></span>
					</div>

					<div class="control">
						<button class="button backgroundDark" onclick="searchProduct()"><i class="fas fa-magnifying-glass"></i></button>
					</div>
				</div>

				<div class="columns is-mobile is-multiline is-centered mt-1">
					<?php echo $showCategories; ?>
				</div>
			</div>
		</div>
	</div>

	<div id="overlaySelectProduct2" class="trvModal">
		<div class="trvModal-content trvModal-content-large">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct2').style.display='none'"></span>

			<div class="trvModal-header">
				<button class="button is-small is-pulled-left" onclick="backCategorySelection()"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></button>
				<h3 class="is-size-3 mb-1" id="txtCategory">ERROR</h3>
			</div>

			<div class="trvModal-elements">
				<div class="columns is-multiline is-mobile is-centered mt-1 has-text-left" id="divProductsList"></div>
			</div>
		</div>
	</div>

	<div id="overlaySelectProduct3" class="trvModal">
		<div class="trvModal-content trvModal-content">
			<span class="delete" onclick="document.getElementById('overlaySelectProduct3').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Select a price</h3>
			</div>

			<div class="trvModal-elements">
				<div class="columns is-multiline is-mobile is-centered mt-1" id="divProductPrices"></div>
			</div>
		</div>
	</div>

	<div id="overlayDescuento" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayDescuento').style.display='none'"></span>

			<div class="fade" id="selectDiscountType">
				<div class="trvModal-header">
					<h3 class="is-size-3 mb-1">Select the type of discount you want to apply</h3>
				</div>

				<div class="trvModal-elements">
					<div class="columns is-multiline is-centered">
						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-white-ter" onclick="document.getElementById('overlayDescuento').style.display= 'none';">
								<span class="icon is-large"><i class="fas fa-times fa-2x"></i></span>
								<p><b>No discount</b></p>
							</div>
						</div>

						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-success-light" onclick="applyDiscount(1)">
								<span class="icon is-large"><i class="fas fa-dollar-sign fa-2x"></i></span>
								<p><b>Value discount</b></p>
							</div>
						</div>

						<div class="column is-one-third">
							<div class="box is-shadowless is-clickable has-background-warning-light" onclick="applyDiscount(2)">
								<span class="icon is-large"><i class="fas fa-tags fa-2x"></i></span>
								<p><b>Coupon</b></p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="fade" id="applyDiscount1">
				<div class="trvModal-header">
					<button class="button is-small is-pulled-left" onclick="openDiscountSelection()"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></button>
					<h3 class="is-size-3 mb-1">Value discount</h3>
				</div>

				<div class="trvModal-elements">
					<div class="buttons are-medium is-centered">
						<button class="button backgroundDark" title="Add $1" onclick="applyValueDiscount(1)">$1</button>
						<button class="button backgroundDark" title="Add $2" onclick="applyValueDiscount(2)">$2</button>
						<button class="button backgroundDark" title="Add $3" onclick="applyValueDiscount(3)">$3</button>
						<button class="button backgroundDark" title="Add $5" onclick="applyValueDiscount(5)">$5</button>
						<button class="button backgroundDark" title="Add $10" onclick="applyValueDiscount(10)">$10</button>
						<button class="button backgroundDark" title="Reset value" onclick="resetDiscount()"><i class="fas fa-trash-can"></i></button>
					</div>

					<div id="discountValueActions" style="display: none">
						<div class="columns has-text-left">
							<div class="column">
								<div class="block">
									<span class="icon is-large is-pulled-left"><i class="fas fa-arrow-trend-down fa-2x"></i></span>
									<h4 class="is-size-6 has-text-grey">Total discount</h4>
									<p class="is-size-5"><b>$<span id="discountTxtTotal">0</span></b></p>
								</div>
							</div>

							<div class="column">
								<div class="block">
									<span class="icon is-large is-pulled-left"><i class="fas fa-hand-holding-dollar fa-2x"></i></span>
									<h4 class="is-size-6 has-text-grey">New total value</h4>
									<p class="is-size-5 has-text-success"><b>$<span id="discountTxtNewValue">0</span></b></p>
								</div>
							</div>
						</div>

						<div class="columns mt-5">
							<div class="column">
								<button class="button is-fullwidth backgroundDark" onclick="confirmDiscounts()">Apply discount</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="fade" id="applyDiscount2">
				<div class="trvModal-header">
					<button class="button is-small is-pulled-left" onclick="openDiscountSelection()"><span class="icon is-small"><i class="fas fa-chevron-left"></i></span></button>
					<h3 class="is-size-3 mb-1">Coupon discount</h3>
				</div>

				<div class="trvModal-elements">
					<div class="field">
						<label class="label"><b>Coupon code</b></label>
						<div class="control has-icons-left">
							<input type="text" class="input" placeholder="e.g. 10OFF, SUMMERSALE" id="applyVoucherCode" onkeydown="onup3()" onkeyup="this.value = this.value.toUpperCase();">
							<span class="icon is-small is-left"><i class="fas fa-tag"></i></span>
						</div>
					</div>

					<div class="columns mt-5">
						<div class="column">
							<button class="button is-fullwidth backgroundDark" onclick="applyVoucher()">Apply discount</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlayOtherProduct" class="trvModal">
		<div class="trvModal-content trvModal-content-small">
			<span class="delete" onclick="document.getElementById('overlayOtherProduct').style.display='none'"></span>

			<div class="trvModal-header">
				<h3 class="is-size-3 mb-1">Add temporary product</h3>
			</div>

			<div class="trvModal-elements">
				<p>Add a product that is not available in the catalog <b>only for this sale</b>.</p>

				<div class="columns">
					<div class="column">
						<div class="field">
							<label class="label"><b>Product name</b></label>
							<div class="control has-icons-left">
								<input type="text" class="input" placeholder="e.g. T-shirt, Glasses" id="otherProdNameInput">
								<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
							</div>
						</div>
					</div>

					<div class="column">
						<div class="field">
							<label class="label"><b>Selling price (taxes included)</b></label>
							<div class="control has-icons-left">
								<input type="number" class="input" placeholder="e.g. 50, 300" id="otherProdPriceInput" step="1" min="0">
								<span class="icon is-small is-left"><i class="fas fa-dollar-sign"></i></span>
							</div>
						</div>
					</div>
				</div>

				<div class="columns mt-5">
					<div class="column">
						<button class="button is-fullwidth is-light is-danger" onclick="document.getElementById('overlayOtherProduct').style.display='none'">Cancel</button>
					</div>
					<div class="column">
						<button class="button is-fullwidth backgroundDark" onclick="addOtherProduct()">Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="overlayPriceList" class="trvModal">
		<div class="trvModal-content">
			<span class="delete" onclick="document.getElementById('overlayPriceList').style.display='none'"></span>

			<div class="trvModal-elements">
				<iframe src="/trv/price-check.php?hide_header=true" height="900" width="100%" style="border: none"></iframe>
			</div>
		</div>
	</div>

	<form method="POST" action="/trv/include/product-selection-1.php" style="display: none" id="prodSelection1Form" onsubmit="return prodSelection1Return();">
		<input name="prodSelection1Category" id="prodSelection1Category" readonly>
		<input name="prodSelection1Search" id="prodSelection1Search" readonly>
		<input type="submit" id="prodSelection1Send" value="Send">
	</form>

	<form method="POST" action="/trv/include/product-selection-2-variable.php" style="display: none" id="prodSelection2Form" onsubmit="return prodSelection2Return();">
		<input name="prodSelection2IdProd" id="prodSelection2IdProd" readonly>
		<input type="submit" id="prodSelection2Send" value="Send">
	</form>

	<form method="POST" action="/trv/include/search-barcode-product.php" style="display: none" id="searchBarcodeForm" onsubmit="return searchBarcodeReturn();">
		<input name="searchBarcodeQuery" id="searchBarcodeQuery" readonly>
		<input type="submit" id="searchBarcodeSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/search-voucher-info.php" style="display: none" id="searchVoucherForm" onsubmit="return searchVoucherReturn();">
		<input name="searchVoucherCode" id="searchVoucherCode" readonly>
		<input name="searchVoucherPaymentMethod" id="searchVoucherPaymentMethod" readonly>
		<input name="searchVoucherSubtotal" id="searchVoucherSubtotal" readonly>
		<input type="submit" id="searchVoucherSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/new-invoice.php" style="display: none" id="createSaleForm" onsubmit="return createSaleReturn();">
		<input name="createSaleProducts" id="createSaleProducts" readonly>
		<input name="createSaleProductsArray" id="createSaleProductsArray" readonly>
		<input name="createSaleProductsArrayAuto" id="createSaleProductsArrayAuto" readonly>
		<input name="createSalePayment" id="createSalePayment" readonly>
		<input name="createSaleSubtotal" id="createSaleSubtotal" readonly>
		<input name="createSaleDiscounts" id="createSaleDiscounts" readonly>
		<input name="createSaleReceived" id="createSaleReceived" readonly>
		<input name="createSaleChange" id="createSaleChange" readonly>
		<input name="createSaleNotes" id="createSaleNotes" readonly>
		<input name="createSaleMultiEf" id="createSaleMultiEf" readonly>
		<input name="createSaleMultiTa" id="createSaleMultiTa" readonly>
		<input name="createSaleMultiOt" id="createSaleMultiOt" readonly>
		<input name="createSaleVoucherID" id="createSaleVoucherID" readonly>
		<input name="createSaleProductsChangeTicket" id="createSaleProductsChangeTicket" readonly>
		<input name="createSaleChangeTicketsNum" id="createSaleChangeTicketsNum" readonly>
		<input name="createSaleAutoNoPrint" id="createSaleAutoNoPrint" readonly>

		<input type="submit" id="createSaleSend" value="Send">
	</form>

	<form method="POST" action="/trv/include/generate-sale-template.php" style="display: none" id="generateTemplateForm" onsubmit="return generateTemplateReturn();">
		<input name="generateTemplateIDInvoice" id="generateTemplateIDInvoice" readonly>
		<input name="generateTemplatePrintOrSend" id="generateTemplatePrintOrSend" readonly>
		<input name="generateTemplateAutoChangeTickets" id="generateTemplateAutoChangeTickets" readonly>
		<input type="submit" id="generateTemplateSend" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script src="/trv/include/get-products.js"></script>
	<script src="/trv/include/cloudService.js"></script>
	<script>
		//Send to backend
		var productList = "",
			productListChangeTicket = "",
			productsArray = [],
			productsArrayAutoPrinting = [];
		//In this page only
		var productListPage = "",
			productsPageArray = [],
			productOrderId = 0;

		var subtotal = 0,
			descuentos = 0,
			total = 0,
			metodoPago = "";
		var wantPrint = 0,
			finalInvoice = "",
			voucherApplied = false,
			copiesChangeTickets = <?php echo $ticketsCambioDefault; ?>;
		var preguntarParaCerrar = false,
			permitirVentaSinStock = <?php echo $venderSinStock; ?>,
			noPermitirCambiarMenorPrecio = <?php echo $cambiarAMenorPrecio; ?>,
			limiteDescuento = <?php echo $limiteDescuento; ?>;
		var printIDInvoice = 0;

		function onup() {
			if (event.keyCode === 13) {
				searchBarcode();
			}
		}

		function onup2() {
			if (event.keyCode === 13) {
				searchProduct();
			}
		}

		function onup3() {
			if (event.keyCode === 13) {
				applyVoucher();
			}
		}

		function openProdSelection() {
			document.getElementById('overlaySelectProduct1').style.display = 'block';
			document.getElementById('searchProductInput').value = '';
		}

		function searchBarcode() {
			var barcode = document.getElementById('codigosInput').value;

			if (barcode == "") {
				openProdSelection();
			} else {
				document.getElementById('searchBarcodeQuery').value = barcode;
				document.getElementById('searchBarcodeSend').click();

				openLoader();
			}
		}

		function searchProduct() {
			var searchInput = document.getElementById('searchProductInput').value;

			if (searchInput != "") {
				getProdsCategory('', 'Search results', searchInput);
			}
		}

		function addProduct(idProd, nombreProd, precioProd, stockProd, priceVariable) {
			if (priceVariable == 1) {
				document.getElementById('prodSelection2IdProd').value = idProd;
				document.getElementById('prodSelection2Send').click();

				document.getElementById('overlaySelectProduct3').style.display = "block";
				document.getElementById('divProductPrices').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';

				document.getElementById('overlaySelectProduct1').style.display = "none";
				document.getElementById('overlaySelectProduct2').style.display = "none";
				document.getElementById('divProductsList').innerHTML = "";
			} else {
				var totalUnits = 0;
				for (var x = 0; x < productsArray.length; x++) {
					if (productsArray[x] == idProd) {
						totalUnits++;
					}
				}

				if (permitirVentaSinStock == 0 && totalUnits >= stockProd) {
					newNotification('No more units available for sale', 'error');
				} else {
					var newValue = document.getElementById('otroValorProdInput').value;
					newValue++;
					newValue--;
					var finalPrice = precioProd;
					if (newValue != 0) {
						finalPrice = newValue;
					}

					if (noPermitirCambiarMenorPrecio == 1 && newValue != 0 && newValue < finalPrice) {
						newNotification('The new price cannot be less than the original', 'error');
					} else {
						var prodExists = false;
						for (var x2 = 0; x2 < productsPageArray.length; x2++) {
							if (productsPageArray[x2]["id"] == idProd && productsPageArray[x2]["price"] == finalPrice) {
								productsPageArray[x2].quantity += 1;
								productsPageArray[x2].totalPrice += finalPrice;
								prodExists = true;
								break;
							}
						}

						if (prodExists == false) {
							productsPageArray.push({
								id: idProd,
								name: nombreProd,
								price: finalPrice,
								totalPrice: finalPrice,
								stock: stockProd,
								quantity: 1,
								numberProdSale: productOrderId
							});
							productOrderId++;
						}

						subtotal += finalPrice;

						preguntarParaCerrar = true;
						updateValues();
						document.getElementById('overlaySelectProduct1').style.display = "none";
						document.getElementById('overlaySelectProduct2').style.display = "none";
						document.getElementById('overlaySelectProduct3').style.display = "none";
						document.getElementById('otroValorProdInput').value = "";
						document.getElementById('divProductsList').innerHTML = "";
					}
				}
			}
		}

		function updateValues() {
			productList = "", productListPage = "", productListChangeTicket = "", productsArray = [], productsArrayAutoPrinting = [];

			for (var x = 0; x < productsPageArray.length; x++) {
				var onclickNewUnit = 'addProduct(' + productsPageArray[x]["id"] + ', "' + productsPageArray[x]["name"] + '", ' + productsPageArray[x]["price"] + ', ' + productsPageArray[x]["stock"] + ')';
				var onclickDelete = 'deleteProduct(' + productsPageArray[x]["numberProdSale"] + ', "' + productsPageArray[x]["price"] + '")';
				var onclickMinus1Unit = 'subtractUnit(' + productsPageArray[x]["numberProdSale"] + ', "' + productsPageArray[x]["price"] + '")';

				if (productsPageArray[x]["quantity"] > 1) {
					productList += '<p class= "pdfClassProd"><b>' + productsPageArray[x]["name"] + '<span class= "prodListPrice">$' + thousands_separators(productsPageArray[x]["totalPrice"]) + '</span></b><br>' + productsPageArray[x]["quantity"] + ' x $' + thousands_separators(productsPageArray[x]["price"]) + '</p>';

					productListPage += "<p class= 'is-size-5'><b>" + productsPageArray[x]["name"] + "<span class= 'prodListPrice'>$" + thousands_separators(productsPageArray[x]["totalPrice"]) + " <i class='fas fa-circle-plus fa-fw is-clickable' style= 'margin-right:2px;margin-left:2px;' title= 'Add 1 unit' onclick= '" + onclickNewUnit + "'></i> <i class='fas fa-circle-minus fa-fw is-clickable' style= 'margin-left:2px;' title= 'Remove 1 unit' onclick= '" + onclickMinus1Unit + "'></i></span></b><br>" + productsPageArray[x]["quantity"] + " x $" + thousands_separators(productsPageArray[x]["price"]) + "</p>";

					productListChangeTicket += '<p><b>' + productsPageArray[x]["name"] + '</b><br>' + productsPageArray[x]["quantity"] + ' unidades</p>';

					productsArrayAutoPrinting.push({
						line1: productsPageArray[x]["name"] + "{{new_line}}",
						line2: productsPageArray[x]["quantity"] + " x $" + thousands_separators(productsPageArray[x]["price"]) + " = $" + thousands_separators(productsPageArray[x]["totalPrice"]) + "{{new_line}}"
					});
				} else {
					productList += '<p class= "pdfClassProd"><b>' + productsPageArray[x]["name"] + '<span class= "prodListPrice">$' + thousands_separators(productsPageArray[x]["totalPrice"]) + '</span></b></p>';

					productListPage += "<p class= 'is-size-5'><b>" + productsPageArray[x]["name"] + "<span class= 'prodListPrice'>$" + thousands_separators(productsPageArray[x]["totalPrice"]) + " <i class='fas fa-circle-plus fa-fw is-clickable' style= 'margin-right:2px;margin-left:2px;' title= 'Add 1 unit' onclick= '" + onclickNewUnit + "'></i> <i class='fas fa-trash-can fa-fw is-clickable' style= 'margin-left:2px;' title= 'Delete from the list' onclick= '" + onclickDelete + "'></i></span></b></p>";

					productListChangeTicket += '<p><b>' + productsPageArray[x]["name"] + '</b></p>';

					productsArrayAutoPrinting.push({
						line1: productsPageArray[x]["name"] + "{{new_line}}",
						line2: "$" + thousands_separators(productsPageArray[x]["totalPrice"]) + "{{new_line}}"
					});
				}

				for (var x2 = 0; x2 < productsPageArray[x]["quantity"]; x2++) {
					productsArray.push(productsPageArray[x]["id"]);
				}
			}

			total = subtotal - descuentos;
			document.getElementById('resumenProductos').innerHTML = productListPage;
			document.getElementById('resumenCobrar').innerHTML = thousands_separators(total);
			if (productListPage != "") {
				document.getElementById('boxResumenProductos').style.display = 'block';
			} else {
				document.getElementById('boxResumenProductos').style.display = 'none';
			}
		}

		function deleteProduct(idUnique, priceProd) {
			for (var x = 0; x < productsPageArray.length; x++) {
				if (productsPageArray[x]["numberProdSale"] == idUnique && productsPageArray[x]["price"] == priceProd) {
					var positionProd = productsPageArray.map(function(e) {
						return e.numberProdSale;
					}).indexOf(idUnique);

					productsPageArray.splice(positionProd, 1);
					subtotal -= priceProd;
					break;
				}
			}

			updateValues();
		}

		function subtractUnit(idUnique, priceProd) {
			for (var x = 0; x < productsPageArray.length; x++) {
				if (productsPageArray[x]["numberProdSale"] == idUnique && productsPageArray[x]["price"] == priceProd) {
					subtotal -= productsPageArray[x]["price"];
					productsPageArray[x].quantity -= 1;
					productsPageArray[x].totalPrice -= productsPageArray[x]["price"];
					break;
				}
			}

			updateValues();
		}

		function selectMethod(methodSelected) {
			updateValues();

			if (productsArray.length <= 0) {
				newNotification('Select at least one (1) product', 'error');
			} else if (voucherApplied != true) {
				metodoPago = methodSelected;

				if (methodSelected == "M") {
					document.getElementById('optionsMultipago').style.display = "block";
				} else {
					document.getElementById('optionsMultipago').style.display = "none";
				}

				document.getElementById("cambioInput").value = total;
				document.getElementById('methodSelection').style.display = "none";
				if (descuentos == 0) {
					document.getElementById('discountSelection').style.display = "block";
				}
				document.getElementById('additionalButtons').style.display = "";

				document.getElementById('sectionProductos').style.display = "none";
				document.getElementById('sectionCambio').style.display = "block";
				document.getElementById('sectionImpresiones').style.display = "block";
				document.getElementById('sectionCambioInput').style.display = "block";
				document.getElementById('sectionCambioTxt').style.display = "none";

				if (methodSelected == "E" || methodSelected == "O") {
					document.getElementById('cambioInput').disabled = false;
				} else {
					document.getElementById('cambioInput').disabled = true;
				}
				document.getElementById('sectionImpresionesButtons').style.display = "block";
				document.getElementById('sectionImpresionesTxt').style.display = "none";
			}
		}

		function changePayment() {
			document.getElementById('methodSelection').style.display = "block";
			document.getElementById('discountSelection').style.display = "none";
			document.getElementById('discountDeletion').style.display = "none";
			document.getElementById('additionalButtons').style.display = "none";
			document.getElementById('optionsMultipago').style.display = "none";

			document.getElementById('sectionCambioInput').style.display = "none";
			document.getElementById('sectionCambioTxt').style.display = "block";
			document.getElementById('sectionImpresionesButtons').style.display = "none";
			document.getElementById('sectionImpresionesTxt').style.display = "block";
		}

		function addMoreProds() {
			document.getElementById('methodSelection').style.display = "block";
			document.getElementById('discountSelection').style.display = "none";
			document.getElementById('discountDeletion').style.display = "none";
			document.getElementById('additionalButtons').style.display = "none";
			document.getElementById('optionsMultipago').style.display = "none";

			document.getElementById('sectionProductos').style.display = "block";
			document.getElementById('sectionCambio').style.display = "none";
			document.getElementById('sectionImpresiones').style.display = "none";
		}

		function openDiscountSelection() {
			ficticiousDiscounts = 0;
			document.getElementById('overlayDescuento').style.display = 'block';

			document.getElementById('selectDiscountType').style.display = "";
			document.getElementById('applyDiscount1').style.display = "none";
			document.getElementById('applyDiscount2').style.display = "none";
			document.getElementById('applyVoucherCode').value = "";
		}

		function applyDiscount(idDiscount) {
			document.getElementById('selectDiscountType').style.display = "none";
			document.getElementById('applyDiscount1').style.display = "none";
			document.getElementById('applyDiscount2').style.display = "none";

			document.getElementById('applyDiscount' + idDiscount).style.display = "block";

			if (idDiscount == 2) {
				document.getElementById('applyVoucherCode').focus();
			}
		}

		var ficticiousDiscounts = 0;

		function applyValueDiscount(valueDis) {
			var simulationTotalDiscounts = ficticiousDiscounts + valueDis;

			if (simulationTotalDiscounts > subtotal) {
				newNotification('You cannot apply more discounts', 'error');
			} else {
				ficticiousDiscounts += valueDis;
				var simulationNewValue = subtotal - ficticiousDiscounts;

				document.getElementById('discountValueActions').style.display = "block";
				document.getElementById('discountTxtTotal').innerHTML = thousands_separators(ficticiousDiscounts);
				document.getElementById('discountTxtNewValue').innerHTML = thousands_separators(simulationNewValue);
			}
		}

		function resetDiscount() {
			ficticiousDiscounts = 0;

			document.getElementById('discountValueActions').style.display = "none";
			document.getElementById('discountTxtTotal').innerHTML = 0;
			document.getElementById('discountTxtNewValue').innerHTML = 0;
		}

		function confirmDiscounts() {
			if (limiteDescuento != 0 && limiteDescuento != "" && ficticiousDiscounts > limiteDescuento) {
				newNotification('The selected discount is greater than allowed', 'error');
			} else {
				descuentos = ficticiousDiscounts;
				updateValues();

				newNotification('Discount applied', 'success');

				document.getElementById('overlayDescuento').style.display = "none";
				document.getElementById('discountSelection').style.display = "none";
				document.getElementById('discountDeletion').style.display = "block";
				document.getElementById('additionalButtons').style.display = "none";
				document.getElementById("cambioInput").value = total;
				if (metodoPago == "M") {
					updateMultipago();
				}
			}
		}

		function deleteDiscounts() {
			descuentos = 0;
			ficticiousDiscounts = 0;
			voucherApplied = false;
			document.getElementById('createSaleVoucherID').value = "";
			document.getElementById('additionalButtons').style.display = "";

			document.getElementById('discountSelection').style.display = "block";
			document.getElementById('discountDeletion').style.display = "none";
			updateValues();
			document.getElementById("cambioInput").value = total;
			if (metodoPago == "M") {
				updateMultipago();
			}
		}

		function updateMultipago() {
			var recibidoEf = document.getElementById("multipagoEfectivo").value;
			var recibidoTa = document.getElementById("multipagoTarjeta").value;
			var recibidoOt = document.getElementById("multipagoOther").value;
			recibidoEf++;
			recibidoEf--;
			recibidoTa++;
			recibidoTa--;
			recibidoOt++;
			recibidoOt--;

			var faltante = total - (recibidoEf + recibidoTa + recibidoOt);
			document.getElementById('faltanteMultipago').innerHTML = thousands_separators(faltante);

			if (faltante < 0) {
				document.getElementById('faltanteMultipago').style.color = "#ef4d4d";
			} else {
				document.getElementById('faltanteMultipago').style.color = "#19191a";
			}
		}

		function updateCambio() {
			var recibidoCliente = document.getElementById("cambioInput").value;
			recibidoCliente++;
			recibidoCliente--;

			var cambio = recibidoCliente - total;
			document.getElementById('resumenCambio').innerHTML = thousands_separators(cambio);
		}

		function applyVoucher() {
			var voucherCode = document.getElementById('applyVoucherCode').value;

			if (voucherCode == "") {
				newNotification('Enter the coupon code', 'error');
			} else {
				document.getElementById('searchVoucherCode').value = voucherCode;
				document.getElementById('searchVoucherPaymentMethod').value = metodoPago;
				document.getElementById('searchVoucherSubtotal').value = subtotal;

				document.getElementById('searchVoucherSend').click();
				openLoader();
				document.getElementById('overlayDescuento').style.display = "none";
			}
		}

		function createInvoice(printInvoice) {
			var receivedTotal = document.getElementById("cambioInput").value;
			var receivedEf = document.getElementById("multipagoEfectivo").value;
			var receivedTa = document.getElementById("multipagoTarjeta").value;
			var notes = document.getElementById("notasInput").value;
			var modifyChangeTickets = document.getElementById("numeroTicketsCambioPrint").value;
			modifyChangeTickets++;
			modifyChangeTickets--;
			receivedTotal++;
			receivedTotal--;
			receivedEf++;
			receivedEf--;
			receivedTa++;
			receivedTa--;
			var receivedOt = 0;
			<?php if ($metodoPagoPersonalizado != "") { ?>
			receivedOt = document.getElementById("multipagoOther").value;
			receivedOt++;
			receivedOt--;
			<?php } ?>

			var multipagoTotalReceived = receivedEf + receivedTa + receivedOt;

			var totalRecibido = receivedTotal;
			var totalCambio = receivedTotal - total;
			if (metodoPago == "M") {
				totalRecibido = thousands_separators(receivedEf);
				totalCambio = 0;

				if (receivedTa != "") {
					totalRecibido += " Card $" + thousands_separators(receivedTa);
				}
				if (receivedOt != "") {
					totalRecibido += " <?php echo $metodoPagoPersonalizado; ?> $" + thousands_separators(receivedOt);
				}
			}

			if (metodoPago == "M" && (multipagoTotalReceived > total || multipagoTotalReceived < total)) {
				newNotification('Check the received values', 'error');
			} else if (1 == <?php echo $ticketsCambioActivos ?> && (modifyChangeTickets < 0 || modifyChangeTickets > 5)) {
				newNotification('You can print a <b>maximum of 5 gift tickets</b>', 'error');
			} else {
				wantPrint = printInvoice;
				copiesChangeTickets = modifyChangeTickets;

				document.getElementById('createSaleProducts').value = productList;
				document.getElementById('createSaleProductsChangeTicket').value = productListChangeTicket;
				document.getElementById('createSaleProductsArray').value = JSON.stringify(productsArray);
				document.getElementById('createSaleProductsArrayAuto').value = JSON.stringify(productsArrayAutoPrinting);
				document.getElementById('createSalePayment').value = metodoPago;
				document.getElementById('createSaleSubtotal').value = subtotal;
				document.getElementById('createSaleDiscounts').value = descuentos;
				document.getElementById('createSaleReceived').value = totalRecibido;
				document.getElementById('createSaleChange').value = totalCambio;
				document.getElementById('createSaleNotes').value = notes;
				document.getElementById('createSaleChangeTicketsNum').value = copiesChangeTickets;
				document.getElementById('createSaleAutoNoPrint').value = printInvoice;
				if (metodoPago == "M") {
					document.getElementById('createSaleMultiEf').value = receivedEf;
					document.getElementById('createSaleMultiTa').value = receivedTa;
					document.getElementById('createSaleMultiOt').value = receivedOt;
				}

				document.getElementById('createSaleSend').click();
				openLoader();
			}
		}

		function showOtherProd() {
			document.getElementById('overlayOtherProduct').style.display = "block";
			document.getElementById('otherProdNameInput').value = "";
			document.getElementById('otherProdPriceInput').value = "";
		}

		function addOtherProduct() {
			var nameProd = document.getElementById('otherProdNameInput').value;
			var priceProd = document.getElementById('otherProdPriceInput').value;
			priceProd++;
			priceProd--;

			if (nameProd == "" || priceProd < 0) {
				newNotification('Check the fields', 'error')
			} else {
				productsPageArray.push({
					id: 0,
					name: nameProd,
					price: priceProd,
					totalPrice: priceProd,
					stock: 1,
					quantity: 1,
					numberProdSale: productOrderId
				});
				productOrderId++;

				subtotal += priceProd;

				updateValues();
				document.getElementById('overlayOtherProduct').style.display = "none";
			}
		}

		function confirmationExit() {
			if (preguntarParaCerrar == true) {
				return "Are you sure you want to exit?";
			}
		}

		function searchBarcodeReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/search-barcode-product.php',
				data: $('#searchBarcodeForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotificationError();
					} else if (response['codigo_existe'] == true) {
						var codigoFinal = response['codigo_id'];
						codigoFinal++;
						codigoFinal--;
						var precioFinal = response['codigo_precio'];
						precioFinal++;
						precioFinal--;
						addProduct(codigoFinal, response['codigo_nombre'], precioFinal, response['codigo_stock']);
					} else {
						document.getElementById('searchProductInput').value = document.getElementById('codigosInput').value;
						searchProduct();
					}

					closeLoader();
					document.getElementById('codigosInput').value = "";
				}
			});

			return false;
		}

		function searchVoucherReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/search-voucher-info.php',
				data: $('#searchVoucherForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['no_cumple_condiciones']) {
						newNotification('Incorrect code or does not meet the conditions', 'error');
						document.getElementById('overlayDescuento').style.display = "block";
					} else {
						descuentos = response['valor_descuentos'];
						document.getElementById('createSaleVoucherID').value = response['id_bono'];
						document.getElementById('additionalButtons').style.display = "none";
						document.getElementById('discountSelection').style.display = "none";
						document.getElementById('discountDeletion').style.display = "block";

						voucherApplied = true;
						newNotification('Discount applied', 'success');
						updateValues();
						if (metodoPago == "M") {
							updateMultipago();
						}

						document.getElementById("cambioInput").value = total;
					}

					closeLoader();
				}
			});

			return false;
		}

		function createSaleReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/new-invoice.php',
				data: $('#createSaleForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
						closeLoader();
					} else if (response['venta_creada'] == true) {
						finalInvoice = response['plantilla_impresion'];
						printIDInvoice = response['id_venta'];

						if (wantPrint == 1 && response['auto_print'] != true) {
							printInvoice();
						} else {
							newNotification('Sale registered', 'success');
						}
						closeLoader();

						document.getElementById('optionsPrinting1').style.display = 'none';
						document.getElementById('optionsPrinting2').style.display = 'block';
						document.getElementById('sectionPayment').style.display = 'none';
						document.getElementById('sectionCambio').style.display = 'none';
						document.getElementById('actionButtons').style.display = 'none';
						document.getElementById('recommendedActions').style.display = 'block';
						document.getElementById('newSaleButton').style.display = '';
						document.getElementById('btnOnlyPrint').focus();
						preguntarParaCerrar = false;
						<?php if ($cloudServiceActive == 1) { ?>updateCloudInfo(1, 0);
					<?php } ?>
					}
				}
			});

			return false;
		}

		function printInvoice() {
			openLoader();

			document.getElementById('generateTemplateIDInvoice').value = printIDInvoice;
			document.getElementById('generateTemplatePrintOrSend').value = "P";
			document.getElementById('generateTemplateAutoChangeTickets').value = "";
			document.getElementById('generateTemplateSend').click();
		}

		function generateTemplateReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/include/generate-sale-template.php',
				data: $('#generateTemplateForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
						closeLoader();
					} else if (response['plantilla_impresion'] != "") {
						if (response['auto_print'] != true) {
							newNotification('Printing receipt, check the opened tab', 'success');
							var printWindow = window.open("print-invoice.php?idInvoice=" + printIDInvoice, "PRINT", "width=400,height=600");
						}

						closeLoader();
					}
				}
			});

			return false;
		}

		document.onkeydown = function() {
			switch (event.keyCode) {
				case 112:
					if (event.keyCode == 112) {
						window.location = "/trv/new-invoice.php";
					}

				case 116:
					if (event.keyCode == 116 && preguntarParaCerrar == true) {
						selectMethod('E');
					}

				case 117:
					if (event.keyCode == 117 && preguntarParaCerrar == true) {
						selectMethod('T');
					}

				case 118:
					if (event.keyCode == 118 && preguntarParaCerrar == true) {
						selectMethod('M');
					}

				case 120:
					if (event.keyCode == 120 && preguntarParaCerrar == true) {
						createInvoice(1);
					}

				case 121:
					if (event.keyCode == 121 && preguntarParaCerrar == true) {
						createInvoice(0);
					}

					if (event.keyCode) {
						event.returnValue = false;
						event.keyCode = 0;
						return false;
					}
			}
		}
	</script>
</body>

</html>