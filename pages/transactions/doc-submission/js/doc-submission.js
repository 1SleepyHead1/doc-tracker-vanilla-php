"use strict";

$(document).ready(function () {});

// globals
var documentsDataTable = $("#tbl-documents").DataTable({
	columnDefs: [
		{
			targets: [5], // Date column index
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
			targets: [6],
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

function insertUpdateDocument(action, id = null) {
	const url = action === 0 ? "insert.php" : "update.php";
	const docType = $("#doc-type option:selected").text();
	const docTypeId = $("#doc-type").val();
	const purpose = $("#purpose").val();
	const submitter = $("#submitter").val();
	const submitterId = $("#submitter-list").find(`option[value="${submitter}"]`).data("id");

	$.post(
		`pages/transactions/doc-submission/scripts/${url}`,
		{
			id: id,
			docType: docType,
			docTypeId: docTypeId,
			purpose: purpose,
			submitter: submitter,
			submitterId: submitterId
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				if (action === 0) {
					appendDocument(response.data);
				} else {
					updateDocument(response.data);
				}

				const message = action === 0 ? "New document has been added." : "Document has been updated.";
				toggleModal("doc-submission-modal", 1);
				showAlert(message);
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

function deleteDocument(id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this document?", "Delete Document", "Delete", id, function (id) {
			$.post(
				`pages/transactions/doc-submission/scripts/delete.php`,
				{
					id: id
				},
				function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						const row = documentsDataTable.row(`#tr-doc-${id}`);
						if (row.length) {
							row.remove().draw(false);
						}
						showAlert("Document has been deleted.");
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

// for data table manipulation
function appendDocument(docData) {
	const { id, doc_no, type, submitter, purpose, status, tstamp } = docData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-icon btn-clean me-0" type="button" id="dropdown-menu-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-ellipsis-v"></i>
				</button>
				<ul class="dropdown-menu" style="font-size: 1.1em;">
					<li><a class="dropdown-item" onclick="showDocStats('${doc_no}')">View Document Logs</a></li>
					<li><a class="dropdown-item" onclick="showQRCode(${id})">Show QR Code</a></li>
					<li><a class="dropdown-item" onclick="showModal(1, ${id})">Edit</a></li>
					<li><a class="dropdown-item text-danger" onclick="deleteDocument${id})">Delete</a></li>
				</ul>
			</div>
		</td>
	`;

	const newRow = documentsDataTable.row
		.add([doc_no, type, submitter, purpose, status, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-doc-${id}`);

	dataTablesNewRow($("#tbl-documents"), documentsDataTable, newRow);
}

function updateDocument(docData) {
	const { id, type, submitter, purpose } = docData;
	const row = documentsDataTable.row(`#tr-doc-${id}`);

	if (row.length) {
		documentsDataTable.row(row).data([row.data()[0], type, submitter, purpose, row.data()[4], row.data()[5], row.data()[6]]).draw(false);
	}
}
// end for data table manipulation

// for qr codes
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
// end for qr codes
