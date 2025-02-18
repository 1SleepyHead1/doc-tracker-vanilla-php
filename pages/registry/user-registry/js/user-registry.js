"use strict";

$(document).ready(function () {});

// globals
var officePersonnelDataTable = $("#tbl-office-personnel").DataTable({
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

var nonOfficePersonnelDataTable = $("#tbl-non-office-personnel").DataTable({
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
// end globals

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");
	const userCategory = $(`.user-category.active`).data("category");
	const url = userCategory === "office" ? "office-personnel-modal.php" : "non-office-personnel-modal.php";

	await $.post(`pages/registry/user-registry/components/${url}`, { action: action, id: id }, function (data) {
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

function insertUpdateUser(action, id = null) {
	const url = action === 0 ? "insert.php" : "update.php";
	const firstName = $("#first-name").val();
	const middleName = $("#middle-name").val();
	const lastName = $("#last-name").val();
	const extensionName = $("#ext-name").val();
	const userType = $("#user-type").val();
	const email = $("#email").val();
	const province = $("#province").val();
	const city = $("#city").val();
	const barangay = $("#barangay").val();
	const contactNumber = $("#contact-no").val();
	const userCategory = $(`.user-category.active`).data("category");

	$.post(
		`pages/registry/user-registry/scripts/${url}`,
		{
			id: id,
			firstName: firstName,
			middleName: middleName,
			lastName: lastName,
			extensionName: extensionName,
			userType: userType,
			email: email,
			province: province,
			city: city,
			barangay: barangay,
			contactNumber: contactNumber,
			userCategory: userCategory
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				if (action === 0) {
					if (userCategory === "office") {
						appendOfficePersonnelUser(response.data);
					} else {
						appendNonOfficePersonnelUser(response.data);
					}
				} else {
					if (userCategory === "office") {
						updateOfficePersonnelUser(response.data);
					} else {
						updateNonOfficePersonnelUser(response.data);
					}
				}

				const message = action === 0 ? "New user has been added." : "User has been updated.";
				toggleModal("user-registry-modal", 1);
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

function deleteUser(type, id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this user?", "Delete User", "Delete", id, function () {
			$.post(`pages/registry/user-registry/scripts/delete.php`, { id: id })
				.then(function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						const row = type === "office" ? officePersonnelDataTable.row(`#tr-user-op-${id}`) : nonOfficePersonnelDataTable.row(`#tr-user-nop-${id}`);
						if (row.length) {
							row.remove().draw(false);
						}
						showAlert("User has been deleted.");
						resolve(true);
					} else {
						showAlert(response.message, "danger");
						resolve(false);
					}
				})
				.fail(function (jqXHR, textStatus, errorThrown) {
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
function appendOfficePersonnelUser(userData) {
	const { id, name, email, contactNo, address, tstamp } = userData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="Edit User" onclick="showModal(1, ${id})">
					<i class="fas fa-edit me-1"></i>Edit
				</button>
				<button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteUser('office', ${id})">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = officePersonnelDataTable.row
		.add([name, email, contactNo, address, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-user-op-${id}`);

	dataTablesNewRow($("#tbl-office-personnel"), officePersonnelDataTable, newRow);
}

function updateOfficePersonnelUser(userData) {
	const { id, name, email, contactNo, address } = userData;
	const row = officePersonnelDataTable.row(`#tr-user-op-${id}`);

	if (row.length) {
		officePersonnelDataTable.row(row).data([name, email, contactNo, address, row.data()[4], row.data()[5]]).draw(false);
	}
}

function appendNonOfficePersonnelUser(userData) {
	const { id, name, userType, email, contactNo, address, tstamp } = userData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="Edit User" onclick="showModal(1, ${id})">
					<i class="fas fa-edit me-1"></i>Edit
				</button>
				<button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteUser('non-office', ${id})">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = nonOfficePersonnelDataTable.row
		.add([userType, name, email, contactNo, address, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-user-nop-${id}`);

	dataTablesNewRow($("#tbl-non-office-personnel"), nonOfficePersonnelDataTable, newRow);
}

function updateNonOfficePersonnelUser(userData) {
	const { id, name, userType, email, contactNo, address } = userData;
	const row = nonOfficePersonnelDataTable.row(`#tr-user-nop-${id}`);

	if (row.length) {
		nonOfficePersonnelDataTable.row(row).data([userType, name, email, contactNo, address, row.data()[5], row.data()[6]]).draw(false);
	}
}
// end for data table manipulation
