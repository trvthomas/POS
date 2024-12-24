<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/inventory/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Home - Inventory Dashboard</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
</head>

<body>
	<?php include_once "include/header.php"; ?>

	<div class="contentBox has-text-centered">
		<div class="columns is-multiline is-centered">
			<div class="column is-one-third">
				<a href="/trv/inventory/merchandise-entry.php">
					<div class="box pastel-bg-purple">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-truck-ramp-box fa-2x"></i> Receive Stock</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/merchandise-exit.php">
					<div class="box pastel-bg-orange">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-dolly fa-2x"></i> Remove Stock</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/merchandise-adjustment.php">
					<div class="box pastel-bg-lightblue">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-clipboard-list fa-2x"></i> Inventory Adjustment</h3>
					</div>
				</a>
			</div>

			<div class="column is-one-third">
				<a href="/trv/inventory/inventory-history.php">
					<div class="box pastel-bg-pistachio">
						<h3 class="is-size-5"><i class="is-pulled-left fas fa-clock-rotate-left fa-2x"></i> History</h3>
					</div>
				</a>
			</div>

			<?php if (isset($_COOKIE[$prefixCoookie . "TemporaryInventoryIdUser"])) { ?>
				<div class="column is-one-third">
					<a href="/trv/inventory/logout-temporary.php">
						<div class="box">
							<h3 class="is-size-5"><i class="is-pulled-left fas fa-right-from-bracket fa-2x"></i> Log out</h3>
						</div>
					</a>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/footer.php"; ?>
</body>

</html>