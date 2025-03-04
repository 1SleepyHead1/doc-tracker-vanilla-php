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

	if (fileName !== "login.php" || fileName !== "docstats.php") {
		if (sessionStorage.getItem("menu") === undefined || sessionStorage.getItem("menu") === null) {
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
			subTitle = "Home";
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

const showDocStats = (dn) => {
	window.open(`docstats.php?dn=${dn}`, "_blank");
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
	const link = document.createElement("a");
	link.href = `assets/uploads/qr-codes/${f}.png`;
	link.download = `${fileName}.png`;
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
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
		const container = $(`#update-user-profile-modal`).find(`div[id="_modal-content"]`);
		const response = await $.ajax({
			url: `pages/user-profile/user-profile.php`,
			type: "POST",
			data: { id: id }
		});

		container.html(response);
		await toggleModal("update-user-profile-modal");
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
	const formElement = $("#update-user-profile-form");
	const cword = formElement.find(`input[name="current-password"]`).val();
	const pword = formElement.find(`input[name="new-password"]`).val();
	const confirmPword = formElement.find(`input[name="confirm-password"]`).val();
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
// end edit profile

// notifs
const countNotifs = () => {
	const notificationItems = $("#_notifications").find(".notification-item").length;
	const btnReadAllNotifs = $("#btn-read-notifs");
	const noNotif = $("#no-notifications");
	const notifCount = $("#_notif-count");

	if (notificationItems > 0) {
		notifCount.text(notificationItems).prop("hidden", false);
		btnReadAllNotifs.prop("hidden", false);
		noNotif.prop("hidden", true);
	} else {
		notifCount.prop("hidden", true);
		btnReadAllNotifs.prop("hidden", true);
		noNotif.prop("hidden", false);
	}

	if ($(".dropdown-footer a").text().trim() === "See All Notifications") {
		$(".notification-item").hide();
		$(".notification-item:lt(10)").show();
	}
};

const readNotif = (a, e = null) => {
	const element = $(e);
	const id = element.data("id");

	$.post(`script/notifs/scripts/read.php`, { action: a, id: id || null }, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			if (a === 0) {
				$(`.notification-item[data-id="${id}"]`).remove();
			} else {
				$(`.notification-item`).remove();
			}

			countNotifs();
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

const adminNotifs = async () => {
	const parentElement = $("#_notifications");
	const notifs = parentElement.find(".notification-item");
	let existings = [];

	notifs.each(function () {
		existings.push($(this).data("id"));
	});

	await $.post(`script/notifs/admin-notifs.php`, { existings: JSON.stringify(existings) }, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			const notifs = response.data;

			if (notifs) {
				$.each(notifs, function (key, notif) {
					const notificationTemplates = {
						Forwarded: {
							icon: "fa-paper-plane",
							color: "text-info",
							title: "Document Forwarded",
							message: "has been forwarded to the next office"
						},
						"For Release": {
							icon: "fa-check-circle",
							color: "text-success",
							title: "Document is Ready for Releasing",
							message: "is now ready for releasing"
						},
						Rejected: {
							icon: "fa-exclamation-circle",
							color: "text-danger",
							title: "Document has been Rejected",
							message: "has been rejected"
						},
						default: {
							icon: "fa-file-alt",
							color: "text-primary",
							title: "New Document Submitted",
							message: "has been submitted"
						}
					};

					const template = notificationTemplates[notif.status] || notificationTemplates.default;

					const notifElement = `
						<div class="notification-item p-3 border-bottom" data-id="${notif.id}">
							<div class="d-flex">
								<div class="icon me-3">
									<i class="fas ${template.icon} fa-lg ${template.color}"></i>
								</div>
								<div class="content flex-grow-1">
									<h6 class="mb-1">${template.title}</h6>
									<p class="text-muted small mb-2">Document <b>${notif.doc_no}</b> ${template.message}.</p>
									<span class="text-muted smaller">${formatDateTime(notif.tstamp)}</span>
									<br>
									<button class="btn btn-link text-primary p-0 fw-semibold" style="font-size: 13px" onclick="showDocStats('${notif.doc_no}')">
										View Document
									</button>
								</div>
								<div class="actions">
									<button class="btn btn-link text-muted p-0" data-id="${notif.id}" onclick="readNotif(0,this)">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					`;

					parentElement.prepend(notifElement);
				});
			}

			countNotifs();
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

const officeNotifs = async () => {
	const parentElement = $("#_notifications");

	await $.post(`script/notifs/office-notifs.php`, { officeId: $("#_container").attr("of") }, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			const notifs = response.data;

			if (notifs) {
				parentElement.html("");
				$.each(notifs, function (key, notif) {
					const notifElement = `
						<div class="notification-item p-3 border-bottom" data-id="${notif.id}">
							<div class="d-flex">
								<div class="icon me-3">
									<i class="fas fa-exclamation-circle fa-lg text-info"></i>
								</div>
								<div class="content flex-grow-1">
									<h6 class="mb-1">Document in need of Action</h6>
									<p class="text-muted small mb-2">Document <b>${notif.doc_no}</b> is in need of action from this office.</p>
									<span class="text-muted smaller">${formatDateTime(notif.tstamp)}</span>
									<br>
									<button class="btn btn-link text-primary p-0 fw-semibold" style="font-size: 13px" onclick="showDocStats('${notif.doc_no}')">
										View Document
									</button>
								</div>
							</div>
						</div>
					`;
					parentElement.prepend(notifElement);
				});
			}

			countNotifs();
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

const submitterNotifs = async () => {
	const parentElement = $("#_notifications");
	const notifs = parentElement.find(".notification-item");
	let existings = [];

	notifs.each(function () {
		existings.push($(this).data("id"));
	});

	await $.post(`script/notifs/submitter-notifs.php`, { u: $("#_container").attr("u"), existings: JSON.stringify(existings) }, function (data) {
		const response = JSON.parse(data);
		if (response.status) {
			const notifs = response.data;

			if (notifs) {
				$.each(notifs, function (key, notif) {
					const notificationTemplates = {
						Forwarded: {
							icon: "fa-paper-plane",
							color: "text-info",
							title: "Document Forwarded",
							message: "has been forwarded to the next office"
						},
						"For Release": {
							icon: "fa-check-circle",
							color: "text-success",
							title: "Document is Ready for Releasing",
							message: "is now ready for releasing"
						},
						Rejected: {
							icon: "fa-exclamation-circle",
							color: "text-danger",
							title: "Document has been Rejected",
							message: "has been rejected"
						},
						default: {
							icon: "fa-file-alt",
							color: "text-primary",
							title: "New Document Submitted",
							message: "has been submitted"
						}
					};

					const template = notificationTemplates[notif.status] || notificationTemplates.default;

					const notifElement = `
						<div class="notification-item p-3 border-bottom" data-id="${notif.id}">
							<div class="d-flex">
								<div class="icon me-3">
									<i class="fas ${template.icon} fa-lg ${template.color}"></i>
								</div>
								<div class="content flex-grow-1">
									<h6 class="mb-1">${template.title}</h6>
									<p class="text-muted small mb-2">Document <b>${notif.doc_no}</b> ${template.message}.</p>
									<span class="text-muted smaller">${formatDateTime(notif.tstamp)}</span>
									<br>
									<button class="btn btn-link text-primary p-0 fw-semibold" style="font-size: 13px" onclick="showDocStats('${notif.doc_no}')">
										View Document
									</button>
								</div>
								<div class="actions">
									<button class="btn btn-link text-muted p-0" data-id="${notif.id}" onclick="readNotif(0,this)">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					`;

					parentElement.prepend(notifElement);
				});
			}

			countNotifs();
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
// end notifs

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
