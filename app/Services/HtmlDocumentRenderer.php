<?php

namespace App\Services;

/**
 * Produces the bytes actually served for an uploaded HTML document.
 *
 * The stored file is never rewritten. A document either goes out exactly as it
 * was uploaded, or goes out with a single <script> tag appended before the
 * closing </body>. Nothing else is ever changed.
 */
class HtmlDocumentRenderer
{
    public function __construct(private HtmlDocumentAnalyzer $analyzer)
    {
    }

    /**
     * Return the document as it should be served.
     */
    public function render(string $html): string
    {
        $analysis = $this->analyzer->analyze($html);

        if (!$analysis['should_inject']) {
            return $html;
        }

        return $this->insertBeforeClosingBody($html, $this->bootstrap($analysis['tier']));
    }

    /**
     * Insert the bootstrap immediately before the final </body>, falling back to
     * appending when the document has no closing body tag.
     */
    private function insertBeforeClosingBody(string $html, string $script): string
    {
        $position = strripos($html, '</body>');

        if ($position === false) {
            return $html . $script;
        }

        return substr($html, 0, $position) . $script . substr($html, $position);
    }

    /**
     * The injected bootstrap.
     *
     * Renders no markup and adds no CSS. It assigns ids to sections that lack
     * them, publishes the section list to the parent frame, and answers
     * scroll-to requests. Everything lives inside an IIFE and every message type
     * is namespaced, so it cannot collide with the uploaded document's own code.
     */
    private function bootstrap(string $tier): string
    {
        $tierAttribute = htmlspecialchars($tier, ENT_QUOTES, 'UTF-8');

        return <<<HTML

<script data-gihqs-bootstrap data-tier="{$tierAttribute}">
(function(){
  "use strict";
  var TIER = document.currentScript ? document.currentScript.getAttribute("data-tier") : "heading";
  var INERT = { SCRIPT: 1, STYLE: 1, TEMPLATE: 1 };

  function isInert(node) {
    for (var n = node.parentNode; n; n = n.parentNode) {
      if (n.nodeName && INERT[n.nodeName.toUpperCase()]) return true;
    }
    return false;
  }

  function slugify(text, used) {
    var base = String(text).toLowerCase().trim()
      .replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "") || "section";
    var id = base, n = 2;
    while (used[id] || document.getElementById(id)) { id = base + "-" + n++; }
    used[id] = 1;
    return id;
  }

  function headingText(el) {
    var h = el.querySelector("h1,h2,h3");
    return h && h.textContent.trim() ? h.textContent.trim() : "";
  }

  function collect() {
    var used = {}, out = [], i, el, label, id;

    if (TIER === "section-id" || TIER === "section-heading") {
      var sections = document.querySelectorAll("section");
      for (i = 0; i < sections.length; i++) {
        el = sections[i];
        if (isInert(el)) continue;
        label = headingText(el);
        if (!el.id && !label) continue;
        if (el.id) { used[el.id] = 1; } else { el.id = slugify(label, used); }
        out.push({ id: el.id, label: label || el.id });
      }
    } else {
      var headings = document.querySelectorAll("h2");
      for (i = 0; i < headings.length; i++) {
        el = headings[i];
        if (isInert(el)) continue;
        label = el.textContent.trim();
        if (!label) continue;
        if (!el.id) { el.id = slugify(label, used); } else { used[el.id] = 1; }
        out.push({ id: el.id, label: label });
      }
    }
    return out;
  }

  var sections = collect();

  function post(message) {
    try { parent.postMessage(message, "*"); } catch (e) {}
  }

  post({ type: "gihqs:sections", sections: sections });

  window.addEventListener("message", function (event) {
    var data = event.data;
    if (!data || data.type !== "gihqs:scrollTo" || !data.id) return;
    var target = document.getElementById(data.id);
    if (target && target.scrollIntoView) {
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });

  var active = null;
  function reportActive() {
    var best = null, bestTop = Infinity, i, el, top;
    for (i = 0; i < sections.length; i++) {
      el = document.getElementById(sections[i].id);
      if (!el) continue;
      top = el.getBoundingClientRect().top;
      if (top <= 120 || (best === null && top < bestTop)) {
        if (top <= 120) { best = sections[i].id; }
        else if (top < bestTop) { bestTop = top; best = sections[i].id; }
      }
    }
    if (best && best !== active) { active = best; post({ type: "gihqs:active", id: best }); }
  }

  var ticking = false;
  window.addEventListener("scroll", function () {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(function () { reportActive(); ticking = false; });
  }, { passive: true });

  reportActive();
})();
</script>
HTML;
    }
}
