/* global OsnClanice */
(function () {
  "use strict";

  if (window.osnClaniceGridInit) return;
  window.osnClaniceGridInit = true;

  // ----------------------------------------------------------------
  // State per widget instance
  // ----------------------------------------------------------------
  function WidgetState(root) {
    this.root       = root;
    this.instanceId = root.id;
    this.perPage    = parseInt(root.dataset.perPage, 10) || 12;
    this.page       = 1;
    this.ime        = "";
    this.drzava     = 0;
    this.delatnost  = 0;
    this.mesto      = "";
    this.mestoText  = "";
    this.loading    = false;
    this.debounceTimer = null;
    this.view       = "grid"; // 'grid' | 'list'
  }

  // ----------------------------------------------------------------
  // AJAX fetch
  // ----------------------------------------------------------------
  WidgetState.prototype.fetch = function (newPage) {
    if (this.loading) return;

    var self     = this;
    var page     = newPage || this.page;
    var results  = this.root.querySelector(".osn-clanice-grid__results");

    this.loading = true;
    this.root.classList.add("is-loading");

    var body = new FormData();
    body.append("action",   "osn_clanice_grid_filter");
    body.append("nonce",    OsnClanice.nonce);
    body.append("ime",      this.ime);
    body.append("drzava",   this.drzava);
    body.append("delatnost",this.delatnost);
    body.append("mesto",    this.mesto);
    body.append("paged",    page);
    body.append("per_page", this.perPage);

    fetch(OsnClanice.ajaxurl, {
      method:      "POST",
      credentials: "same-origin",
      body:        body
    })
      .then(function (response) {
        if (!response.ok) throw new Error("Network error");
        return response.json();
      })
      .then(function (data) {
        if (!data.success) throw new Error("Server error");
        self.page = data.data.page;
        if (results) {
          results.innerHTML = data.data.html;
          // Scroll results into view on mobile (if not in viewport)
          if (window.innerWidth < 768) {
            var top = results.getBoundingClientRect().top + window.scrollY - 16;
            window.scrollTo({ top: top, behavior: "smooth" });
          }
        }
        // Update mobile count display
        var countMob = self.root.querySelector(".osn-clanice-grid__result-count-mob");
        if (countMob) {
          var countEl = results && results.querySelector(".osn-clanice-grid__count");
          countMob.textContent = countEl ? countEl.textContent : "";
        }
        self.bindPagination();
      })
      .catch(function (err) {
        console.error("Clanice grid fetch failed:", err);
      })
      .finally(function () {
        self.loading = false;
        self.root.classList.remove("is-loading");
      });
  };

  // ----------------------------------------------------------------
  // Read filter values from a form element
  // ----------------------------------------------------------------
  WidgetState.prototype.readForm = function (form) {
    var imeInput    = form.querySelector("[name=ime]");
    var drzavaInput = form.querySelector("[name=drzava]");
    var delInput    = form.querySelector("[name=delatnost]");
    var mestoInput  = form.querySelector("[name=mesto]");
    var mestoSearch = form.querySelector("[name=mesto_search]");
    if (imeInput)    this.ime        = imeInput.value.trim();
    if (drzavaInput) this.drzava     = parseInt(drzavaInput.value, 10) || 0;
    if (delInput)    this.delatnost  = parseInt(delInput.value,    10) || 0;
    if (mestoInput)  this.mesto      = mestoInput.value.trim();
    if (mestoSearch) this.mestoText  = mestoSearch.value.trim();
  };

  // ----------------------------------------------------------------
  // Sync form values (used to pre-populate modal from sidebar state)
  // ----------------------------------------------------------------
  WidgetState.prototype.syncForm = function (form) {
    var imeInput    = form.querySelector("[name=ime]");
    var drzavaInput = form.querySelector("[name=drzava]");
    var delInput    = form.querySelector("[name=delatnost]");
    var mestoInput  = form.querySelector("[name=mesto]");
    var mestoSearch = form.querySelector("[name=mesto_search]");
    if (imeInput)    imeInput.value    = this.ime;
    if (drzavaInput) drzavaInput.value = this.drzava  || "";
    if (delInput)    delInput.value    = this.delatnost || "";
    if (mestoInput)  mestoInput.value  = this.mesto;
    if (mestoSearch) mestoSearch.value = this.mestoText;
  };

  // ----------------------------------------------------------------
  // Reset filters
  // ----------------------------------------------------------------
  WidgetState.prototype.reset = function () {
    this.ime        = "";
    this.drzava     = 0;
    this.delatnost  = 0;
    this.mesto      = "";
    this.mestoText  = "";
    this.page       = 1;
    // Clear all forms
    var forms = this.root.querySelectorAll(".osn-clanice-grid__filter-form");
    forms.forEach(function (f) {
      f.reset();
    });
    this.fetch(1);
  };

  // ----------------------------------------------------------------
  // City suggestions
  // ----------------------------------------------------------------
  WidgetState.prototype.cityOptions = function () {
    var selectedCountry = this.drzava;
    var cities = (window.OsnClanice && Array.isArray(OsnClanice.cities)) ? OsnClanice.cities : [];

    if (!selectedCountry) return cities;

    return cities.filter(function (city) {
      return Array.isArray(city.country_ids) && city.country_ids.indexOf(selectedCountry) !== -1;
    });
  };

  WidgetState.prototype.findCity = function (value) {
    var cities = (window.OsnClanice && Array.isArray(OsnClanice.cities)) ? OsnClanice.cities : [];
    for (var i = 0; i < cities.length; i += 1) {
      if (cities[i].value === value) return cities[i];
    }
    return null;
  };

  WidgetState.prototype.cityAllowed = function (value) {
    var city = this.findCity(value);
    if (!city) return false;
    if (!this.drzava) return true;
    return Array.isArray(city.country_ids) && city.country_ids.indexOf(this.drzava) !== -1;
  };

  WidgetState.prototype.clearCityForm = function (form) {
    var mestoInput  = form && form.querySelector("[name=mesto]");
    var mestoSearch = form && form.querySelector("[name=mesto_search]");
    if (mestoInput) mestoInput.value = "";
    if (mestoSearch) mestoSearch.value = "";
    this.mesto = "";
    this.mestoText = "";
  };

  WidgetState.prototype.hideCitySuggestions = function (form) {
    var list = form && form.querySelector(".osn-clanice-grid__suggestions--mesto");
    if (!list) return;
    list.hidden = true;
    list.innerHTML = "";
  };

  WidgetState.prototype.renderCitySuggestions = function (form, input, immediate) {
    var self = this;
    var list = form && form.querySelector(".osn-clanice-grid__suggestions--mesto");
    if (!list || !input) return;

    var query = input.value.trim().toLowerCase();
    var matches = this.cityOptions().filter(function (city) {
      return !query || city.name.toLowerCase().indexOf(query) !== -1;
    }).slice(0, 12);

    list.innerHTML = "";
    if (!matches.length) {
      list.hidden = true;
      return;
    }

    matches.forEach(function (city) {
      var button = document.createElement("button");
      button.type = "button";
      button.className = "osn-clanice-grid__suggestion";
      button.textContent = city.name;
      button.addEventListener("mousedown", function (event) {
        event.preventDefault();
      });
      button.addEventListener("click", function () {
        var hidden = form.querySelector("[name=mesto]");
        if (hidden) hidden.value = city.value;
        input.value = city.name;
        self.mesto = city.value;
        self.mestoText = city.name;
        self.hideCitySuggestions(form);

        if (immediate) {
          self.page = 1;
          self.fetch(1);
        }
      });
      list.appendChild(button);
    });

    list.hidden = false;
  };

  // ----------------------------------------------------------------
  // Bind pagination buttons (re-run after each AJAX response)
  // ----------------------------------------------------------------
  WidgetState.prototype.bindPagination = function () {
    var self = this;
    var pagination = this.root.querySelector(".osn-clanice-grid__pagination");
    if (!pagination) return;
    pagination.querySelectorAll(".osn-clanice-pag__btn:not([disabled])").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var p = parseInt(btn.dataset.page, 10);
        if (p && !btn.disabled) {
          self.fetch(p);
        }
      });
    });
  };

  // ----------------------------------------------------------------
  // View toggle (grid / list)
  // ----------------------------------------------------------------
  WidgetState.prototype.setView = function (view) {
    this.view = view;
    var cardsWrap = this.root.querySelector(".osn-clanice-grid__cards-wrap");
    if (cardsWrap) {
      cardsWrap.classList.toggle("osn-clanice-grid__cards-wrap--list", view === "list");
    }
    this.root.querySelectorAll(".osn-clanice-grid__view-btn").forEach(function (btn) {
      var active = btn.dataset.view === view;
      btn.classList.toggle("is-active", active);
      btn.setAttribute("aria-pressed", active ? "true" : "false");
    });
  };

  // ----------------------------------------------------------------
  // Modal open / close
  // ----------------------------------------------------------------
  WidgetState.prototype.openModal = function () {
    var modal    = this.root.querySelector(".osn-clanice-grid__modal");
    var toggle   = this.root.querySelector(".osn-clanice-grid__filter-toggle");
    var modalForm = modal && modal.querySelector(".osn-clanice-grid__filter-form--modal");
    if (!modal) return;
    if (modalForm) this.syncForm(modalForm);
    modal.removeAttribute("hidden");
    document.body.style.overflow = "hidden";
    if (toggle) toggle.setAttribute("aria-expanded", "true");
  };

  WidgetState.prototype.closeModal = function () {
    var modal  = this.root.querySelector(".osn-clanice-grid__modal");
    var toggle = this.root.querySelector(".osn-clanice-grid__filter-toggle");
    if (!modal) return;
    modal.setAttribute("hidden", "");
    document.body.style.overflow = "";
    if (toggle) toggle.setAttribute("aria-expanded", "false");
  };

  // ----------------------------------------------------------------
  // Initialise a single widget root element
  // ----------------------------------------------------------------
  function initWidget(root) {
    var state = new WidgetState(root);

    function bindCitySearch(form, immediate) {
      var input = form.querySelector("[name=mesto_search]");
      var hidden = form.querySelector("[name=mesto]");
      if (!input || !hidden) return;

      input.addEventListener("input", function () {
        var hadSelection = hidden.value !== "";
        hidden.value = "";
        state.mesto = "";
        state.mestoText = input.value.trim();
        state.renderCitySuggestions(form, input, immediate);

        if (immediate && hadSelection && input.value.trim() === "") {
          clearTimeout(state.debounceTimer);
          state.debounceTimer = setTimeout(function () {
            state.page = 1;
            state.fetch(1);
          }, 400);
        }
      });

      input.addEventListener("focus", function () {
        state.readForm(form);
        state.renderCitySuggestions(form, input, immediate);
      });

      input.addEventListener("blur", function () {
        setTimeout(function () {
          state.hideCitySuggestions(form);
        }, 150);
      });

      input.addEventListener("keydown", function (event) {
        var list = form.querySelector(".osn-clanice-grid__suggestions--mesto");
        var first = list && list.querySelector(".osn-clanice-grid__suggestion");
        if (event.key === "Enter" && first && !list.hidden) {
          event.preventDefault();
          first.click();
        }
        if (event.key === "Escape") {
          state.hideCitySuggestions(form);
        }
      });
    }

    // --- Desktop sidebar filters (immediate for selects, debounced for text)
    root.querySelectorAll(".osn-clanice-grid__sidebar .osn-clanice-grid__filter-form").forEach(function (form) {
      // Text input — debounced
      form.querySelectorAll("input[name=ime]").forEach(function (input) {
        input.addEventListener("input", function () {
          clearTimeout(state.debounceTimer);
          state.debounceTimer = setTimeout(function () {
            state.readForm(form);
            state.page = 1;
            state.fetch(1);
          }, 400);
        });
      });

      // Selects — immediate
      form.querySelectorAll("select").forEach(function (sel) {
        sel.addEventListener("change", function () {
          state.readForm(form);
          if (state.mesto && !state.cityAllowed(state.mesto)) {
            state.clearCityForm(form);
          }
          state.page = 1;
          state.fetch(1);
        });
      });

      bindCitySearch(form, true);

      // Prevent accidental form submission
      form.addEventListener("submit", function (e) { e.preventDefault(); });
    });

    // --- Mobile search input (outside sidebar, debounced)
    root.querySelectorAll(".osn-clanice-grid__mobile-search .osn-clanice-grid__input--ime").forEach(function (input) {
      input.addEventListener("input", function () {
        clearTimeout(state.debounceTimer);
        state.debounceTimer = setTimeout(function () {
          state.ime = input.value.trim();
          state.page = 1;
          state.fetch(1);
        }, 400);
      });
    });

    // --- Filter toggle button (mobile)
    var toggle = root.querySelector(".osn-clanice-grid__filter-toggle");
    if (toggle) {
      toggle.addEventListener("click", function () {
        state.openModal();
      });
    }

    // --- Modal backdrop click → close
    var backdrop = root.querySelector(".osn-clanice-grid__modal-backdrop");
    if (backdrop) {
      backdrop.addEventListener("click", function () {
        state.closeModal();
      });
    }

    // --- Modal form submit (Primeni filtere)
    var modalForm = root.querySelector(".osn-clanice-grid__filter-form--modal");
    if (modalForm) {
      bindCitySearch(modalForm, false);

      modalForm.addEventListener("submit", function (e) {
        e.preventDefault();
        state.readForm(modalForm);
        if (state.mesto && !state.cityAllowed(state.mesto)) {
          state.clearCityForm(modalForm);
        }
        state.page = 1;
        state.closeModal();
        state.fetch(1);
      });

      // Reset button in modal
      var resetBtn = modalForm.querySelector(".osn-clanice-grid__modal-reset");
      if (resetBtn) {
        resetBtn.addEventListener("click", function () {
          state.reset();
          state.closeModal();
        });
      }
    }

    // --- View toggle buttons
    root.querySelectorAll(".osn-clanice-grid__view-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        state.setView(btn.dataset.view);
      });
    });

    // --- Escape key closes modal
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        state.closeModal();
      }
    });

    // --- Initial pagination binding (server-rendered first page)
    state.bindPagination();
  }

  // ----------------------------------------------------------------
  // Boot on DOMContentLoaded
  // ----------------------------------------------------------------
  function boot() {
    document.querySelectorAll(".osn-clanice-grid").forEach(initWidget);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
