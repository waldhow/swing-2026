/* Swing Golf & Lounge — progressiv förbättring. Sajten fungerar utan JS
   (TECH-01); skriptet sköter menyerna, förval av pott och formulärvalidering. */
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

    // Fokus till ett felaktigt fält och visa det med sin etikett under menyn.
    function showInvalid(el) {
      el.focus({ preventScroll: true });
      (el.closest(".field, fieldset") || el).scrollIntoView({ block: "start" });
    }

    // Validering i webbläsaren: samma regler och texter som servern (data-msg-*
    // skrivs av vyerna), felen visas vid fälten utan att sidan laddas om.
    // Servern validerar ändå varje inskick, och utan JS fungerar allt som förut.
    var iconTpl = document.getElementById("ferr-icon");
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function fieldBox(el) {
      return el.type === "radio" ? el.closest("fieldset") : el.closest(".field");
    }

    function errorText(el) {
      var msg = el.dataset;
      if (el.type === "radio") {
        var checked = el.form.querySelector('input[name="' + el.name + '"]:checked');
        return el.required && !checked ? fieldBox(el).dataset.msgRequired : "";
      }
      var value = el.type === "checkbox" ? (el.checked ? "1" : "") : el.value.trim();
      var bad = el.validity && el.validity.badInput;
      if (value === "" && !bad) return el.required ? msg.msgRequired : "";
      if (el.type === "email" && !emailRe.test(value)) return msg.msgEmail;
      if (el.type === "number") {
        var n = Number(value);
        if (bad || !Number.isInteger(n) || (el.min !== "" && n < Number(el.min)) || (el.max !== "" && n > Number(el.max))) return msg.msgRange;
      }
      if (msg.pattern && !new RegExp("^(?:" + msg.pattern + ")$").test(value)) return msg.msgPattern;
      if ("pnr" in msg) {
        // Svenskt personnummer med kontrollsiffra, som SwingRules::personnummer().
        var d = value.replace(/\D/g, "");
        if (d.length === 12) d = d.slice(2);
        if (d.length !== 10) return msg.msgPnrFormat;
        var sum = 0;
        for (var i = 0; i < 9; i++) {
          var x = Number(d[i]) * (i % 2 === 0 ? 2 : 1);
          sum += Math.floor(x / 10) + (x % 10);
        }
        if ((10 - (sum % 10)) % 10 !== Number(d[9])) return msg.msgPnrChecksum;
      }
      return "";
    }

    // Samma markering som servern ritar: .has-error, aria-invalid och
    // <p class="ferr" id="f-namn-err"> kopplad med aria-describedby.
    function setError(el, text) {
      var box = fieldBox(el);
      if (!box) return;
      var id = "f-" + el.name + "-err";
      var radio = el.type === "radio";
      var described = radio ? box : el;
      var err = document.getElementById(id);

      box.classList.toggle("has-error", !!text);
      Array.prototype.forEach.call(radio ? el.form.querySelectorAll('input[name="' + el.name + '"]') : [el], function (t) {
        if (text) t.setAttribute("aria-invalid", "true");
        else t.removeAttribute("aria-invalid");
      });
      var ids = (described.getAttribute("aria-describedby") || "").split(/\s+/).filter(function (x) { return x && x !== id; });
      if (text) ids.push(id);
      if (ids.length) described.setAttribute("aria-describedby", ids.join(" "));
      else described.removeAttribute("aria-describedby");

      if (!text) {
        if (err) err.remove();
        return;
      }
      if (!err) {
        err = document.createElement("p");
        err.className = "ferr";
        err.id = id;
        // Pottvalets fel står ovanför korten, övriga sist i fältet.
        var legend = box.classList.contains("pot-fieldset") && box.querySelector("legend");
        if (legend) legend.after(err);
        else box.appendChild(err);
      }
      err.innerHTML = iconTpl ? iconTpl.innerHTML : "";
      err.appendChild(document.createTextNode(text));
    }

    function validate(el) {
      var text = errorText(el) || "";
      setError(el, text);
      return text === "";
    }

    function hasError(el) {
      var box = fieldBox(el);
      return !!box && box.classList.contains("has-error");
    }

    Array.prototype.forEach.call(document.querySelectorAll("form[data-validate]"), function (form) {
      form.addEventListener("submit", function (e) {
        var first = null;
        var seen = {};
        Array.prototype.forEach.call(form.elements, function (el) {
          if (!el.name || el.name === "webbplats" || el.disabled || !/^(INPUT|SELECT|TEXTAREA)$/.test(el.tagName) || el.type === "hidden") return;
          if (el.type === "radio") {
            if (seen[el.name]) return;
            seen[el.name] = true;
          }
          if (!validate(el) && !first) first = el;
        });
        if (first) {
          e.preventDefault();
          showInvalid(first);
        }
      });

      // Ett fält med fel kontrolleras igen medan man rättar, så felet försvinner
      // direkt. Nya fel visas bara vid inskick: ett fel som dyker upp när man
      // lämnar fältet flyttar knappen under muspekaren, och klicket tappas.
      function recheck(e) {
        if (e.target.name && hasError(e.target)) validate(e.target);
      }
      form.addEventListener("input", recheck);
      form.addEventListener("change", recheck);
    });

    // Fel från servern (utan JS, eller fel som bara servern kan se): felen står
    // vid fälten. Fokus till första felaktiga fältet eller till formulärets
    // felruta när felet gäller hela formuläret. Webbläsaren återställer fokus
    // när den scrollar till #fragmentet efter laddning, så det görs igen efter load.
    var invalid = document.querySelector("[data-focus]") || document.querySelector('form [aria-invalid="true"]');
    if (invalid) {
      showInvalid(invalid);
      window.addEventListener("load", function () { setTimeout(function () { showInvalid(invalid); }, 0); });
    }
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
