"use strict";

$(document).ready(function () {});

// globals
var documentsDataTable = $("#tbl-documents").DataTable({
	columnDefs: [
		{
			targets: [4], // Date column index
			type: "date",
			render: function (data, type, row) {
				if (type === "sort") {
					// Convert date string to sortable format (YYYY-MM-DD)
					let dateParts = data.split(".");
					let monthDay = dateParts[0].split(","); // Split month and day
					let month = {
						Jan: "01",
						Feb: "02",
						Mar: "03",
						Apr: "04",
						May: "05",
						Jun: "06",
						Jul: "07",
						Aug: "08",
						Sep: "09",
						Oct: "10",
						Nov: "11",
						Dec: "12"
					};
					return monthDay[1] + "-" + month[monthDay[0]] + "-" + dateParts[1];
				}
				return data;
			}
		}
	],
	columnDefs: [
		{
			targets: [5],
			orderable: false // Disable sorting
		}
	],
	order: []
});
// end of globals

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");

	await $.post(`pages/transactions/doc-submission/components/entry-modal.php`, { action: action, id: id }, function (data) {
		parentElement.html(data);
		toggleModal("doc-submission-modal");
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
