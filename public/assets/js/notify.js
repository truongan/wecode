/**
 * Lightweight notification helper built on Bootstrap 5's native Toast.
 *
 * Usage:
 *   notify('Saved', { position: 'bottom right', className: 'success', autoHideDelay: 5000 });
 *
 * Only depends on Bootstrap's bundled JS (window.bootstrap), which is loaded
 * before this file. Exposed as the global `notify`.
 */
(function (window) {
	"use strict";

	var BG_CLASS = {
		success: "text-bg-success",
		error: "text-bg-danger",
		danger: "text-bg-danger",
		info: "text-bg-info",
		warning: "text-bg-warning",
	};

	// Backgrounds dark enough to need the white close-button variant.
	var DARK_BG = { success: true, error: true, danger: true };

	/**
	 * Resolve (creating if needed) the fixed toast container for a position
	 * string such as "bottom right" / "top left" / "top center".
	 */
	function getContainer(position) {
		position = (position || "bottom right").toLowerCase();

		var vertical = position.indexOf("top") !== -1 ? "top-0" : "bottom-0";
		var horizontal;
		if (position.indexOf("left") !== -1) {
			horizontal = "start-0";
		} else if (position.indexOf("center") !== -1) {
			horizontal = "start-50 translate-middle-x";
		} else {
			horizontal = "end-0";
		}

		var id = "toast-container-" + (vertical + "-" + horizontal).replace(/[^a-z0-9]+/g, "-");
		var container = document.getElementById(id);
		if (!container) {
			container = document.createElement("div");
			container.id = id;
			container.className = "toast-container position-fixed p-3 " + vertical + " " + horizontal;
			container.style.zIndex = "1090";
			document.body.appendChild(container);
		}
		return container;
	}

	/**
	 * Show a notification.
	 *
	 * @param {string} message Text to display.
	 * @param {{position?: string, className?: string, autoHideDelay?: number}} [options]
	 */
	function notify(message, options) {
		options = options || {};

		var className = options.className || "info";
		var bg = BG_CLASS[className] || "text-bg-secondary";
		var delay = options.autoHideDelay;
		var autohide = delay !== 0 && delay !== false;

		var toast = document.createElement("div");
		toast.className = "toast align-items-center border-0 " + bg;
		toast.setAttribute("role", "alert");
		toast.setAttribute("aria-live", "assertive");
		toast.setAttribute("aria-atomic", "true");

		var flex = document.createElement("div");
		flex.className = "d-flex";

		var body = document.createElement("div");
		body.className = "toast-body";
		body.textContent = message == null ? "" : String(message);

		var close = document.createElement("button");
		close.type = "button";
		close.className = "btn-close me-2 m-auto" + (DARK_BG[className] ? " btn-close-white" : "");
		close.setAttribute("data-bs-dismiss", "toast");
		close.setAttribute("aria-label", "Close");

		flex.appendChild(body);
		flex.appendChild(close);
		toast.appendChild(flex);

		getContainer(options.position).appendChild(toast);

		var instance = window.bootstrap.Toast.getOrCreateInstance(toast, {
			autohide: autohide,
			delay: autohide ? delay || 5000 : 5000,
		});
		toast.addEventListener("hidden.bs.toast", function () {
			toast.remove();
		});
		instance.show();

		return toast;
	}

	window.notify = notify;
})(window);
