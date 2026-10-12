/* Swing Golf & Lounge — lightbox för bildgalleriet (.gal-item är <a href="stor bild">) */
(function () {
  function boot() {
    var items = Array.prototype.slice.call(document.querySelectorAll(".gal-item"));
    var lb = document.querySelector(".lb");
    if (!items.length || !lb) return;

    var img = lb.querySelector("img");
    var caption = lb.querySelector(".lb-caption");
    var current = 0;
    var lastFocus = null;
    var touchX = null;

    function show(i) {
      current = (i + items.length) % items.length;
      var it = items[current];
      img.src = it.getAttribute("href");
      img.alt = it.querySelector("img") ? it.querySelector("img").alt : "";
      caption.textContent = it.getAttribute("data-caption") || "";
    }
    function open(i) {
      lastFocus = document.activeElement;
      show(i);
      lb.classList.add("open");
      document.body.style.overflow = "hidden";
      lb.querySelector(".lb-close").focus();
    }
    function close() {
      lb.classList.remove("open");
      document.body.style.overflow = "";
      img.src = "";
      if (lastFocus) lastFocus.focus();
    }

    items.forEach(function (it, i) {
      it.addEventListener("click", function (e) { e.preventDefault(); open(i); });
    });
    lb.querySelector(".lb-close").addEventListener("click", close);
    lb.querySelector(".lb-prev").addEventListener("click", function () { show(current - 1); });
    lb.querySelector(".lb-next").addEventListener("click", function () { show(current + 1); });
    lb.addEventListener("click", function (e) { if (e.target === lb) close(); });

    document.addEventListener("keydown", function (e) {
      if (!lb.classList.contains("open")) return;
      if (e.key === "Escape") close();
      else if (e.key === "ArrowLeft") show(current - 1);
      else if (e.key === "ArrowRight") show(current + 1);
      else if (e.key === "Tab") {
        // Fokusfälla: Tab cirkulerar mellan dialogens knappar.
        var buttons = Array.prototype.slice.call(lb.querySelectorAll("button"));
        var i = buttons.indexOf(document.activeElement);
        e.preventDefault();
        buttons[(i + (e.shiftKey ? -1 : 1) + buttons.length) % buttons.length].focus();
      }
    });

    lb.addEventListener("touchstart", function (e) { touchX = e.changedTouches[0].clientX; }, { passive: true });
    lb.addEventListener("touchend", function (e) {
      if (touchX === null) return;
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 48) show(current + (dx < 0 ? 1 : -1));
      touchX = null;
    }, { passive: true });
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
