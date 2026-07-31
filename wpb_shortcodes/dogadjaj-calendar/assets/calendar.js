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
