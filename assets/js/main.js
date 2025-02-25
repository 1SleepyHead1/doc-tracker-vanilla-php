$(document).ready(function () {
	// Get the current URL
	let currentUrl = window.location.href;

	// Create a URL object
	let url = new URL(currentUrl);

	// Get the pathname and split it by '/'
	let pathname = url.pathname;
	let pathSegments = pathname.split("/");

	// The last segment of the path is the file name
	let fileName = pathSegments.pop(); // 'login.php'

	if (fileName !== "login.php") {
		if (sessionStorage.getItem("menu") == undefined || sessionStorage.getItem("menu") == null) {
			$(`#default-menu`).click();
		} else {
			loadDefaultMenu();
		}
	}

	$(`#unit-form`).submit(function (e) {
		e.preventDefault();
	});

	// initialize dashboard scrollbar
	var scrollbarDashboard = $(".sidebar .scrollbar");
	if (scrollbarDashboard.length > 0) {
		scrollbarDashboard.scrollbar();
	}

	// sidebar toggle
	var toggle_sidebar = false,
		toggle_topbar = false,
		minimize_sidebar = false,
		first_toggle_sidebar = false,
		toggle_page_sidebar = false,
		toggle_overlay_sidebar = false,
		nav_open = 0,
		quick_sidebar_open = 0,
		topbar_open = 0,
		mini_sidebar = 0,
		page_sidebar_open = 0,
		overlay_sidebar_open = 0;

	if (!toggle_sidebar) {
		var toggle = $(".sidenav-toggler");

		toggle.on("click", function () {
			if (nav_open == 1) {
				$("html").removeClass("nav_open");
				toggle.removeClass("toggled");
				nav_open = 0;
			} else {
				$("html").addClass("nav_open");
				toggle.addClass("toggled");
				nav_open = 1;
			}
		});
		toggle_sidebar = true;
	}

	if (!quick_sidebar_open) {
		var toggle = $(".quick-sidebar-toggler");

		toggle.on("click", function () {
			if (nav_open == 1) {
				$("html").removeClass("quick_sidebar_open");
				$(".quick-sidebar-overlay").remove();
				toggle.removeClass("toggled");
				quick_sidebar_open = 0;
			} else {
				$("html").addClass("quick_sidebar_open");
				toggle.addClass("toggled");
				$('<div class="quick-sidebar-overlay"></div>').insertAfter(".quick-sidebar");
				quick_sidebar_open = 1;
			}
		});

		$(".wrapper").mouseup(function (e) {
			var subject = $(".quick-sidebar");

			if (e.target.className != subject.attr("class") && !subject.has(e.target).length) {
				$("html").removeClass("quick_sidebar_open");
				$(".quick-sidebar-toggler").removeClass("toggled");
				$(".quick-sidebar-overlay").remove();
				quick_sidebar_open = 0;
			}
		});

		$(".close-quick-sidebar").on("click", function () {
			$("html").removeClass("quick_sidebar_open");
			$(".quick-sidebar-toggler").removeClass("toggled");
			$(".quick-sidebar-overlay").remove();
			quick_sidebar_open = 0;
		});

		quick_sidebar_open = true;
	}

	if (!toggle_topbar) {
		var topbar = $(".topbar-toggler");

		topbar.on("click", function () {
			if (topbar_open == 1) {
				$("html").removeClass("topbar_open");
				topbar.removeClass("toggled");
				topbar_open = 0;
			} else {
				$("html").addClass("topbar_open");
				topbar.addClass("toggled");
				topbar_open = 1;
			}
		});
		toggle_topbar = true;
	}

	if (!minimize_sidebar) {
		var minibutton = $(".toggle-sidebar");
		if ($(".wrapper").hasClass("sidebar_minimize")) {
			minibutton.addClass("toggled");
			minibutton.html('<i class="gg-more-vertical-alt"></i>');
			mini_sidebar = 1;
		}

		minibutton.on("click", function () {
			if (mini_sidebar == 1) {
				$(".wrapper").removeClass("sidebar_minimize");
				minibutton.removeClass("toggled");
				minibutton.html('<i class="gg-menu-right"></i>');
				mini_sidebar = 0;
			} else {
				$(".wrapper").addClass("sidebar_minimize");
				minibutton.addClass("toggled");
				minibutton.html('<i class="gg-more-vertical-alt"></i>');
				mini_sidebar = 1;
			}
			$(window).resize();
		});
		minimize_sidebar = true;
		first_toggle_sidebar = true;
	}

	if (!toggle_page_sidebar) {
		var pageSidebarToggler = $(".page-sidebar-toggler");

		pageSidebarToggler.on("click", function () {
			if (page_sidebar_open == 1) {
				$("html").removeClass("pagesidebar_open");
				pageSidebarToggler.removeClass("toggled");
				page_sidebar_open = 0;
			} else {
				$("html").addClass("pagesidebar_open");
				pageSidebarToggler.addClass("toggled");
				page_sidebar_open = 1;
			}
		});

		var pageSidebarClose = $(".page-sidebar .back");

		pageSidebarClose.on("click", function () {
			$("html").removeClass("pagesidebar_open");
			pageSidebarToggler.removeClass("toggled");
			page_sidebar_open = 0;
		});

		toggle_page_sidebar = true;
	}

	if (!toggle_overlay_sidebar) {
		var overlaybutton = $(".sidenav-overlay-toggler");
		if ($(".wrapper").hasClass("is-show")) {
			overlay_sidebar_open = 1;
			overlaybutton.addClass("toggled");
			overlaybutton.html('<i class="icon-options-vertical"></i>');
		}

		overlaybutton.on("click", function () {
			if (overlay_sidebar_open == 1) {
				$(".wrapper").removeClass("is-show");
				overlaybutton.removeClass("toggled");
				overlaybutton.html('<i class="icon-menu"></i>');
				overlay_sidebar_open = 0;
			} else {
				$(".wrapper").addClass("is-show");
				overlaybutton.addClass("toggled");
				overlaybutton.html('<i class="icon-options-vertical"></i>');
				overlay_sidebar_open = 1;
			}
			$(window).resize();
		});
		minimize_sidebar = true;
	}

	$(".sidebar")
		.mouseenter(function () {
			if (mini_sidebar == 1 && !first_toggle_sidebar) {
				$(".wrapper").addClass("sidebar_minimize_hover");
				first_toggle_sidebar = true;
			} else {
				$(".wrapper").removeClass("sidebar_minimize_hover");
			}
		})
		.mouseleave(function () {
			if (mini_sidebar == 1 && first_toggle_sidebar) {
				$(".wrapper").removeClass("sidebar_minimize_hover");
				first_toggle_sidebar = false;
			}
		});
	// end
});

