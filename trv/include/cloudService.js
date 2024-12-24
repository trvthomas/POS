//Service no longer supported
var cForm = document.createElement("FORM");
var cFormAttr1 = document.createAttribute("method");
cFormAttr1.value = "POST";
var cFormAttr2 = document.createAttribute("action");
cFormAttr2.value = "/trv/include/update-cloud-service.php";
var cFormAttr3 = document.createAttribute("style");
cFormAttr3.value = "display: none";
var cFormAttr4 = document.createAttribute("id");
cFormAttr4.value = "cloudServiceForm";
var cFormAttr5 = document.createAttribute("onsubmit");
cFormAttr5.value = "return cloudServiceReturn();";
var cFormAppend = document.body.appendChild(cForm);
cFormAppend.setAttributeNode(cFormAttr1); cFormAppend.setAttributeNode(cFormAttr2); cFormAppend.setAttributeNode(cFormAttr3); cFormAppend.setAttributeNode(cFormAttr4); cFormAppend.setAttributeNode(cFormAttr5);

document.getElementById('cloudServiceForm').innerHTML = '<input name= "cloudServiceToken" value= "vzjnp88k" readonly> <input id= "cloudServiceStatus" name= "cloudServiceStatus" readonly> <input id= "cloudServiceTCAccepted" name= "cloudServiceTCAccepted" readonly> <input type= "submit" id= "cloudServiceSend">';

function updateCloudInfo(statusPos, tCAccept) {
	var sendStatus = "open";
	if (statusPos == 0) { sendStatus = "close"; }

	document.getElementById('cloudServiceStatus').value = sendStatus;
	document.getElementById('cloudServiceTCAccepted').value = tCAccept;
	document.getElementById('cloudServiceSend').click();
}

function cloudServiceReturn() {
	$.ajax({
		type: 'POST',
		url: '/trv/include/update-cloud-service.php',
		data: $('#cloudServiceForm').serialize(),
		dataType: 'json',
		success: function (response) {
			console.log("Response update Cloud Service: " + response["result"]);
		}
	});

	return false;
}