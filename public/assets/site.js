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
    // Skickas formuläret innan sidan laddat klart kan typsnitt och bilder
    // flytta innehållet efteråt – då skrollas fältet fram igen efter load.
    function showInvalid(el) {
      var box = el.closest(".field, fieldset") || el;
      el.focus({ preventScroll: true });
      box.scrollIntoView({ block: "start" });
      if (document.readyState !== "complete") {
        window.addEventListener("load", function () { box.scrollIntoView({ block: "start" }); }, { once: true });
      }
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

    // Skickat formulär: spinner och ny text i knappen (data-busy-label) tills
    // nästa sida laddats. Ett andra klick skickar inte igen – inga dubbla
    // beställningar. Knappen behåller bredden så att inget hoppar.
    function setBusy(form, busy) {
      var btn = form.querySelector('button[type="submit"]');
      form.dataset.busy = busy ? "1" : "";
      form.setAttribute("aria-busy", busy ? "true" : "false");
      if (!btn) return;
      if (busy) {
        btn.dataset.idle = btn.innerHTML;
        btn.style.minWidth = btn.offsetWidth + "px";
        btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>';
        btn.appendChild(document.createTextNode(btn.dataset.busyLabel || "Skickar…"));
      } else if (btn.dataset.idle) {
        btn.innerHTML = btn.dataset.idle;
        btn.style.minWidth = "";
      }
      btn.classList.toggle("is-busy", busy);
      btn.setAttribute("aria-disabled", busy ? "true" : "false");
    }

    // Tillbaka med bakåtknappen (sidan från webbläsarens cache): knappen ska gå att använda igen.
    window.addEventListener("pageshow", function (e) {
      if (!e.persisted) return;
      Array.prototype.forEach.call(document.querySelectorAll('form[data-busy="1"]'), function (form) { setBusy(form, false); });
    });

    Array.prototype.forEach.call(document.querySelectorAll("form[data-validate]"), function (form) {
      form.addEventListener("submit", function (e) {
        if (form.dataset.busy === "1") {
          e.preventDefault();
          return;
        }
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
          return;
        }
        setBusy(form, true);
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

    // Samtycke för statistik (Google Analytics, TECH-03). Inget laddas förrän
    // besökaren sagt ja. Valet sparas i en nödvändig cookie och kan ändras via
    // "Cookie-inställningar" i sidfoten; nej raderar Analytics-cookies.
    var consent = document.getElementById("consent");
    if (consent) {
      var gaId = consent.dataset.ga;
      var days = Number(consent.dataset.days) || 365;
      var readChoice = function () {
        var m = document.cookie.match(/(?:^|;\s*)swing_consent=(granted|denied)/);
        return m ? m[1] : null;
      };
      var saveChoice = function (value) {
        document.cookie = "swing_consent=" + value + "; Max-Age=" + days * 86400 + "; Path=/; SameSite=Lax" + (location.protocol === "https:" ? "; Secure" : "");
      };
      var loadAnalytics = function () {
        if (window.__swingAnalytics) return;
        window.__swingAnalytics = true;
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag("consent", "default", { analytics_storage: "granted", ad_storage: "denied", ad_user_data: "denied", ad_personalization: "denied" });
        window.gtag("js", new Date());
        window.gtag("config", gaId, { allow_google_signals: false, allow_ad_personalization_signals: false });
        var s = document.createElement("script");
        s.async = true;
        s.src = "https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(gaId);
        document.head.appendChild(s);
      };
      var removeAnalyticsCookies = function () {
        var root = location.hostname.replace(/^www\./, "");
        document.cookie.split(";").forEach(function (c) {
          var name = c.split("=")[0].trim();
          if (!/^_ga($|_)|^_gid$/.test(name)) return;
          ["", "; Domain=" + location.hostname, "; Domain=." + root].forEach(function (domain) {
            document.cookie = name + "=; Max-Age=0; Path=/" + domain;
          });
        });
      };
      var showConsent = function (open) { consent.hidden = !open; };

      Array.prototype.forEach.call(consent.querySelectorAll("[data-consent]"), function (button) {
        button.addEventListener("click", function () {
          var before = readChoice();
          var value = button.dataset.consent;
          saveChoice(value);
          showConsent(false);
          if (value === "granted") {
            loadAnalytics();
          } else {
            removeAnalyticsCookies();
            // Redan laddad Analytics stoppas genom att sidan laddas om.
            if (before === "granted" && window.__swingAnalytics) location.reload();
          }
        });
      });
      Array.prototype.forEach.call(document.querySelectorAll("[data-consent-open]"), function (button) {
        button.addEventListener("click", function () {
          showConsent(true);
          consent.querySelector("[data-consent]").focus();
        });
      });

      var choice = readChoice();
      if (choice === "granted") loadAnalytics();
      else if (choice === null) showConsent(true);
    }

    // TrackMan-guiden: nivåval, innehållsförteckning och Smash Factor-kalkylator.
    // Allt innehåll finns i HTML:en – utan JS syns guiden som en vanlig sida.
    var guide = document.getElementById("tm-guide");
    if (guide) {
      Array.prototype.forEach.call(guide.querySelectorAll("[data-js-only]"), function (el) { el.hidden = false; });

      var levelNames = {};
      Array.prototype.forEach.call(guide.querySelectorAll("[data-level-pick]"), function (b) { levelNames[b.dataset.levelPick] = b.textContent; });
      var setLevel = function (level, save) {
        if (!levelNames[level]) level = "all";
        guide.dataset.level = level;
        Array.prototype.forEach.call(guide.querySelectorAll("[data-level-pick]"), function (b) {
          b.setAttribute("aria-pressed", b.dataset.levelPick === level ? "true" : "false");
        });
        Array.prototype.forEach.call(guide.querySelectorAll("[data-level-label]"), function (el) { el.textContent = levelNames[level]; });
        if (save) { try { localStorage.setItem("swing-tm-level", level); } catch (err) { /* ingen lagring */ } }
        onGuideScroll();
      };
      guide.addEventListener("click", function (e) {
        var pick = e.target.closest("[data-level-pick]");
        if (pick) setLevel(pick.dataset.levelPick, true);
        if (e.target.closest("[data-level-reset]")) setLevel("all", true);
      });

      // Innehållsförteckningen öppnas som modal dialog (fokusfälla och Esc på köpet).
      var toc = document.getElementById("tmg-toc");
      var tocOpener = guide.querySelector("[data-toc-open]");
      if (toc && typeof toc.showModal === "function") {
        tocOpener.addEventListener("click", function () { toc.showModal(); });
        toc.querySelector("[data-toc-close]").addEventListener("click", function () { toc.close(); });
        toc.addEventListener("click", function (e) {
          if (e.target.closest("[data-toc-link]")) toc.close();
        });
        toc.addEventListener("close", function () { tocOpener.focus({ preventScroll: true }); });
      } else if (tocOpener) {
        tocOpener.closest(".tmg-bar").hidden = true;
      }

      // Aktivt avsnitt i innehållsraden och förlopp genom guiden.
      var sections = guide.querySelectorAll("[data-sec]");
      var nowNum = guide.querySelector("[data-active-num]");
      var nowTitle = guide.querySelector("[data-active-title]");
      var progress = guide.querySelector("[data-progress]");
      var tocLinks = guide.querySelectorAll("[data-toc-link]");
      var ticking = false;
      function onGuideScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
          ticking = false;
          var active = null;
          Array.prototype.forEach.call(sections, function (s) {
            if (s.offsetParent !== null && s.getBoundingClientRect().top < 180) active = s;
          });
          active = active || sections[0];
          var num = active.querySelector(".tmg-num");
          var title = active.querySelector(".tmg-sec-title");
          if (nowNum && num) nowNum.textContent = num.textContent;
          if (nowTitle && title) nowTitle.textContent = title.textContent;
          Array.prototype.forEach.call(tocLinks, function (a) {
            if (a.getAttribute("href") === "#" + active.id) a.setAttribute("aria-current", "true");
            else a.removeAttribute("aria-current");
          });
          var box = guide.getBoundingClientRect();
          var span = guide.offsetHeight - window.innerHeight;
          var share = span > 0 ? Math.min(1, Math.max(0, -box.top / span)) : 0;
          if (progress) progress.style.width = (share * 100).toFixed(1) + "%";
        });
      }
      window.addEventListener("scroll", onGuideScroll, { passive: true });

      var savedLevel = "all";
      try { savedLevel = localStorage.getItem("swing-tm-level") || "all"; } catch (err) { /* ingen lagring */ }
      setLevel(savedLevel, false);

      // Smash Factor = Ball Speed / Club Speed, med omdöme och skala.
      Array.prototype.forEach.call(guide.querySelectorAll("[data-calc]"), function (calc) {
        var clubs = JSON.parse(calc.dataset.clubs);
        var zones = JSON.parse(calc.dataset.zones);
        var cs = calc.querySelector("[data-cs]");
        var bs = calc.querySelector("[data-bs]");
        var pct = function (v) { return Math.max(0, Math.min(100, ((v - 1.10) / 0.5) * 100)).toFixed(2) + "%"; };
        var fmt = function (v) { return v.toFixed(2).replace(".", ","); };
        var verdict = function (club, r) {
          if (club === "j6") return "Som jämförelse: Järn 6 på PGA Tour ligger på 1,39.";
          if (club === "pw") return "Som jämförelse: omkring 1,25 är en generell referens för pitching wedge.";
          return r < 1.35 ? "Ofta stor förbättringspotential" : r <= 1.42 ? "Träffen eller energiöverföringen kan förbättras" : r <= 1.46 ? "Bra nivå för många amatörer" : r <= 1.49 ? "Mycket effektiv bollträff" : "Nära typiskt maximalt effektiv nivå med driver";
        };
        var render = function () {
          var club = calc.dataset.club;
          var r = Math.round((Number(bs.value) / Number(cs.value)) * 100) / 100;
          calc.querySelector("[data-cs-out]").textContent = cs.value + " mph";
          calc.querySelector("[data-bs-out]").textContent = bs.value + " mph";
          calc.querySelector("[data-cs-eq]").textContent = cs.value;
          calc.querySelector("[data-bs-eq]").textContent = bs.value;
          calc.querySelector("[data-value]").textContent = fmt(r);
          calc.querySelector("[data-verdict]").textContent = verdict(club, r);
          calc.querySelector("[data-mark]").style.left = pct(r);
          calc.querySelector("[data-ref]").style.left = pct(clubs[club].ref);
          calc.querySelector("[data-ref-label]").textContent = clubs[club].refLabel;
          Array.prototype.forEach.call(calc.querySelectorAll(".tmg-zone"), function (z, i) {
            z.classList.toggle("on", r >= zones[i][0] && r < zones[i][1]);
          });
        };
        cs.addEventListener("input", render);
        bs.addEventListener("input", render);
        Array.prototype.forEach.call(calc.querySelectorAll("button[data-club]"), function (b) {
          b.addEventListener("click", function () {
            var key = b.dataset.club;
            calc.dataset.club = key;
            cs.value = clubs[key].cs;
            bs.value = clubs[key].bs;
            Array.prototype.forEach.call(calc.querySelectorAll("button[data-club]"), function (o) { o.setAttribute("aria-pressed", o === b ? "true" : "false"); });
            render();
          });
        });
      });
    }

    // Händelser i Google Analytics – skickas bara när besökaren har godkänt
    // statistik (annars finns ingen gtag). Inga personuppgifter skickas.
    function track(name, params) {
      if (window.__swingAnalytics && typeof window.gtag === "function") window.gtag("event", name, params || {});
    }

    // Var på sidan knappen sitter – syns som parametern "placering" i Analytics.
    function placement(el) {
      if (el.closest(".nav")) return "meny";
      if (el.closest(".hero, .phero")) return "hero";
      if (el.closest(".cta-band")) return "cta";
      if (el.closest(".apps")) return "appar";
      if (el.closest(".offer")) return "pottkort";
      return "sida";
    }

    // Klick på Boka (Sweetspot på webben eller i appbutikerna), Köp spelpott och
    // Begär offert. Länkar i menyernas undersidor, brödsmulor och sidfot räknas inte.
    document.addEventListener("click", function (e) {
      var a = e.target.closest && e.target.closest("a[href]");
      if (!a || a.closest(".nav-group, .crumbs, footer")) return;
      var url;
      try { url = new URL(a.href); } catch (err) { return; }
      var where = placement(a);
      var page = location.pathname;
      if (/(^|\.)sweetspot\.io$/.test(url.hostname)) {
        track("klick_boka", { placering: where, mal: "webb", sida: page });
      } else if (url.hostname === "apps.apple.com" && /sweetspot/.test(url.pathname)) {
        track("klick_boka", { placering: where, mal: "app_store", sida: page });
      } else if (url.hostname === "play.google.com" && /sweetspot/.test(url.search)) {
        track("klick_boka", { placering: where, mal: "google_play", sida: page });
      } else if (url.origin === location.origin && url.pathname === "/spelpotter/" && a.matches(".btn, .offer")) {
        track("klick_kop_spelpott", { placering: where, sida: page, pott: (url.hash.match(/^#((?:pott|kampanj)-\d+)$/) || [])[1] || "" });
      } else if (url.origin === location.origin && url.pathname === "/foretagsevent/" && url.hash === "#offert" && a.matches(".btn")) {
        track("klick_begar_offert", { placering: where, sida: page });
      }
    });

    // Slutförd förfrågan eller beställning (tacksidorna): data-track-event på sidan.
    // Skickas en gång per förfrågan/order även om sidan laddas om.
    Array.prototype.forEach.call(document.querySelectorAll("[data-track-event]"), function (el) {
      var key = "swing_tracked_" + (el.dataset.trackOnce || el.dataset.trackEvent);
      try { if (sessionStorage.getItem(key)) return; } catch (err) { /* privat läge: skicka ändå */ }
      var params = {};
      try { params = JSON.parse(el.dataset.trackParams || "{}"); } catch (err) { /* ogiltiga parametrar */ }
      if (!window.__swingAnalytics) return;
      track(el.dataset.trackEvent, params);
      try { sessionStorage.setItem(key, "1"); } catch (err) { /* ingen lagring */ }
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