// --globals--
var $uid, $did;

const loader = (elementId, color = "primary") => {
	elementId.html(`
		<div class="d-flex justify-content-center align-items-center mt-4">
			<div class="text-center">
				<div class="spinner-border text-${color}" role="status" style="width: 2rem; height: 2rem;">
					<span class="visually-hidden">Loading...</span>
				</div>
				<div class="mt-2">
					<small class="text-muted">Loading...</small>
				</div>
			</div>
		</div>
	`);
};

const getCurrentDate = () => {
	const date = new Date();

	let day = date.getDate();
	day = day < 10 ? "0" + day : day;
	let month = date.getMonth() + 1;
	month = month < 10 ? "0" + month : month;
	let year = date.getFullYear();

	// This arrangement can be altered based on how we want the date's format to appear.
	let currentDate = `${year}-${month}-${day}`;

	return currentDate;
};

const checkForEmptyInputs = (elementClass) => {
	// Select all input elements and filter those that are empty
	let isEmpty = false;
	const inputElements = $(`.${elementClass}`);

	var emptyInputs = inputElements.filter(function () {
		return $.trim($(this).val()) === "";
	});

	// If more than one empty input, apply red border after 3 seconds
	if (emptyInputs.length > 0) {
		isEmpty = true;
	}

	return isEmpty;
};

const validateDataListOptions = (elementId, elementDisplayId, list) => {
	let inputValue = $(`#${elementId}`).val();
	let option = $(`#${list} option[value="${inputValue}"]`);
	let listType = $(`#${elementId}`).data("list-type");
	// console.log(inputValue, option);

	if (!option.length) {
		$(`#${elementDisplayId != "" ? elementDisplayId : null}`).val("");
		$(`#${elementId},  #${elementDisplayId != "" ? elementDisplayId : null}`)
			.val("")
			.css("border-color", "red");

		showAlert(`Select a valid ${listType} from the provided list only.`, "danger");
		setTimeout(function () {
			$(`#${elementId}, #${elementDisplayId != "" ? elementDisplayId : null}`).css("border-color", "");
		}, 3000);

		return false;
	} else {
		$(`#${elementDisplayId != "" ? elementDisplayId : null}`).val(option.data("name"));
		$(`#${elementId}, #${elementDisplayId != "" ? elementDisplayId : null}`).css("border-color", "");

		return true;
	}
};

const playAlertSounds = (type = "success") => {
	// Get the existing audio element or create a new one
	let audioElement = document.getElementById("notice-audio");

	if (!audioElement) {
		// Create a new audio element
		audioElement = document.createElement("audio");
		audioElement.id = "notice-audio";
		audioElement.style.display = "none"; // Hide the audio element if you don't want it visible
		document.body.appendChild(audioElement); // Append it to the body (or any other container)
	}

	// Determine the audio source based on type
	const audioSource = type.toLowerCase() === "success" ? "assets/audio/notice/success.mp3" : "assets/audio/notice/error.mp3";

	// Set the source attribute
	audioElement.setAttribute("src", audioSource);

	// Ensure the audio is loaded and then play it
	audioElement.addEventListener(
		"canplaythrough",
		() => {
			audioElement.play().catch((error) => {
				console.error("Error playing audio:", error);
			});
		},
		{ once: true }
	);
};

