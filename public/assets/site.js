/* Swing Golf & Lounge — progressiv förbättring. Sajten fungerar utan JS
   (TECH-01); skriptet sköter bara mobilmenyn och fokus vid formulärfel. */
(function () {
  function boot() {
    var burger = document.querySelector(".burger");
    var menu = document.getElementById("huvudmeny");

    if (burger && menu) {
      function setOpen(open) {
        menu.classList.toggle("open", open);
        burger.setAttribute("aria-expanded", open ? "true" : "false");
      }

      burger.addEventListener("click", function () {
        var open = burger.getAttribute("aria-expanded") !== "true";
        setOpen(open);
        if (open) {
          var first = menu.querySelector("a");
          if (first) first.focus();
        }
      });

      // Esc stänger menyn och lämnar tillbaka fokus till knappen (AI-spec 5).
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && burger.getAttribute("aria-expanded") === "true") {
          setOpen(false);
          burger.focus();
        }
      });

      // Stäng när fokus eller klick hamnar utanför menyn.
      document.addEventListener("click", function (e) {
        if (burger.getAttribute("aria-expanded") === "true" && !menu.contains(e.target) && !burger.contains(e.target)) {
          setOpen(false);
        }
      });
      menu.addEventListener("focusout", function (e) {
        if (burger.getAttribute("aria-expanded") === "true" && e.relatedTarget && !menu.contains(e.relatedTarget) && e.relatedTarget !== burger) {
          setOpen(false);
        }
      });

      window.addEventListener("resize", function () {
        if (window.innerWidth > 1000) setOpen(false);
      });
    }

    // Undermenyer: pilknappen fäller ut/in. Esc eller klick utanför stänger.
    var groups = Array.prototype.slice.call(document.querySelectorAll(".nav-group"));
    function closeGroup(group) {
      group.classList.remove("open");
      group.querySelector(".nav-toggle").setAttribute("aria-expanded", "false");
    }
    groups.forEach(function (group) {
      var toggle = group.querySelector(".nav-toggle");
      toggle.addEventListener("click", function (e) {
        e.stopPropagation();
        var open = !group.classList.contains("open");
        groups.forEach(closeGroup);
        if (open) {
          group.classList.add("open");
          toggle.setAttribute("aria-expanded", "true");
        }
      });
      group.addEventListener("focusout", function (e) {
        if (e.relatedTarget && !group.contains(e.relatedTarget) && window.innerWidth > 1000) closeGroup(group);
      });
    });
    document.addEventListener("keydown", function (e) {
      if (e.key !== "Escape") return;
      groups.forEach(function (group) {
        if (group.classList.contains("open")) {
          closeGroup(group);
          group.querySelector(".nav-toggle").focus();
          e.stopImmediatePropagation();
        }
      });
    }, true);
    document.addEventListener("click", function (e) {
      groups.forEach(function (group) { if (!group.contains(e.target)) closeGroup(group); });
    });

    // Förval av pott från länk (t.ex. /spelpotter/#pott-2000 från startsidan):
    // välj kortet, fokusera det och visa avsnittets rubrik och ingress.
    var potMatch = window.location.hash.match(/^#((?:pott|kampanj)-\d+)$/);
    var potRadio = potMatch && document.querySelector('.pot-radio[value="' + potMatch[1] + '"]');
    if (potRadio) {
      potRadio.checked = true;
      var showPot = function () {
        var head = document.getElementById("kop");
        if (head) head.scrollIntoView({ block: "start" });
        potRadio.focus({ preventScroll: true });
      };
      showPot();
      window.addEventListener("load", function () { setTimeout(showPot, 0); });
    }

    // Formulär med fel: flytta fokus till felsammanfattningen. Webbläsaren
    // återställer fokus när den scrollar till #fragmentet efter laddning,
    // så fokus sätts igen efter load.
    var alert = document.querySelector("[data-focus]");
    if (alert) {
      alert.focus();
      window.addEventListener("load", function () { setTimeout(function () { alert.focus(); }, 0); });
    }
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
