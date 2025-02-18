"use strict";

$(document).ready(function () {});

// globals
var userAccountDataTable = $("#tbl-user-accounts").DataTable({
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
// end globals

async function showModal(action, id = null) {
	const parentElement = $("#_modal-content");

	await $.post(`pages/registry/user-account-registry/components/modal.php`, { action: action, id: id }, function (data) {
		parentElement.html(data);
		toggleModal("user-account-registry-modal");
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

function refreshUserList() {
	$.post(`pages/registry/user-account-registry/components/no-account-user-list.php`, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			const users = response.data;
			if (users) {
				$("#user-list").empty();
				users.forEach((user) => {
					$("#user-list").append(`<option data-gen-uname="${user.first_name}" data-gen-pass="${user.last_name}" data-category="${user.is_office_personnel}" data-id="${user.id}" value="${user.name}">${user.name}</option>`);
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

function appendGenerated() {
	let user = $("#user").val();
	user = $("#user-list").find(`option[value="${user}"]`);
	const username = user.data("gen-uname");
	const password = user.data("gen-pass");

	console.log(username, password);

	$(`#username`).val(username);
	$(`#new-password`).val(password);
	$(`#confirm-password`).val(password);
	$(`#confirm-password-error`).html("");
}

function insertUpdateUserAccount(action, id = null) {
	const url = action === 0 ? "insert.php" : "update.php";
	const name = $("#user").val();
	const user = $("#user-list").find(`option[value="${name}"]`).data("id");
	const category = $("#user-list").find(`option[value="${name}"]`).data("category");
	const username = $("#username").val();
	const password = $("#new-password").val();
	const confirmPassword = $("#confirm-password").val();

	if (password !== confirmPassword) {
		showAlert("Passwords do not match", "danger");
		return;
	}

	$.post(
		`pages/registry/user-account-registry/scripts/${url}`,
		{
			id: id,
			user: user,
			name: name,
			category: category,
			username: username,
			password: password
		},
		function (data) {
			const response = JSON.parse(data);
			if (response.status) {
				if (action === 0) {
					appendUserAccount(response.data);
					refreshUserList();
				}

				const message = action === 0 ? "New user account has been added." : "New password has been set.";
				toggleModal("user-account-registry-modal", 1);
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

function deleteAccount(id) {
	return new Promise((resolve, reject) => {
		swalConfirmation("Are you sure you want to delete this user account?", "Delete User Account", "Delete", id, function (id) {
			$.post(
				`pages/registry/user-account-registry/scripts/delete.php`,
				{
					id: id
				},
				function (data) {
					const response = JSON.parse(data);
					if (response.status) {
						const row = userAccountDataTable.row(`#tr-user-account-${id}`);
						if (row.length) {
							row.remove().draw(false);
						}
						refreshUserList();
						showAlert("User account has been deleted.");
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
function appendUserAccount(userAccountData) {
	const { id, name, category, username, tstamp } = userAccountData;
	const action = `
		<td>
			<div class="btn-group">
				<button class="btn btn-primary btn-sm" title="New Password" onclick="showModal(1,${id})">
					<i class="fas fa-key me-1"></i>New Password
				</button>
				<button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteAccount(${id})">
					<i class="fas fa-trash me-1"></i>Delete
				</button>
			</div>
		</td>
	`;

	const newRow = userAccountDataTable.row
		.add([name, category, username, formatDateTime(tstamp), action])
		.draw(false)
		.node();
	$(newRow).attr("id", `tr-user-account-${id}`);

	dataTablesNewRow($("#tbl-user-accounts"), userAccountDataTable, newRow);
}
// end for data table manipulation
