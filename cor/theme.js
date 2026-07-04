(function () {
  const root = document.documentElement;
  const toggleId = "theme-toggle-checkbox";
  const storageKey = "mihiway-theme";

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

    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  }

  function applyTheme(theme) {
    const mode = theme === "dark" ? "dark" : "light";
    root.style.colorScheme = mode;
    root.setAttribute("data-theme", mode);

    const toggle = document.getElementById(toggleId);
    if (toggle) {
      toggle.checked = mode === "light";
    }
  }

  function bindToggle() {
    const toggle = document.getElementById(toggleId);
    if (!toggle || toggle.dataset.themeBound === "1") {
      return;
    }

    toggle.dataset.themeBound = "1";
    toggle.addEventListener("change", function () {
      const nextTheme = toggle.checked ? "light" : "dark";
      applyTheme(nextTheme);
      try {
        localStorage.setItem(storageKey, nextTheme);
      } catch (error) {
      }
    });
  }

  applyTheme(resolveInitialTheme());

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bindToggle, { once: true });
  } else {
    bindToggle();
  }
})();
