"use strict";

$(document).ready(function () {
	generateReport();
});

function typeChange() {
	const type = $(`#r-type`).val();
	const filters = $(`.r-filter`);

	filters.each(function () {
		const e = $(this);
		if (e.hasClass("form-select")) {
			e.val(e.find(`option[data-default="1"]`).val());
		} else if (e.hasClass("form-control")) {
			e.val(getCurrentDate());
		}
	});

	if (type === "daily") {
		$(`.for-monthly, .for-yearly`).prop("hidden", true);
		$(`.for-daily`).prop("hidden", false);
	} else if (type === "monthly") {
		$(`.for-daily, .for-yearly`).prop("hidden", true);
		$(`.for-monthly`).prop("hidden", false);
	} else if (type === "yearly") {
		$(`.for-daily, .for-monthly`).prop("hidden", true);
		$(`.for-yearly`).prop("hidden", false);
	}
}

function generateReport() {
	const parentElement = $("#_doc-entries-report");
	const type = $(`#r-type`).val();
	const date = $(`#date`).val();
	const month = $(`#month`).val();
	const monthN = $(`#month option:selected`).text();
	const year = $(`#year`).val();
	const office = $("#offices").val();
	const officeN = $("#offices option:selected").text();

	loader(parentElement);
	$.post(
		`pages/reports/doc-entries-report/components/entries.php`,
		{
			type: type,
			date: date,
			month: month,
			monthN: monthN,
			year: year,
			office: office,
			officeN: officeN
		},
		function (data) {
			parentElement.html(data);
			toggleModal("office-registry-modal");
		}
	).fail(function (jqXHR, textStatus, errorThrown) {
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
