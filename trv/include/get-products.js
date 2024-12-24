function getProdsCategory(idCategory, titleCategory, searchQuery) {
	document.getElementById('prodSelection1Category').value = idCategory;
	document.getElementById('prodSelection1Search').value = searchQuery;
	document.getElementById('prodSelection1Send').click();

	document.getElementById('txtCategory').innerHTML = titleCategory;
	document.getElementById('overlaySelectProduct1').style.display = "none";
	document.getElementById('overlaySelectProduct2').style.display = "block";

	document.getElementById('divProductsList').innerHTML = '<div style= "width: 30%;margin: auto;"><img src= "/trv/media/loader.gif" alt= "Loading..." width= "100%" loading= "lazy"></div>';
}

function backCategorySelection() {
	document.getElementById('overlaySelectProduct1').style.display = 'block';
	document.getElementById('overlaySelectProduct2').style.display = 'none';
	document.getElementById('searchProductInput').value = '';
}

function variablePriceAdd(idProd, nombreProd, precioProd, stockProd, recommendedPrice) {
	document.getElementById('otroValorProdInput').value = recommendedPrice;
	addProduct(idProd, nombreProd, precioProd, stockProd, 0);
}

function variablePriceAddOther(idProd, nombreProd, precioProd, stockProd) {
	var otherPrice = document.getElementById('variablePriceOtherInput').value;
	otherPrice++; otherPrice--;

	document.getElementById('otroValorProdInput').value = otherPrice;
	addProduct(idProd, nombreProd, precioProd, stockProd, 0);
}

function prodSelection1Return() {
	$.ajax({
		type: 'POST',
		url: '/trv/include/product-selection-1.php',
		data: $('#prodSelection1Form').serialize(),
		dataType: 'json',
		success: function (response) {
			if (response["lista_prods"] != "") {
				document.getElementById('divProductsList').innerHTML = response["lista_prods"];
			} else {
				newNotificationError();
				document.getElementById('overlaySelectProduct2').style.display = "none";
			}
		}
	});

	return false;
}

function prodSelection2Return() {
	$.ajax({
		type: 'POST',
		url: '/trv/include/product-selection-2-variable.php',
		data: $('#prodSelection2Form').serialize(),
		dataType: 'json',
		success: function (response) {
			if (response["lista_pecios"] != "") {
				document.getElementById('divProductPrices').innerHTML = response["lista_pecios"];
			} else {
				newNotificationError();
				document.getElementById('overlaySelectProduct3').style.display = "none";
			}
		}
	});

	return false;
}