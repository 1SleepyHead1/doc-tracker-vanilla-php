"use strict";

$(document).ready(function () {
	loadDocSubmissions();
});

function loadDocSubmissions() {
	const parentElement = $("#_doc-submissions");
	const dateFrom = $("#date-from").val();
	const dateTo = $("#date-to").val();
	const userId = parentElement.attr("u");
	const docStatus = $("#doc-status").val();

	loader(parentElement);
	$.post(`pages/dashboard/submitter-dashboard/components/doc-submissions.php`, { dateFrom: dateFrom, dateTo: dateTo, userId: userId, docStatus: docStatus }, function (data) {
		parentElement.html(data);
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

async function showQRCode(id) {
	const parentElement = $("#_qr_modal_content");

	await $.post(`pages/transactions/doc-submission/components/qr-modal.php`, { id: id }, function (data) {
		parentElement.html(data);
		toggleModal("qr-code-modal");
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
