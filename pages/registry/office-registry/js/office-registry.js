"use strict";

$(document).ready(function () {});

//globals
var officeDataTable = $("#tbl-offices").DataTable({
	columnDefs: [
		{
			targets: [3], // Date column index
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
			targets: [4],
			orderable: false // Disable sorting
		}
	],
	order: []
});
//end of globals

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");

	await $.post(`pages/registry/office-registry/components/modal.php`, { action: action, id: id }, function (data) {
		parentElement.html(data);
		toggleModal("office-registry-modal");
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

function refreshPersonnelList() {
	$.post(`pages/registry/office-registry/components/personnel-list.php`, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			const users = response.data;
			if (users) {
				$("#office-personnel-list").empty();
				users.forEach((user) => {
					$("#office-personnel-list").append(`<option data-id="${user.id}" value="${user.name}"></option>`);
				});
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

function insertUpdateOffice(action, id = null) {
	const url = action === 0 ? "insert.php" : "update.php";
	const officeCode = $("#office-code").val();
	const officeName = $("#office-name").val();
	const personInCharge = $("#person-in-charge").val();
	const personInChargeId = $("#office-personnel-list").find(`option[value="${personInCharge}"]`).data("id");

	$.post(
		`pages/registry/office-registry/scripts/${url}`,
		{
			id: id,
			officeCode: officeCode,
			officeName: officeName,
			personInCharge: personInCharge,
			personInChargeId: personInChargeId
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				if (action === 0) {
					appendOffice(response.data);
				} else {
					updateOffice(response.data);
				}

				const message = action === 0 ? "New office has been added." : "Office has been updated.";
				refreshPersonnelList();
				toggleModal("office-registry-modal", 1);
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

function deleteOffice(id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this office?", "Delete Office", "Delete", id, function (id) {
			$.post(
				`pages/registry/office-registry/scripts/delete.php`,
				{
					id: id
				},
				function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						const row = officeDataTable.row(`#tr-office-${id}`);
						if (row.length) {
							row.remove().draw(false);
						}
						refreshPersonnelList();
						showAlert("Office has been deleted.");
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
function appendOffice(officeData) {
	const { id, office_code, office_name, person_in_charge, tstamp } = officeData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="Edit Office" onclick="showModal(1,${id})">
					<i class="fas fa-edit me-1"></i>Edit
				</button>
				<button class="btn btn-danger btn-sm" title="Delete Office" onclick="deleteOffice(${id})">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = officeDataTable.row
		.add([office_name, office_code, person_in_charge, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-office-${id}`);

	dataTablesNewRow($("#tbl-offices"), officeDataTable, newRow);
}

function updateOffice(officeData) {
	const { id, office_name, office_code, person_in_charge } = officeData;
	const row = officeDataTable.row(`#tr-office-${id}`);

	if (row.length) {
		officeDataTable.row(row).data([office_name, office_code, person_in_charge, row.data()[3], row.data()[4]]).draw(false);
	}
}
// end for data table manipulation
