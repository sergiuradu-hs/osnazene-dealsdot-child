(function () {
  if (window.osnDogadjajCalendarInit) {
    return;
  }
  window.osnDogadjajCalendarInit = true;

  document.addEventListener(
    "click",
    async function (event) {
      var btn = event.target.closest(".osn-cal-widget .osn-cal__nav-btn");
      if (!btn) {
        return;
      }

      var widget = btn.closest(".osn-cal-widget");
      if (!widget) {
        return;
      }

      // Keep graceful fallback: if async update fails, navigate normally.
      event.preventDefault();

      if (widget.dataset.loading === "1") {
        return;
      }

      widget.dataset.loading = "1";
      widget.classList.add("is-loading");

      try {
        var currentWidgets = Array.prototype.slice.call(
          document.querySelectorAll(".osn-cal-widget")
        );
        var widgetIndex = currentWidgets.indexOf(widget);
        var targetUrl = new URL(btn.href, window.location.href);

        var response = await fetch(targetUrl.toString(), {
          method: "GET",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        });

        if (!response.ok) {
          throw new Error("Calendar fetch failed");
        }

        var html = await response.text();
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, "text/html");
        var fetchedWidgets = doc.querySelectorAll(".osn-cal-widget");
        var replacement = fetchedWidgets[widgetIndex] || fetchedWidgets[0];

        if (!replacement) {
          throw new Error("Calendar markup not found in response");
        }

        widget.replaceWith(replacement);

        // Keep URL in sync with currently visible month.
        window.history.replaceState({}, "", targetUrl.toString());
      } catch (err) {
        window.location.href = btn.href;
      }
    },
    true
  );

  // Cache self-heal: if the page came from a full-page cache generated in a
  // previous month, the server-rendered default month is stale. When no
  // explicit cal_year/cal_month params are present, compare the rendered
  // month against the device's current month and re-fetch if they differ
  // (the query-string URL bypasses page caches).
  function refreshStaleCalendars() {
    var params = new URLSearchParams(window.location.search);
    if (params.has("cal_year") || params.has("cal_month")) {
      return;
    }

    var now = new Date();
    var currentYear = now.getFullYear();
    var currentMonth = now.getMonth() + 1;

    var widgets = Array.prototype.slice.call(
      document.querySelectorAll(".osn-cal-widget[data-osn-year]")
    );
    var stale = widgets.filter(function (widget) {
      return (
        parseInt(widget.dataset.osnYear, 10) !== currentYear ||
        parseInt(widget.dataset.osnMonth, 10) !== currentMonth
      );
    });

    if (!stale.length) {
      return;
    }

    var url = new URL(window.location.href);
    url.searchParams.set("cal_year", String(currentYear));
    url.searchParams.set("cal_month", String(currentMonth));

    fetch(url.toString(), {
      method: "GET",
      credentials: "same-origin",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      }
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Calendar refresh failed");
        }
        return response.text();
      })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, "text/html");
        var fetched = doc.querySelectorAll(".osn-cal-widget");
        var all = Array.prototype.slice.call(
          document.querySelectorAll(".osn-cal-widget")
        );
        stale.forEach(function (widget) {
          var replacement = fetched[all.indexOf(widget)] || fetched[0];
          if (replacement) {
            widget.replaceWith(replacement);
          }
        });
      })
      .catch(function () {
        // Leave the server-rendered month in place; nav links still work.
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", refreshStaleCalendars);
  } else {
    refreshStaleCalendars();
  }

  document.addEventListener(
    "click",
    function (event) {
      const cell = event.target.closest(
        ".osn-cal-widget .osn-cal__cell--has-event"
      );
      if (!cell || !window.matchMedia("(max-width: 767px)").matches) {
        return;
      }

      const widget = cell.closest(".osn-cal-widget");
      const targetId = cell.dataset.osnTarget;
      if (!widget || !targetId) {
        return;
      }

      const target = document.getElementById(targetId);
      if (!target || !widget.contains(target)) {
        return;
      }

      event.preventDefault();
      target.scrollIntoView({
        behavior: "smooth",
        block: "center"
      });
    },
    true
  );
})();
