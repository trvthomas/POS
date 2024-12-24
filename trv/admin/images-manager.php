<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php"; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Images Manager</title>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/head-tracking.php"; ?>
	<script type="text/javascript" src="/trv/include/libraries/copy-clipboard.min.js"></script>

	<style>
		body,
		html {
			background-color: var(--modal-background-color);
		}
	</style>
</head>

<body>
	<div class="box">
		<h3 class="is-size-5 has-text-centered">Images Manager</h3>
		<hr><br>

		<div class="tabs is-centered is-boxed">
			<ul>
				<li id="tab1" onclick="selectTab(1)" class="is-active"><a><span class="icon is-small"><i class="fas fa-images"></i></span><span>Images</span></a></li>
				<li id="tab2" onclick="selectTab(2)"><a><span class="icon is-small"><i class="fas fa-upload"></i></span><span>Upload image</span></a></li>
			</ul>
		</div>

		<div class="has-text-centered" id="divTab1">
			<label class="label">Search image</label>
			<div class="field has-addons">
				<div class="control has-icons-left is-expanded">
					<input type="text" class="input" placeholder="Search by name" id="inputSearchImage" onkeydown="onupImages()">
					<span class="icon is-small is-left"><i class="fas fa-heading"></i></span>
				</div>

				<div class="control">
					<button class="button backgroundDark" onclick="getImages()"><i class="fas fa-magnifying-glass"></i></button>
				</div>
			</div>

			<div id="filesDiv1">
				<p class='has-text-centered is-size-5 has-text-success'><b>Perform a search</b></p>
			</div>
		</div>

		<div class="has-text-centered" id="divTab2" style="display: none">
			<div id="divUpload">
				<label class="label">Click or drag the image below to upload</label>
				<form action="/trv/media/uploads/upload.php" method="POST" enctype="multipart/form-data" id="formUploadImage">
					<input type="file" name="image[]" accept="image/*" multiple>
				</form>
			</div>

			<div style="margin: auto;display: none;" id="imgLoading">
				<div style="width: 30%;margin: auto;"><img src="/trv/media/loader.gif" alt="Loading..." width="100%" loading="lazy"></div>
			</div>
		</div>
	</div>

	<form action="/trv/media/uploads/get-images.php" method="POST" style="display: none" id="getImgsForm" onsubmit="return getImgs();">
		<input name="getImgsCode" value="xo92Th794P" readonly>
		<input id="getImgsCodeSearch" name="getImgsCodeSearch" readonly>
		<input id="getImgsSend" type="submit" value="Send">
	</form>

	<form action="/trv/media/uploads/delete.php" method="POST" style="display: none" id="deleteImgForm" onsubmit="return deleteImgReturn();">
		<input id="deleteImgUrl" name="deleteImgUrl" readonly>
		<input id="deleteImgSend" type="submit" value="Send">
	</form>

	<script type="text/javascript" src="/trv/include/libraries/jquery.js"></script>
	<script defer type="text/javascript" src="/trv/include/notifications-loader.js"></script>
	<script>
		new ClipboardJS('.copyUrl');

		function selectTab(idTab) {
			for (var x = 1; x <= 2; x++) {
				document.getElementById('divTab' + x).style.display = 'none';
				document.getElementById('tab' + x).classList.remove('is-active');
			}
			document.getElementById('divTab' + idTab).style.display = 'block';
			document.getElementById('tab' + idTab).classList.add('is-active');
		}

		function getImages() {
			var searchValue = document.getElementById('inputSearchImage').value;

			if (searchValue == "" || searchValue == "." || searchValue == "webp") {
				var c = confirm("Do you really want to see all the images? A large amount of information could affect the browser's performance.");
				if (c == true) {
					getImages2(searchValue);
				}
			} else {
				getImages2(searchValue);
			}
		}

		function getImages2(searchTerm) {
			document.getElementById('filesDiv1').innerHTML = '<div style= "margin: auto;"><div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div></div>';
			document.getElementById('getImgsCodeSearch').value = searchTerm;
			document.getElementById('getImgsSend').click();
			selectTab(1);
		}

		function onupImages() {
			if (event.keyCode === 13) {
				getImages();
			}
		}

		function deleteImg(urlImg, idBtn) {
			var c = confirm("Are you sure? This action cannot be undone");
			if (c == true) {
				document.getElementById('deleteImgUrl').value = urlImg;
				document.getElementById('deleteImgSend').click();

				document.getElementById('btnDeleteImg' + idBtn).disabled = true;
				document.getElementById('btnDeleteImg' + idBtn).innerHTML = '<i class= "fas fa-spinner fa-spin"></i>';
			}
		}

		function codeCopied() {
			newNotification("Link copied", "success");
		}

		function getImgs() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/get-images.php',
				data: $('#getImgsForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['files'] != "") {
						document.getElementById('filesDiv1').innerHTML = response['files'];
					}
				}
			});

			return false;
		}

		function deleteImgReturn() {
			$.ajax({
				type: 'POST',
				url: '/trv/media/uploads/delete.php',
				data: $('#deleteImgForm').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response['errores'] == true) {
						newNotification('There was an error', 'error');
					} else if (response['foto_eliminada'] == true) {
						newNotification('Information updated', 'success');
						getImages();
					}
				}
			});

			return false;
		}

		$(document).ready(function(e) {
			$("#formUploadImage").on('change', (function(e) {
				document.getElementById('divUpload').style.display = "none";
				document.getElementById('imgLoading').style.display = "block";
				$.ajax({
					url: "/trv/media/uploads/upload.php",
					type: "POST",
					data: new FormData(this),
					dataType: 'json',
					contentType: false,
					processData: false,
					success: function(data) {
						if (data['error_imagen'] == true) {
							newNotification('There was an error', 'error');
						} else if (data['imagen_subida'] == true) {
							newNotification(data['cantidad_imagenes_subidas'] + ' images uploaded', 'success');

							document.getElementById('formUploadImage').reset();
						}
						document.getElementById('divUpload').style.display = "block";
						document.getElementById('imgLoading').style.display = "none";
					}
				});
			}));
		});
	</script>
</body>

</html>