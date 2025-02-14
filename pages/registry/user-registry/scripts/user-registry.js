"use strict";

$(document).ready(function () {});

// globals
var officePersonnelDataTable = $("#tbl-office-personnel").DataTable({
	pageLength: 25,
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
	]
});

var nonOfficePersonnelDataTable = $("#tbl-non-office-personnel").DataTable({
	pageLength: 25,
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
	]
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
				if (action === "insert") {
					appendNewUser(response.data);
				} else {
					// appendUpdatedUser(response.data);
				}

				toggleModal("user-registry-modal", 1);
			} else {
				swalAlert("Error", "error", "danger", response.message);
				console.error(response.message);
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

function appendNewUser(userData) {
	const { id, userCategory, name, email, contactNo, address, tstamp } = userData;
	const dataTable = userCategory === "office" ? officePersonnelDataTable : nonOfficePersonnelDataTable;
	const table = userCategory === "office" ? $("#tbl-office-personnel") : $("#tbl-non-office-personnel");
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="Edit User">
					<i class="fas fa-edit me-1"></i>Edit
				</button>
				<button class="btn btn-danger btn-sm" title="Delete User">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = dataTable.row
		.add([name, email, contactNo, address, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-d-${id}`);

	dataTablesNewRow(table, dataTable, newRow);
}
