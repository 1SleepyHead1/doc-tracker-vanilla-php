"use strict";

$(document).ready(function () {
	loadDocCounts();
	// loadDocEntries();
	loadDocSubmissions();
	loadDocHandled();

	setInterval(() => {
		loadDocCounts();
	}, 18000);
});

var mainParentElement = $("#_docs");

function loadDocCounts() {
	const officeId = mainParentElement.attr("of");

	$.post(`pages/dashboard/office-dashboard/components/doc-counts.php `, { officeId: officeId }, function (data) {
		const response = JSON.parse(data);
		const count = response.data;
		const countElements = $(".doc-count");

		countElements.eq(0).text(count.in_need);
		countElements.eq(1).text(count.forwarded);
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

function loadDocEntries() {
	const parentElement = $("#_doc-entries");
	const userId = mainParentElement.attr("u");
	const officeId = mainParentElement.attr("o");

	loader(parentElement);
	$.post(`pages/dashboard/office-dashboard/components/doc-entries.php `, { userId: userId, officeId: officeId }, function (data) {
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

function loadDocSubmissions() {
	const parentElement = $("#_doc-submissions");
	const dateFrom = $("#date-from-b").val();
	const dateTo = $("#date-to-b").val();
	const userId = mainParentElement.attr("u");
	const docStatus = $("#doc-status-b").val();

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

function loadDocHandled() {
	const parentElement = $("#_doc-handled");
	// const dateFrom = $("#date-from-b").val();
	// const dateTo = $("#date-to-b").val();
	const officeId = mainParentElement.attr("of");
	const docStatus = $("#doc-status-b").val();

	loader(parentElement);
	$.post(`pages/dashboard/office-dashboard/components/doc-handled.php`, { officeId: officeId, docStatus: docStatus }, function (data) {
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