// types{primary,secondary,info,success,warning,danger}
const showAlert = (message, type = "success") => {
	const removeAlert = (time = 3000) => {
		const element = $("._alert");
		setTimeout(() => {
			element.remove();
		}, time);
	};

	const content = {};
	content.message = message;
	content.title = "Notice!";
	content.icon = "fa fa-bell";

	content.url = "";
	content.target = "";

	playAlertSounds(type);

	$.notify(content, {
		type: type,
		placement: {
			from: "top",
			align: "right"
		},
		time: 1000,
		delay: 0,
		z_index: 2000 // Higher z-index to show above modals
	});
	removeAlert();
};

// icon {success, warning, error},
const swalAlert = (title = "Success", icon = "success", className = "success", text = "", timer = 3000) => {
	playAlertSounds(title);
	swal({
		timer: timer,
		title: title,
		text: text,
		icon: icon,
		buttons: {
			confirm: {
				text: "Confirm",
				value: true,
				visible: true,
				className: `btn btn-${className}`,
				closeModal: true
			}
		},
		position: "center",
		willOpen: () => {
			const sweetAlertContainer = document.querySelector(".swal-overlay");
			if (sweetAlertContainer) {
				sweetAlertContainer.style.zIndex = 1060;
			}
		}
	});
};

const swalConfirmation = (text, title, confirmText, id = null, callback = null, btnText = "Delete", timer = 3000) => {
	swal({
		title: "Notice!",
		text: text,
		icon: "warning",
		buttons: {
			cancel: {
				visible: true,
				className: "btn btn-danger"
			},
			confirm: {
				text: btnText,
				className: "btn btn-success"
			}
		}
	}).then((confirmed) => {
		if (confirmed) {
			if (callback) {
				callback(id);
			}
		}
	});
};

const pageHeader = (title, icon, subTitle) => {
	$(`#ph-title`).html(title);
	$(`#ph-icon`).removeClass().addClass(icon);
	$(`#ph-sub-menu`).html(subTitle);
};

const getPageHeader = (menu) => {
	let title, icon, subTitle;

	switch (menu) {
		case "user-registry":
			title = "Registry";
			icon = "fas fa-book";
			subTitle = "User Registry";
			parent = "registry";
			break;
		case "user-account-registry":
			title = "Registry";
			icon = "fas fa-book";
			subTitle = "User Account Registry";
			parent = "registry";
			break;
		case "office-registry":
			title = "Registry";
			icon = "fas fa-book";
			subTitle = "Office Registry";
			parent = "registry";
			break;
		case "document-type-registry":
			title = "Registry";
			icon = "fas fa-book";
			subTitle = "Document Type Registry";
			parent = "registry";
			break;
		case "document-mapping":
			title = "Settings";
			icon = "fas fa-cog";
			subTitle = "Document Transaction Setting";
			parent = "settings";
			break;
		case "sms-notifications":
			title = "Settings";
			icon = "fas fa-cog";
			subTitle = "SMS Notifications Setting";
			parent = "settings";
			break;
		case "doc-submission":
			title = "Transactions";
			icon = "fas fa-receipt";
			subTitle = "Document Submission";
			parent = "transactions";
			break;
		default:
			title = "Dashboard";
			icon = "fas fa-home";
			subTitle = "Home Panel";
			parent = "dashboard";
			break;
	}

	pageHeader(title, icon, subTitle);
};

const formatDateTime = (dt) => {
	// Parse the input string into a Date object
	const date = new Date(dt.replace(" ", "T")); // Replace space with 'T' for ISO format

	const months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];

	const day = String(date.getDate()).padStart(2, "0");
	const year = date.getFullYear();

	const hours = date.getHours();
	const minutes = String(date.getMinutes()).padStart(2, "0");

	// Determine AM or PM
	const period = hours >= 12 ? "PM" : "AM";
	// Convert hours from 24-hour to 12-hour format
	const formattedHours = String(hours % 12 || 12).padStart(2, "0");

	// Format the date string
	return `${months[date.getMonth()]} ${day}, ${year} ${formattedHours}:${minutes} ${period}`;
};

const formatDate = (d) => {
	const date = new Date(d);
	const months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];

	const day = String(date.getDate()).padStart(2, "0"); // Get day with leading zero
	const year = date.getFullYear(); // Get full year

	// Format the date string
	return `${months[date.getMonth()]} ${day}, ${year} `;
};

const formatNumber = (n) => {
	return new Intl.NumberFormat("en-US", {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	}).format(n);
};

const calcDaysRemaining = (d) => {
	// Parse the input date string to a Date object
	const targetDate = new Date(d);
	const currentDate = new Date();

	// Calculate the time difference in milliseconds
	const timeDifference = targetDate - currentDate;

	// Convert milliseconds to days
	const daysLeft = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

	// Return the result, ensuring it's not negative
	return daysLeft >= 0 ? daysLeft : 0;
};

