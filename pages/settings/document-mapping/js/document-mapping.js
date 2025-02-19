"use strict";

$(document).ready(function () {});

// globals
var docTypesDataTable = $("#tbl-doc-types").DataTable({});
// end of globals

function loadDocMapping(id) {
	const parentElement = $("#_doc-map");

	$("#tbl-doc-types tbody tr").removeClass("table-active active-doc-type");
	$(`#tbl-doc-types tbody tr[data-id="${id}"]`).addClass("table-active active-doc-type");

	loader(parentElement);
	$.post(`pages/settings/document-mapping/components/map.php`, { id: id }, function (data) {
		parentElement.html(data);
		toggleModal("user-registry-modal");
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

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");

	await $.post(`pages/settings/document-mapping/components/modal.php`, { action: action, id: id }, function (data) {
		parentElement.html(data);
		toggleModal("document-mapping-modal");
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

function insertUpdateDocTransactionSetting() {
	let settings = [];
	const docType = $(".active-doc-type").data("id");
	const rows = $("#tbl-doc-transaction-setting-entry tbody tr");

	rows.each(function () {
		const tr = $(this);
		const order = tr.find("td").eq(0).html();
		let office = tr.find("td").eq(1).find("input").val();
		office = $(`#office-list option[value='${office}']`).data("id");
		settings.push({ order: order, office: office });
	});

	const duplicateOffices = settings.filter((setting, index, self) => index !== self.findIndex((s) => s.office === setting.office));

	if (duplicateOffices.length > 0) {
		showAlert("Duplicate office entries found. Please ensure each office is unique.", "danger");
		duplicateOffices.forEach((setting) => {
			const input = $(`#office-list-input-${setting.order}`);
			input.css("border", "2px solid red");
			setTimeout(() => {
				input.css("border", "");
			}, 3000);
		});
		return;
	}

	$.post(
		`pages/settings/document-mapping/scripts/insert.php`,
		{
			docType: docType,
			settings: JSON.stringify(settings)
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				loadDocMapping(docType);
				toggleModal("document-mapping-modal", 1);
				showAlert("Settings has been updated.");
			} else {
				showAlert(response.message, "danger");
			}
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

function deleteSetting(id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this settings?", "Delete settings", "Delete", id, function (id) {
			$.post(
				`pages/settings/document-mapping/scripts/delete.php`,
				{
					id: id
				},
				function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						loadDocMapping(id);
						showAlert("Settings has been deleted.");
						resolve(true);
					} else {
						showAlert(response.message, "danger");
						resolve(false);
					}
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
				resolve(false);
			});
		});
	});
}
