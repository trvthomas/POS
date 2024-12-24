<?php header('Location:include/updates/db-optimization.php'); ?>
<!DOCTYPE html>
<html>

<head>
	<title>Optimizing the database, please wait...</title>

	<?php include_once "include/head-tracking.php"; ?>
</head>

<body>
	<div id="overlayLoader" class="overlayLoader" style="display: block">
		<div class="loaderBox">
			<div class="imgProcessing">
				<img src="/trv/media/loader.gif" alt="Loading..." width="100%" loading="lazy">
			</div>

			<h1 class="is-size-3" style="margin: 2px auto;">Optimizing the database, please wait</h1>
			<h3 class="is-size-4 has-text-danger" style="margin-top: 2px;">Don't close this page</h3>
		</div>
	</div>
</body>

</html>