// if action == 0 {show modal} else {hide}
async function toggleModal(modalId, action = 0) {
	const modalElement = $(`#${modalId}`);
	action == 0 ? await modalElement.modal("show") : await modalElement.modal("hide");
}

// if action == 0 {show offcanvas} else {hide}
async function toggleCanvas(canvasId, action = 0) {
	const canvasElement = $(`#${canvasId}`);

	if (action == 0) {
		await canvasElement.offcanvas("show");
	} else {
		await canvasElement.offcanvas("hide");
		canvasElement.html("");
	}
}

const countNotifs = () => {
	const btnReadAll = $("#btn-read-notifs");
	const badgeCount = $("#_notif-count");
	const count = $(`#_tbl-notifs tbody tr:not([id="tr-no-notifs"])[read="0"]`).length;
	badgeCount.html(count);

	btnReadAll.prop("hidden", true);

	if (count === 0) {
		btnReadAll.prop("hidden", true);
	} else {
		btnReadAll.prop("hidden", false);
	}
};

//if you want to empty modal every close add 'modal-reset' class to its parent div element
$(".modal-reset").on("hidden.bs.modal", function (e) {
	const modalElement = $(this);
	const modalContent = modalElement.find(".modal-content");

	modalContent.html("");
});

const capitalizeFirstLetterWord = (str) => {
	return str
		.split(" ")
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(" ");
};

const numberToWords = (num) => {
	const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];

	const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

	const thousands = ["", "Thousand", "Million", "Billion", "Trillion", "Quadrillion", "Quintillion"];

	if (num === 0) {
		return "zero pesos";
	}

	let isNegative = num < 0;
	num = Math.abs(num);

	// Split the number into integer and decimal parts
	let [integerPart, decimalPart] = num.toString().split(".").map(Number);

	let words = convertInteger(integerPart, ones, tens, thousands);

	if (decimalPart) {
		words += " and " + convertWholeNumber(decimalPart, ones, tens) + " cents";
	} else {
		words += " pesos";
	}

	return (isNegative ? "negative " : "") + words.trim();
};

const convertInteger = (num, ones, tens, thousands) => {
	let words = "";
	let unitIndex = 0;

	while (num > 0) {
		const remainder = num % 1000;
		if (remainder !== 0) {
			words = convertHundreds(remainder, ones, tens) + " " + thousands[unitIndex] + " " + words;
		}
		num = Math.floor(num / 1000);
		unitIndex++;
	}

	return words; // Add "pesos" at the end of the integer part
};

const convertHundreds = (num, ones, tens) => {
	let words = "";

	if (num >= 100) {
		words += ones[Math.floor(num / 100)] + " Hundred ";
		num %= 100;
	}

	if (num >= 20) {
		words += tens[Math.floor(num / 10)] + " ";
		num %= 10;
	}

	if (num > 0) {
		words += ones[num] + " ";
	}

	return words;
};

// New function to convert the decimal part (cents) as a whole number
const convertWholeNumber = (num, ones, tens) => {
	let words = "";

	if (num >= 100) {
		words += convertHundreds(num) + " ";
	} else {
		if (num >= 20) {
			words += tens[Math.floor(num / 10)] + " ";
			num %= 10;
		}

		if (num > 0) {
			words += ones[num] + " ";
		}
	}

	return words.trim();
};

