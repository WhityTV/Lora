(function () {
  const root = document.documentElement;
  const toggleId = "theme-toggle-checkbox";
  const storageKey = "mihiway-theme";

  function getCookie(name) {
    const prefix = name + "=";
    const parts = document.cookie ? document.cookie.split(";") : [];

    for (const part of parts) {
      const trimmed = part.trim();
      if (trimmed.indexOf(prefix) === 0) {
        return decodeURIComponent(trimmed.slice(prefix.length));
      }
    }

    return null;
  }

  function setCookie(name, value) {
    const maxAge = 60 * 60 * 24 * 365;
    document.cookie = name + "=" + encodeURIComponent(value) + "; path=/; max-age=" + maxAge + "; samesite=lax";
  }

  function resolveInitialTheme() {
    let savedTheme = null;
    try {
      savedTheme = localStorage.getItem(storageKey);
    } catch (error) {
      savedTheme = null;
    }

    if (savedTheme === "light" || savedTheme === "dark") {
      return savedTheme;
    }

    const cookieTheme = getCookie(storageKey);
    if (cookieTheme === "light" || cookieTheme === "dark") {
      return cookieTheme;
    }

    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  }

  function applyTheme(theme, options = {}) {
    const silent = options.silent === true;
    const mode = theme === "dark" ? "dark" : "light";

    if (silent) {
      root.classList.add("theme-toggle-initializing");
    }

    root.style.colorScheme = mode;
    root.setAttribute("data-theme", mode);

    const toggle = document.getElementById(toggleId);
    if (toggle) {
      toggle.checked = mode === "light";
    }

    if (silent) {
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          root.classList.remove("theme-toggle-initializing");
        });
      });
    }
  }

  let activeTheme = resolveInitialTheme();

  function bindToggle() {
    const toggle = document.getElementById(toggleId);
    if (!toggle || toggle.dataset.themeBound === "1") {
      return;
    }

    toggle.dataset.themeBound = "1";
    toggle.addEventListener("change", function () {
      activeTheme = toggle.checked ? "light" : "dark";
      applyTheme(activeTheme);
      try {
        localStorage.setItem(storageKey, activeTheme);
      } catch (error) {
      }

      setCookie(storageKey, activeTheme);
    });
  }

  function bindLanguageSwitchDelay() {
    const forms = document.querySelectorAll(".language_buttons form");
    if (!forms || forms.length === 0) {
      return;
    }

    forms.forEach(function (form) {
      if (form.dataset.langDelayBound === "1") {
        return;
      }

      form.dataset.langDelayBound = "1";

      const buttons = form.querySelectorAll('button[name="syslang"]');
      buttons.forEach(function (button) {
        button.addEventListener("click", function (event) {
          if (form.dataset.langPending === "1") {
            event.preventDefault();
            return;
          }

          event.preventDefault();
          form.dataset.langPending = "1";
          button.classList.add("language-switch-pending");

          window.setTimeout(function () {
            if (typeof form.requestSubmit === "function") {
              form.requestSubmit(button);
              return;
            }

            if (button.name) {
              const hidden = document.createElement("input");
              hidden.type = "hidden";
              hidden.name = button.name;
              hidden.value = button.value;
              form.appendChild(hidden);
            }

            form.submit();
          }, 320);
        });
      });
    });
  }

  function bindHeaderInteractions() {
    if (root.dataset.headerUiBound === "1") {
      return;
    }

    root.dataset.headerUiBound = "1";

    function closeAllArtPanels() {
      document.querySelectorAll(".art_cat").forEach(function (panel) {
        panel.style.display = "none";
      });
    }

    function closeAllAccountMenus() {
      document.querySelectorAll(".my_acc_menu.show").forEach(function (menu) {
        menu.classList.remove("show");
      });
    }

    document.addEventListener("click", function (event) {
      const artTrigger = event.target.closest(".art_cat_trigger");
      if (artTrigger) {
        const wrapper = artTrigger.closest(".art_cat_wrapper");
        const panel = wrapper ? wrapper.querySelector(".art_cat") : null;
        const shouldOpen = panel && panel.style.display !== "block";

        closeAllArtPanels();
        if (panel && shouldOpen) {
          panel.style.display = "block";
        }
        return;
      }

      if (!event.target.closest(".art_cat")) {
        closeAllArtPanels();
      }

      const accountTrigger = event.target.closest(".my_acc");
      if (accountTrigger) {
        const wrapper = accountTrigger.closest(".my_acc_wrapper");
        const menu = wrapper ? wrapper.querySelector(".my_acc_menu") : null;
        const shouldOpen = menu && !menu.classList.contains("show");

        closeAllAccountMenus();
        if (menu && shouldOpen) {
          menu.classList.add("show");
        }
        return;
      }

      if (!event.target.closest(".my_acc_menu")) {
        closeAllAccountMenus();
      }
    });
  }

  applyTheme(activeTheme, { silent: true });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      bindToggle();
      bindLanguageSwitchDelay();
      bindHeaderInteractions();
      applyTheme(activeTheme, { silent: true });
    }, { once: true });
  } else {
    bindToggle();
    bindLanguageSwitchDelay();
    bindHeaderInteractions();
    applyTheme(activeTheme, { silent: true });
  }
})();
