var confirmationExit = true;
function alertExit() { if (confirmationExit == true) { return "Are you sure you want to exit? Your changes may not be saved"; } }

var createdProgressBar = "", totalSteps = 0, actualPage = 1;

function createProgressBar(clickableSteps, arrayReceived) {
	if (arrayReceived != false) {
		var decoded = JSON.parse(arrayReceived);
		for (var x = 0; x < decoded.length; x++) {
			totalSteps++;

			var firstClass = "";
			if (totalSteps == 1) { firstClass = "is-active"; }

			var clickableElements = 'class="has-text-dark aNotHover"';
			if (clickableSteps == true) { clickableElements = 'class="has-text-dark" onclick= "jumpStep(' + totalSteps + ')"'; }

			var onclickAction = "'" + decoded[x].actionCode + "'";
			createdProgressBar += '<li class="steps-segment ' + firstClass + '" id= "progressBarStep' + totalSteps + '"><a ' + clickableElements + '><span class="steps-marker progressBar"><span class="icon"><i class="fa fa-' + decoded[x].icon + '"></i></span></span><div class="steps-content"><p class="heading">' + decoded[x].title + '</p></div></a></li>';
		}
	}

	document.getElementById('progressBarDiv').innerHTML = createdProgressBar;
}

function nextStep(index) {
	actualPage += index;
	jumpStep(actualPage);
}

function jumpStep(stepNum) {
	actualPage = stepNum;

	for (var x = 1; x <= totalSteps; x++) {
		document.getElementById('step' + x).style.display = 'none';
		document.getElementById('progressBarStep' + x).classList.remove("is-active");
	}

	document.getElementById('step' + actualPage).style.display = 'block';
	document.getElementById('progressBarStep' + actualPage).classList.add("is-active");

	if (actualPage == 1) {
		document.getElementById('buttonPrevious').classList.add("is-invisible");
		document.getElementById('buttonNext').classList.remove("is-hidden");
		document.getElementById('buttonPublish').classList.add("is-hidden");
	} else if (actualPage == totalSteps) {
		document.getElementById('buttonPrevious').classList.remove("is-invisible");
		document.getElementById('buttonNext').classList.add("is-hidden");
		document.getElementById('buttonPublish').classList.remove("is-hidden");
	} else {
		document.getElementById('buttonPrevious').classList.remove("is-invisible");
		document.getElementById('buttonNext').classList.remove("is-hidden");
		document.getElementById('buttonPublish').classList.add("is-hidden");
	}
}