const previewPrintQr = (e) => {
	const content = $(`#${e}`).html();
	const myWindow = window.open("", "Print", "height=600,width=800");

	myWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
				<link rel="stylesheet" href="assets/css/plugins.min.css" />
				<link rel="stylesheet" href="assets/css/style.min.css" />
				<script src="assets/js/core/bootstrap.min.js"></script>
            </head>
            <body style="-webkit-print-color-adjust:exact;">
                ${content}
            </body>
        </html>
    `);
	myWindow.document.close();
	myWindow.focus();

	myWindow.onload = function () {
		myWindow.focus();
		myWindow.print();
		myWindow.close();
	};

	return true;
};

const donwloadQrCode = (f) => {
	window.location.href = `assets/uploads/qr-codes/${f}.png`;
};

const previewPrint = (e) => {
	const content = $(`#${e}`).html();
	const myWindow = window.open("", "Print", "height=600,width=800");

	myWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
                <link rel="stylesheet" href="assets/css/plugins.min.css" />
                <link rel="stylesheet" href="assets/css/style.min.css" />
                <script src="assets/js/core/bootstrap.min.js"></script>
            </head>
            <body class="te d-flex justify-content-center align-items-center" style="-webkit-print-color-adjust:exact; height:100vh;">
                ${content}
            </body>
        </html>
    `);
	myWindow.document.close();
	myWindow.focus();

	myWindow.onload = function () {
		myWindow.focus();
		myWindow.print();
		myWindow.close();
	};

	return true;
};

const previewPrintChart = (e) => {
	// Get the canvas element
	const canvas = document.getElementById(`${e}`);

	// Convert the canvas to a Data URL
	const dataUrl = canvas.toDataURL();

	// Create a new window
	const printWindow = window.open("", "Print Chart", "height=600,width=800");

	// Write the HTML to the new window
	printWindow.document.write(`
        <html>
            <head>
                <title>Print Chart</title>
				<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
				<link rel="stylesheet" href="assets/css/plugins.min.css" />
				<link rel="stylesheet" href="assets/css/style.min.css" />
				<script src="assets/js/core/bootstrap.min.js"></script>
                <style>
                    body {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                </style>
            </head>
            <body>
                <img src="${dataUrl}" />
            </body>
        </html>
    `);

	setTimeout(() => {
		printWindow.print();
		printWindow.close();
	}, 3000);

	return true;
};

const dataTablesNewRow = (table, dataTable, newRow) => {
	// Get all rows data and add the new row to the beginning
	const rows = dataTable.rows().data().toArray();
	rows.unshift(newRow);

	// Clear the table and add the rows back in the new order
	dataTable.clear().rows.add(rows).draw(false);

	const tableBody = table.find("tbody");
	const trToRemove = tableBody.find("tr:not([id])");

	dataTable.row($(trToRemove)).remove().draw(false);
};
// end

// --login | logout--
const loginAttempt = () => {
	if (checkForEmptyInputs("login-i")) {
		return;
	}

	const username = $("#username").val();
	const password = $("#password").val();

	$.post(`script/actions/login.php`, { username: username, password: password }, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			window.location.href = "index.php";
		} else {
			showAlert(response.message, "danger");
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
};

const logout = () => {
	$.post(`script/actions/logout.php`, {}, function (data) {
		if (data === "s") {
			sessionStorage.clear();
			window.location = "login.php";
		} else {
			showAlert(data, "danger");
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
};
// end

// --menus--
const loadMenu = (url, menu) => {
	const container = $("#_container");
	sessionStorage.setItem("menu", menu);

	getPageHeader(menu);

	loader(container);
	container.load(url);

	// setTimeout(() => {
	// 	container.load(url);
	// }, 2000);
};

const loadDefaultMenu = () => {
	const menu = sessionStorage.getItem("menu");
	// console.log(menu);

	const subMenu = $(`#${menu}-menu`);
	const parentSubMenu = subMenu.parent();
	const parentMenu = $(`#parent-${subMenu.data("parent")}`);
	const collapseDiv = $(`#${subMenu.data("parent")}`);
	const url = subMenu.data("url");

	$(`.nav-item, .sub-menu`).removeClass("active");
	parentMenu.addClass("active");
	collapseDiv.addClass("show");
	parentSubMenu.addClass("active");
	loadMenu(url, menu);
};

// user edit profile
const showUserProfileModalContent = async (id) => {
	try {
		const container = $(`#user-profile-modal`).find(`div[id="_modal-details"]`);
		const response = await $.ajax({
			url: `views/user-profile/c/modal-details.php`,
			type: "POST",
			data: { id: id }
		});

		container.html(response);
		await toggleModal("user-profile-modal");
	} catch (error) {
		if (error.status == 500) {
			console.error("Internal Server Error (500) occurred.");
		} else if (error.status == 404) {
			console.error("Resource not found (404) error.");
		} else if (error.status == 403) {
			console.error("Forbidden (403) error.");
		} else if (error.status == 401) {
			console.error("Unauthorized (401) error.");
		} else if (error.status == 400) {
			console.error("Bad Request (400) error.");
		} else {
			console.error("Unexpected Error:", error);
		}
	}
};

const updateProfile = (id) => {
	if (checkForEmptyInputs("user-profile-i")) {
		return;
	}

	const formElement = $("#user-profile-form");
	// const uname = formElement.find(`input[name="usern"]`).val();
	const cword = formElement.find(`input[name="c-pword"]`).val();
	const pword = formElement.find(`input[name="n-pword"]`).val();
	const confirmPword = formElement.find(`input[name="confirm-pword"]`).val();
	const pwrodLenthg = pword.length;
	const confirmPwordLenthg = confirmPword.length;

	if (pwrodLenthg < 6 || pwrodLenthg > 15) {
		return;
	}

	if (confirmPwordLenthg < 6 || confirmPwordLenthg > 15) {
		return;
	}

	if (confirmPword != pword) {
		showAlert("New and confirm password does not match.", "danger");
		return;
	}

	$.post(
		`views/user-profile/a/u.php`,
		{
			// uname: uname,
			cword: cword,
			pword: pword,
			id: id
		},
		function (data) {
			if (data === "s") {
				showAlert("Password has been changed.");
				toggleModal("user-profile-modal", 1);
			} else {
				showAlert(data, "danger");
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
};
// end

// globals2
const showInkindDtems = async (id) => {
	try {
		const container = $(`#inkind-d-modal`).find(`div[id="_modal-details"]`);
		const response = await $.ajax({
			url: `views/donation/inkind-d/c/d-items.php`,
			type: "POST",
			data: { id: id }
		});

		container.html(response);
		await toggleModal("inkind-d-modal");
	} catch (error) {
		if (error.status == 500) {
			console.error("Internal Server Error (500) occurred.");
		} else if (error.status == 404) {
			console.error("Resource not found (404) error.");
		} else if (error.status == 403) {
			console.error("Forbidden (403) error.");
		} else if (error.status == 401) {
			console.error("Unauthorized (401) error.");
		} else if (error.status == 400) {
			console.error("Bad Request (400) error.");
		} else {
			console.error("Unexpected Error:", error);
		}
	}
};

const showExternalDItems = async (id) => {
	try {
		const container = $(`#external-d-modal`).find(`div[id="_modal-details"]`);
		const response = await $.ajax({
			url: `views/donation/external-d/c/d-items.php`,
			type: "POST",
			data: { id: id }
		});

		container.html(response);
		await toggleModal("external-d-modal");
	} catch (error) {
		if (error.status == 500) {
			console.error("Internal Server Error (500) occurred.");
		} else if (error.status == 404) {
			console.error("Resource not found (404) error.");
		} else if (error.status == 403) {
			console.error("Forbidden (403) error.");
		} else if (error.status == 401) {
			console.error("Unauthorized (401) error.");
		} else if (error.status == 400) {
			console.error("Bad Request (400) error.");
		} else {
			console.error("Unexpected Error:", error);
		}
	}
};
// end

// user notifs

const getUserNotifs = async () => {
	const notifsTable = $("#_tbl-notifs");
	const trNotifs = $('#_tbl-notifs tbody tr:not([id="tr-no-notifs"])')
		.map(function () {
			return $(this).attr("data-id");
		})
		.get();

	try {
		const response = await $.ajax({
			url: `script/notifs/user/get-user-notifs.php`,
			type: "POST",
			data: { id: $did }
		});

		const json = JSON.parse(response);
		const notifs = json.data.map((obj) => obj.id);
		const missingNotif = trNotifs.filter((value) => !notifs.includes(value));
		missingNotif.map((value) => $(`#tr-notif-${value}`).remove());

		if (json.data.length == 0) {
			if (!$("#tr-no-notifs").length) {
				await notifsTable.find("tbody").prepend(`
					<tr id="tr-no-notifs" class="no-hover border-bottom px-3" onclick="">
						<td class="text-center">
							<i class="fas fa-bell-slash text-danger" style="font-size: 1.5rem;"></i>
						</td>
						<td colspan="">
							<small class="notif-text text-muted">Nothing to see here.</small>
						</td>
					</tr>
				`);
			}
		} else {
			if ($("#tr-no-notifs").length) {
				await $("#tr-no-notifs").remove();
			}

			await $.each(json.data, function (key, row) {
				if (!$(`#tr-notif-${row.id}`).length) {
					let a;

					if (row.type === "donation") {
						a = `
							<tr id="tr-notif-${row.id}" class="border-bottom px-3" data-id="${row.id}" type="${row.type}" read="${row.is_read}" onclick="${row.is_read == 0 ? "readUserNotif('0', this)" : ""}" style="cursor: pointer;">
								<td class="text-center">
									<i class="fas fa-bell text-primary" style="font-size: 1.5rem;"></i>
								</td>
								<td>
									<small name="time" class="${row.is_read == 0 ? "text-primary" : "text-muted"} notif-text fw-bolder">${formatDateTime(row.tstamp)}</small>
									<p class="notif-text fw-thin my-2"><b>${row.quantity} ${row.unit_name}</b> of <b>${row.item}</b> has been donated to <b>${row.grantee}</b></p>
									<p class="notif-text mb-1 fw-thin">Reference Number: <b>${row.reference_no}</b></p>
								</td>
							</tr>
						`;
					} else {
						a = `
							<tr id="tr-notif-${row.id}" class="border-bottom px-3" data-id="${row.id}" type="${row.type}" read="${row.is_read}" onclick="${row.is_read == 0 ? "readUserNotif('0', this)" : ""}" style="cursor: pointer;">
								<td class="text-center">
									<i class="fas fa-bell text-danger" style="font-size: 1.5rem;"></i>
								</td>
								<td>
									<small name="time" class="${row.is_read == 0 ? "text-danger" : "text-muted"} notif-text fw-bolder">${formatDateTime(row.tstamp)}</small>
									<p class="notif-text fw-thin my-2"><b>${row.quantity} ${row.unit_name}</b> of <b>${row.item}</b> has expired.</p>
									<p class="notif-text mb-1 fw-thin">Reference Number: <b>${row.reference_no}</b></p>
								</td>
							</tr>
						`;
					}

					notifsTable.find("tbody").prepend(a);
				}
			});
		}

		countNotifs();
	} catch (error) {
		if (error.status == 500) {
			console.error("Internal Server Error (500) occurred.");
		} else if (error.status == 404) {
			console.error("Resource not found (404) error.");
		} else if (error.status == 403) {
			console.error("Forbidden (403) error.");
		} else if (error.status == 401) {
			console.error("Unauthorized (401) error.");
		} else if (error.status == 400) {
			console.error("Bad Request (400) error.");
		} else {
			console.error("Unexpected Error:", error);
		}
	}
};

const updateUserNotif = (action, e = null) => {
	if (action == 0) {
		const type = e.attr("type");
		const timeElement = e.find(`small[name="time"]`);
		const color = type === "donation" ? "text-primary" : "text-danger";

		timeElement.removeClass(color).addClass("text-muted");
		e.attr("read", 1);
		e.attr("onclick", "");
	} else {
		const tr = $(`#_tbl-notifs tbody tr:not([id="tr-no-notifs"])[read="0"]`);

		tr.each(function () {
			const type = $(this).attr("type");
			const timeElement = $(this).find(`small[name="time"]`);
			const color = type === "donation" ? "text-primary" : "text-danger";

			timeElement.removeClass(color).addClass("text-muted");
			$(this).attr("read", 1);
			$(this).attr("onclick", "");
		});
	}

	countNotifs();
};

const readUserNotif = (action, e = null) => {
	const element = $(e);
	const id = element.attr("data-id");
	const type = element.attr("type");

	$.post(`script/notifs/user/read-notif.php`, { action: action, id: id, type: type }, function (data) {
		if (data === "s") {
			updateUserNotif(action, element);
		} else {
			showAlert(data, "danger");
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
};

// end

// admin notifs

const getAdminNotifs = async () => {
	const notifsTable = $("#_tbl-notifs");
	const trNotifs = $('#_tbl-notifs tbody tr:not([id="tr-no-notifs"])')
		.map(function () {
			return $(this).attr("data-id");
		})
		.get();

	function trToAppend(d) {
		let e;
		if (calcDaysRemaining(d.expiry_date) === 0) {
			e = `
				<tr id="tr-notif-${d.id}" class="border-bottom px-3" data-id="${d.id}" type="expired" read="${d.is_read}" onclick="${d.is_read == 0 ? "readAdminNotif('0', this)" : ""}" style="cursor: pointer;">
					<td class="text-center">
						<i class="fas fa-bell text-danger" style="font-size: 1.5rem;"></i>
					</td>
					<td>
						<small name="time" class="${d.is_read == 0 ? "text-danger" : "text-muted"} notif-text fw-bolder">${formatDateTime(d.tstamp)}</small>
						<p class="notif-text fw-thin my-2"><b>${d.quantity} ${d.unit_name}</b> of <b>${d.item}</b> from <b>${d.donor === "  " ? "Anonymous" : d.donor}</b> has expired.</p>
						<p class="notif-text mb-1 fw-thin">Reference Number: <b>${d.reference_no}</b></p>
					</td>
				</tr>
			`;
		} else if (calcDaysRemaining(d.expiry_date) === 1) {
			e = `
				<tr id="tr-notif-${d.id}" class="border-bottom px-3" data-id="${d.id}" type="soon-to-expire" read="${d.is_read}" onclick="${d.is_read == 0 ? "readAdminNotif('0', this)" : ""}" style="cursor: pointer;">
					<td class="text-center">
						<i class="fas fa-bell text-primary" style="font-size: 1.5rem;"></i>
					</td>
					<td>
						<small name="time" class="${d.is_read == 0 ? "text-primary" : "text-muted"} notif-text fw-bolder">${formatDateTime(d.tstamp)}</small>
						<p class="notif-text fw-thin my-2"><b>${d.quantity} ${d.unit_name}</b> of <b>${d.item}</b> from <b>${d.donor === "  " ? "Anonymous" : d.donor}</b> will expire within <b>${calcDaysRemaining(d.expiry_date)}</b> day.</p>
						<p class="notif-text mb-1 fw-thin">Reference Number: <b>${d.reference_no}</b></p>
					</td>
				</tr>
			`;
		} else {
			e = `
				<tr id="tr-notif-${d.id}" class="border-bottom px-3" data-id="${d.id}" type="soon-to-expire" read="${d.is_read}" onclick="${d.is_read == 0 ? "readAdminNotif('0', this)" : ""}" style="cursor: pointer;">
					<td class="text-center">
						<i class="fas fa-bell text-primary" style="font-size: 1.5rem;"></i>
					</td>
					<td>
						<small name="time" class="${d.is_read == 0 ? "text-primary" : "text-muted"} notif-text fw-bolder">${formatDateTime(d.tstamp)}</small>
						<p class="notif-text fw-thin my-2"><b>${d.quantity} ${d.unit_name}</b> of <b>${d.item}</b> from <b>${d.donor === "  " ? "Anonymous" : d.donor}</b> will expire within <b>${calcDaysRemaining(d.expiry_date)}</b> days.</p>
						<p class="notif-text mb-1 fw-thin">Reference Number: <b>${d.reference_no}</b></p>
					</td>
				</tr>
			`;
		}

		return e;
	}

	try {
		const response = await $.ajax({
			url: `script/notifs/admin/get-admin-notifs.php`,
			type: "POST",
			data: { id: $did }
		});

		const json = JSON.parse(response);
		const notifs = json.data.map((obj) => obj.id);
		const missingNotif = trNotifs.filter((value) => !notifs.includes(value));
		missingNotif.map((value) => $(`#tr-notif-${value}`).remove());

		if (json.data.length == 0) {
			if (!$("#tr-no-notifs").length) {
				await notifsTable.find("tbody").prepend(`
					<tr id="tr-no-notifs" class="no-hover border-bottom px-3" onclick="">
						<td class="text-center">
							<i class="fas fa-bell-slash text-danger" style="font-size: 1.5rem;"></i>
						</td>
						<td colspan="">
							<small class="notif-text text-muted">Nothing to see here.</small>
						</td>
					</tr>
				`);
			}
		} else {
			if ($("#tr-no-notifs").length) {
				await $("#tr-no-notifs").remove();
			}

			await $.each(json.data, function (key, row) {
				if (!$(`#tr-notif-${row.id}`).length) {
					notifsTable.find("tbody").prepend(trToAppend(row));
				}
			});
		}

		countNotifs();
	} catch (error) {
		if (error.status == 500) {
			console.error("Internal Server Error (500) occurred.");
		} else if (error.status == 404) {
			console.error("Resource not found (404) error.");
		} else if (error.status == 403) {
			console.error("Forbidden (403) error.");
		} else if (error.status == 401) {
			console.error("Unauthorized (401) error.");
		} else if (error.status == 400) {
			console.error("Bad Request (400) error.");
		} else {
			console.error("Unexpected Error:", error);
		}
	}
};

const updateAdminNotif = (action, e = null) => {
	if (action == 0) {
		const type = e.attr("type");
		const timeElement = e.find(`small[name="time"]`);
		const color = type === "soon-to-expire" ? "text-primary" : "text-danger";

		timeElement.removeClass(color).addClass("text-muted");
		e.attr("read", 1);
		e.attr("onclick", "");
	} else {
		const tr = $(`#_tbl-notifs tbody tr:not([id="tr-no-notifs"])[read="0"]`);

		tr.each(function () {
			const type = $(this).attr("type");
			const timeElement = $(this).find(`small[name="time"]`);
			const color = type === "soon-to-expire" ? "text-primary" : "text-danger";

			timeElement.removeClass(color).addClass("text-muted");
			$(this).attr("read", 1);
			$(this).attr("onclick", "");
		});
	}

	countNotifs();
};

const readAdminNotif = (action, e = null) => {
	const element = $(e);
	const id = element.attr("data-id");

	$.post(`script/notifs/admin/read-notif.php`, { action: action, id: id }, function (data) {
		if (data === "s") {
			updateAdminNotif(action, element);
		} else {
			showAlert(data, "danger");
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
};

// end

const dbBackup = () => {
	$.post(`script/a/db-backup/create-file.php`, {}, function (data) {
		const res = JSON.parse(data);
		if (res.status == 200) {
			// window.open(`sql/backup/${res.file_name}`, "_blank");
			const e = document.getElementById("db-backup");
			e.click();
		} else {
			showAlert(data, "danger");
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
};

// for chatbot
// Chat Widget Functionality
$("#chatToggleBtn").on("click", function () {
	$("#chatBox").toggle();
});

$("#chatCloseBtn").on("click", function () {
	$("#chatBox").hide();
});

$("#sendMessage").on("click", sendChatMessage);

$("#chatInput").on("keypress", function (e) {
	if (e.which == 13) {
		// Enter key
		sendChatMessage();
	}
});

function sendChatMessage() {
	const message = $("#chatInput").val().trim();
	if (message) {
		// Add user message
		const userMessage = `
			<div class="chat-message user">
				<div class="message-content">
					${message}
				</div>
			</div>
		`;

		$("#chatBody").append(userMessage);
		// Clear input
		$("#chatInput").val("");
		// Scroll to bottom
		$("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);

		// Simulate bot response (you can replace this with actual API call)
		setTimeout(() => {
			$.post(`script/chatbot/a/a.php`, { message: message }, function (data) {
				if (data === "s") {
					const botMessage = `
						<div class="chat-message bot">
							<div class="message-content">
								This is a demo response. Replace with actual chatbot logic.
							</div>
						</div>
					`;
					$("#chatBody").append(botMessage);
					$("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
				} else {
					showAlert(data, "danger");
				}
			}).fail(function (jqXHR, textStatus, errorThrown) {
				if (jqXHR.status == 500) {
					console.error("Internal Server Error (500) occurred.");
				} else if (jqXHR.status == 404) {
					console.error("Resource not found (404) error.");
				} else if (jqXHR.status == 403) {
					console.error("Forbidden (403) error.");
				} else if (jqXHR.status == 401) {
					console.error("Unauthorized (401) error.");
				} else if (jqXHR.status == 400) {
					console.error("Bad Request (400) error.");
				} else {
					console.error("Unexpected Error: " + textStatus, errorThrown);
				}
			});
		}, 1000);
	}
}
// end for chatbot
