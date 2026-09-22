/* KMS Candidate Survey — name search, expand/collapse, sticky offset. */
(function () {
	"use strict";

	function init(root) {
		var search = root.querySelector('[data-kcs="search"]');
		var items = Array.prototype.slice.call(root.querySelectorAll('[data-kcs="item"]'));
		var countEl = root.querySelector('[data-kcs="count"]');
		var emptyEl = root.querySelector('[data-kcs="empty"]');
		var emptyQuery = root.querySelector('[data-kcs="empty-query"]');
		var expandBtn = root.querySelector('[data-kcs="expand"]');
		var collapseBtn = root.querySelector('[data-kcs="collapse"]');

		if (!items.length) {
			return;
		}

		function updateCount(visible) {
			if (countEl) {
				countEl.textContent = visible + " of " + items.length + " shown";
			}
		}

		function filter() {
			var term = search ? search.value.trim().toLowerCase() : "";
			var visible = 0;
			items.forEach(function (li) {
				var match = !term || (li.getAttribute("data-search") || "").indexOf(term) !== -1;
				li.hidden = !match;
				if (match) {
					visible++;
				}
			});
			updateCount(visible);
			if (emptyEl) {
				emptyEl.hidden = visible !== 0;
			}
			if (emptyQuery && search) {
				emptyQuery.textContent = search.value.trim();
			}
		}

		if (search) {
			search.addEventListener("input", filter);
			search.addEventListener("search", filter);
		}

		if (expandBtn) {
			expandBtn.addEventListener("click", function () {
				items.forEach(function (li) {
					var d = li.querySelector("details");
					if (d && !li.hidden) {
						d.open = true;
					}
				});
			});
		}

		if (collapseBtn) {
			collapseBtn.addEventListener("click", function () {
				items.forEach(function (li) {
					var d = li.querySelector("details");
					if (d) {
						d.open = false;
					}
				});
			});
		}

		// Deep link: #kcs-…-c123 opens that candidate.
		if (location.hash) {
			var target = root.querySelector('details' + CSS.escape(location.hash));
			if (target) {
				target.open = true;
			}
		}

		updateCount(items.length);
	}

	// Keep the sticky toolbar clear of a fixed theme header (Divi's #main-header).
	function syncStickyOffset(roots) {
		var header = document.getElementById("main-header");
		var offset = 0;
		if (header) {
			var pos = window.getComputedStyle(header).position;
			if (pos === "fixed" || pos === "sticky") {
				offset = Math.round(header.getBoundingClientRect().height);
			}
		}
		roots.forEach(function (root) {
			root.style.setProperty("--kcs-sticky-top", offset + "px");
		});
	}

	function boot() {
		var roots = Array.prototype.slice.call(document.querySelectorAll(".kcs"));
		if (!roots.length) {
			return;
		}
		roots.forEach(init);
		syncStickyOffset(roots);
		window.addEventListener("resize", function () { syncStickyOffset(roots); });
		window.addEventListener("scroll", function () { syncStickyOffset(roots); }, { passive: true });
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", boot);
	} else {
		boot();
	}
})();
