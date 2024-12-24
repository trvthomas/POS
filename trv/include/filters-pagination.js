var totalElements = 0, totalElements2 = 0, actualPage = 1;
var searchMade = "", selectedFilter1 = "", selectedFilter2 = "";

function createFiltersBox(enableSearch, textSearch, arrayReceived, array2Received) {
	totalElements = 0, totalElements2 = 0;
	var createdFilter = '<p class= "panel-heading backgroundDark">Filters</p>';

	if (enableSearch == true && textSearch != "") {
		createdFilter += '<div class="panel-block"><div class= "field"><label class="label">' + textSearch + '</label><div class= "control"><div class= "field has-addons"><div class= "control is-expanded"><input type= "text" class= "input" placeholder= "' + textSearch + '" id= "inputFiltersSearch" onkeyup= "onupFilters()"></div><div class= "control"><button class= "button backgroundDark" onclick= "searchFilter()"><i class="fas fa-magnifying-glass"></i></button></div></div></div></div></div>';
	}

	if (arrayReceived != false) {
		var decoded = JSON.parse(arrayReceived);
		for (var x = 0; x < decoded.length; x++) {
			var onclickAction = "'" + decoded[x].actionCode + "'";
			createdFilter += '<a class="panel-block" id= "filterElement' + totalElements + '" onclick= "setFilter(' + totalElements + ', ' + onclickAction + ')"><span class="panel-icon"><i class="fas fa-' + decoded[x].icon + '"></i></span> ' + decoded[x].title + '</a>';
			totalElements++;
		}
	}

	if (array2Received != false) {
		createdFilter += '<div style="border-top: 1px solid var(--dark);">';
		var decoded2 = JSON.parse(array2Received);
		for (var x = 0; x < decoded2.length; x++) {
			var onclickAction = "'" + decoded2[x].actionCode + "'";
			createdFilter += '<a class="panel-block" id= "filter2Element' + totalElements2 + '" onclick= "setFilter2(' + totalElements2 + ', ' + onclickAction + ')"><span class="panel-icon"><i class="fas fa-' + decoded2[x].icon + '"></i></span> ' + decoded2[x].title + '</a>';
			totalElements2++;
		}
		createdFilter += '</div>';
	}

	createdFilter += '<div class="panel-block"><button class="button backgroundDark is-fullwidth" onclick= "resetFilters()">Reset filters</button></div>';

	document.getElementById('filtersPanel').innerHTML = createdFilter;
	createPagination();
}

function toggleFilters() { document.getElementById('filtersPanel').classList.toggle('is-hidden'); }

function setFilter(selectedFilter, actionToDo) {
	for (var x = 0; x < totalElements; x++) { document.getElementById('filterElement' + x).classList.remove('is-active'); }
	document.getElementById('filterElement' + selectedFilter).classList.add('is-active');

	selectedFilter1 = selectedFilter;
	changeStateUrl();
	onpageSetFilter(actionToDo);
	toggleFilters();
}

function setFilter2(selectedFilter, actionToDo) {
	for (var x = 0; x < totalElements2; x++) { document.getElementById('filter2Element' + x).classList.remove('is-active'); }
	document.getElementById('filter2Element' + selectedFilter).classList.add('is-active');

	selectedFilter2 = selectedFilter;
	changeStateUrl();
	onpageSetFilter2(actionToDo);
	toggleFilters();
}

function searchFilter() {
	var searchInput = document.getElementById('inputFiltersSearch').value;
	if (searchInput != "") {
		searchMade = searchInput;
		changeStateUrl();
		onpageSearchFilter(searchInput);
		toggleFilters();
	}
}

function resetFilters() {
	for (var x = 0; x < totalElements; x++) { document.getElementById('filterElement' + x).classList.remove('is-active'); }
	for (var x = 0; x < totalElements2; x++) { document.getElementById('filter2Element' + x).classList.remove('is-active'); }
	document.getElementById('inputFiltersSearch').value = "";

	searchMade = "", selectedFilter1 = "", selectedFilter2 = "";
	changeStateUrl();
	onpageResetFilters();
	toggleFilters();
}

function changeStateUrl() {
	if (actualPage <= 0) { actualPage = 1; }
	window.history.pushState("filters", document.title, "?search=" + searchMade + "&filter1=" + selectedFilter1 + "&filter2=" + selectedFilter2 + "&page=" + actualPage);
}

function onloadSetFilters(setSearch, setFilter1, setFilter2, setPageNum) {
	document.getElementById('inputFiltersSearch').value = setSearch;
	searchFilter();

	if (setFilter1 != "") { document.getElementById('filterElement' + setFilter1).click(); }
	if (setFilter2 != "") { document.getElementById('filter2Element' + setFilter2).click(); }

	var calcPage = setPageNum - 1;
	nextPage(calcPage);
}

function onupFilters() {
	if (event.keyCode === 13) {
		searchFilter();
	}
}

function createPagination() {
	document.getElementById('paginationPanel').innerHTML = '<a class="pagination-previous is-invisible" id= "paginationPrevious" onclick= "nextPage(-1)"><span class="icon"><i class="fas fa-chevron-left"></i></span></a><p class="pagination-list">Page <span id= "pageNumber" style= "margin-left: 5px;">1</span></p><a class="pagination-next" id= "paginationNext" onclick= "nextPage(1)"><span class="icon"><i class="fas fa-chevron-right"></i></span></a>';
}

function nextPage(index) {
	actualPage += index;
	document.getElementById('paginationPrevious').classList.remove('is-invisible');
	if (actualPage <= 1) { document.getElementById('paginationPrevious').classList.add('is-invisible'); }
	document.getElementById('pageNumber').innerHTML = actualPage;

	changeStateUrl();
	onpageNextPage(actualPage);
}

function hidePagination(hide) {
	if (hide == true) {
		document.getElementById('paginationNext').classList.add('is-invisible');
	} else {
		document.getElementById('paginationNext').classList.remove('is-invisible');
	}
}