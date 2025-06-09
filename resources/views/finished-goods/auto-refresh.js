/**
 * Auto-refresh mechanism for Finished Goods live stock updates
 * This script ensures the DataTable refreshes automatically to show real-time stock changes
 */

(function () {
	"use strict";

	// Configuration
	const REFRESH_INTERVAL = 30000; // 30 seconds
	const VISIBILITY_REFRESH_DELAY = 2000; // 2 seconds after tab becomes visible

	let refreshTimer = null;
	let isPageVisible = true;

	/**
	 * Refresh the DataTable
	 */
	function refreshDataTable() {
		if (typeof table !== "undefined" && table.ajax) {
			console.log("Auto-refreshing finished goods data...");
			table.ajax.reload(null, false); // false = don't reset paging
		}
	}

	/**
	 * Start the auto-refresh timer
	 */
	function startAutoRefresh() {
		if (refreshTimer) {
			clearInterval(refreshTimer);
		}

		refreshTimer = setInterval(function () {
			if (isPageVisible) {
				refreshDataTable();
			}
		}, REFRESH_INTERVAL);

		console.log(
			"Auto-refresh started (every " + REFRESH_INTERVAL / 1000 + " seconds)"
		);
	}

	/**
	 * Stop the auto-refresh timer
	 */
	function stopAutoRefresh() {
		if (refreshTimer) {
			clearInterval(refreshTimer);
			refreshTimer = null;
			console.log("Auto-refresh stopped");
		}
	}

	/**
	 * Handle page visibility changes
	 */
	function handleVisibilityChange() {
		if (document.hidden) {
			isPageVisible = false;
			console.log("Page hidden - pausing auto-refresh");
		} else {
			isPageVisible = true;
			console.log("Page visible - resuming auto-refresh");

			// Refresh immediately when page becomes visible
			setTimeout(refreshDataTable, VISIBILITY_REFRESH_DELAY);
		}
	}

	/**
	 * Initialize auto-refresh when document is ready
	 */
	function initAutoRefresh() {
		// Wait for DataTable to be initialized
		const checkDataTable = setInterval(function () {
			if (typeof table !== "undefined" && table.ajax) {
				clearInterval(checkDataTable);
				startAutoRefresh();
			}
		}, 1000);

		// Stop checking after 10 seconds if DataTable is not found
		setTimeout(function () {
			clearInterval(checkDataTable);
		}, 10000);
	}

	// Event listeners
	document.addEventListener("visibilitychange", handleVisibilityChange);

	// Cleanup on page unload
	window.addEventListener("beforeunload", function () {
		stopAutoRefresh();
	});

	// Initialize when DOM is ready
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initAutoRefresh);
	} else {
		initAutoRefresh();
	}

	// Expose functions globally for manual control
	window.FinishedGoodsAutoRefresh = {
		start: startAutoRefresh,
		stop: stopAutoRefresh,
		refresh: refreshDataTable,
		isRunning: function () {
			return refreshTimer !== null;
		},
	};
})();
