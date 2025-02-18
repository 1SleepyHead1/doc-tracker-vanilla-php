"use strict";

$(document).ready(function () {});

// globals
var docTypeDataTable = $("#tbl-doc-types").DataTable({
	columnDefs: [
		{
			targets: [2], // Date column index
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
			targets: [3],
			orderable: false // Disable sorting
		}
	],
	order: []
});
// end of globals

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");

	await $.post(`pages/registry/document-type-registry/components/modal.php`, { action: action, id: id }, function (data) {
		parentElement.html(data);
		toggleModal("document-type-registry-modal");
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

function insertUpdateDocType(action, id = null) {
	const url = action === 0 ? "insert.php" : "update.php";
	const docTypeCode = $("#doc-type-code").val();
	const docTypeName = $("#doc-type-name").val();

	$.post(
		`pages/registry/document-type-registry/scripts/${url}`,
		{
			id: id,
			docTypeCode: docTypeCode,
			docTypeName: docTypeName
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				if (action === 0) {
					appendDocType(response.data);
				} else {
					updateDocType(response.data);
				}

				const message = action === 0 ? "New document type has been added." : "Document type has been updated.";
				toggleModal("document-type-registry-modal", 1);
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

function deleteDocType(id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this document type?", "Delete Document Type", "Delete", id, function (id) {
			$.post(
				`pages/registry/document-type-registry/scripts/delete.php`,
				{
					id: id
				},
				function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						const row = docTypeDataTable.row(`#tr-doc-type-${id}`);
						if (row.length) {
							row.remove().draw(false);
						}
						showAlert("Document type has been deleted.");
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
			});
		});
	});
}

// for data table manipulation
function appendDocType(docTypeData) {
	const { id, doc_type_code, doc_type_name, tstamp } = docTypeData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="Edit Document Type" onclick="showModal(1,${id})">
					<i class="fas fa-edit me-1"></i>Edit
				</button>
				<button class="btn btn-danger btn-sm" title="Delete Document Type" onclick="deleteDocType(${id})">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = docTypeDataTable.row
		.add([doc_type_name, doc_type_code, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-doc-type-${id}`);

	dataTablesNewRow($("#tbl-doc-types"), docTypeDataTable, newRow);
}

function updateDocType(docTypeData) {
	const { id, doc_type_name, doc_type_code } = docTypeData;
	const row = docTypeDataTable.row(`#tr-doc-type-${id}`);

	if (row.length) {
		docTypeDataTable.row(row).data([doc_type_name, doc_type_code, row.data()[2], row.data()[3]]).draw(false);
	}
}
