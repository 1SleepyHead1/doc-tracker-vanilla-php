"use strict";

$(document).ready(function () {
	loadDocCounts();
	loadMonthlyDocEntries();
	loadDocEntriesPerType();
});

// globals
var monthlyDocEntries = document.getElementById("_monthly-doc-entries").getContext("2d"),
	docEntriesPerOffice = document.getElementById("_doc-entries-per-office").getContext("2d"),
	docEntriesPerType = document.getElementById("_doc-entries-per-type").getContext("2d");

var $monthlyDocEntries = new Chart(monthlyDocEntries, {
	type: "line",
	data: {
		labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		datasets: [
			{
				label: "Document Entries",
				borderColor: "#1d7af3",
				pointBorderColor: "#FFF",
				pointBackgroundColor: "#1d7af3",
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBorderWidth: 1,
				pointRadius: 4,
				backgroundColor: "transparent",
				fill: true,
				borderWidth: 2,
				data: []
			}
		]
	},
	options: {
		responsive: true,
		maintainAspectRatio: false,
		legend: {
			position: "bottom",
			labels: {
				padding: 10,
				fontColor: "#1d7af3"
			}
		},
		tooltips: {
			bodySpacing: 4,
			mode: "nearest",
			intersect: 0,
			position: "nearest",
			xPadding: 10,
			yPadding: 10,
			caretPadding: 10
		},
		layout: {
			padding: { left: 15, right: 15, top: 15, bottom: 15 }
		}
	}
});

var $docEntriesPerOffice = new Chart(docEntriesPerOffice, {
	type: "bar",
	data: {
		labels: [],
		datasets: [
			{
				label: "Document Entries",
				backgroundColor: "rgb(138, 189, 255)",
				borderColor: "rgb(138, 189, 255)",
				data: []
			}
		]
	},
	options: {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
			yAxes: [
				{
					ticks: {
						beginAtZero: true
					}
				}
			]
		}
	}
});

var $docEntriesPerType = new Chart(docEntriesPerType, {
	type: "bar",
	data: {
		labels: [],
		datasets: [
			{
				label: "Document Entries",
				backgroundColor: "rgb(55, 105, 169)",
				borderColor: "rgb(55, 105, 169)",
				data: []
			}
		]
	},
	options: {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
			yAxes: [
				{
					ticks: {
						beginAtZero: true
					}
				}
			]
		}
	}
});

// end of globals
function loadDocCounts() {
	$.post(`pages/dashboard/admin-dashboard/components/doc-counts.php `, {}, function (data) {
		const response = JSON.parse(data);
		const count = response.data;
		const countElements = $(".doc-count");

		countElements.eq(0).text(count.entries);
		countElements.eq(1).text(count.pending);
		countElements.eq(2).text(count.rejected);
		countElements.eq(3).text(count.for_release);
	}).fail(function (jqXHR, textStatus, errorThrown) {
		const errorMessages = {
			500: "Internal Server Error (500) occurred.",
			404: "Resource not found (404) error.",
			403: "Forbidden (403) error.",
			401: "Unauthorized (401) error.",
			400: "Bad Request (400) error."
		};
		console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
	});
}

// functions for graphs
function loadMonthlyDocEntries() {
	$.post(`pages/dashboard/admin-dashboard/graphs/monthly-doc-entries.php`, { year: $("#mde-year").val() }, function (data) {
		const response = JSON.parse(data);
		const count = response.data;

		if (response.status) {
			if (count) {
				const newCounts = [...count];
				$monthlyDocEntries.data.datasets[0].data = newCounts;
				$monthlyDocEntries.update(); // Re-render the chart
			}
		}
	}).fail(function (jqXHR, textStatus, errorThrown) {
		const errorMessages = {
			500: "Internal Server Error (500) occurred.",
			404: "Resource not found (404) error.",
			403: "Forbidden (403) error.",
			401: "Unauthorized (401) error.",
			400: "Bad Request (400) error."
		};
		console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
	});
}

function loadDocEntriesPerType() {
	const types = $("#dept-types").val();
	$.post(`pages/dashboard/admin-dashboard/graphs/doc-entries-per-type.php`, { from: $("#depo-date-from").val(), to: $("#depo-date-to").val(), types: JSON.stringify(types) }, function (data) {
		const response = JSON.parse(data);
		const labels = types.map((type) => $(`#dept-types option[value="${type}"]`).text());
		const counts = response.data;

		if (response.status) {
			if (counts) {
				$docEntriesPerType.data.labels = labels;
				$docEntriesPerType.data.datasets[0].data = counts;
				$docEntriesPerType.update(); // Re-render the chart
			}
		}
	}).fail(function (jqXHR, textStatus, errorThrown) {
		const errorMessages = {
			500: "Internal Server Error (500) occurred.",
			404: "Resource not found (404) error.",
			403: "Forbidden (403) error.",
			401: "Unauthorized (401) error.",
			400: "Bad Request (400) error."
		};
		console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
	});
}

function loadDocEntriesPerOffice() {
	const offices = $("#depo-offices").val();

	if (!offices.length) {
		return;
	}

	$.post(`pages/dashboard/admin-dashboard/graphs/doc-entries-per-office.php`, { from: $("#depo-date-from").val(), to: $("#depo-date-to").val(), offices: JSON.stringify(offices) }, function (data) {
		const response = JSON.parse(data);
		const labels = offices.map((office) => $(`#depo-offices option[value="${office}"]`).text());
		const counts = response.data;

		if (response.status) {
			if (counts) {
				$docEntriesPerOffice.data.labels = labels;
				$docEntriesPerOffice.data.datasets[0].data = counts;
				$docEntriesPerOffice.update();
			}
		}
	}).fail(function (jqXHR, textStatus, errorThrown) {
		const errorMessages = {
			500: "Internal Server Error (500) occurred.",
			404: "Resource not found (404) error.",
			403: "Forbidden (403) error.",
			401: "Unauthorized (401) error.",
			400: "Bad Request (400) error."
		};
		console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
	});
}
// end functions for graphs
