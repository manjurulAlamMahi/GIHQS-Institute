# HTML Resource Upload & Rendering System

**Date:** 2026-09-01
**Status:** Approved, ready for implementation planning
**Scope:** Laravel API + admin panel, React frontend (`Frontend_Website/`)

---

## Problem

The platform needs to host standalone HTML documents — course modules, story guides, and
interactive toolkits — supplied by the client as complete, self-contained `.html` files.

Today three fixed columns on `catalogues` (`details_file`, `story_guide_file`, `module_file`)
hold one HTML file each, and `AutoIframe` renders them in an iframe. That works, but it does
not satisfy the requirements:

1. A catalogue cannot carry more than three HTML documents, and none of the three slots is a
   natural home for a toolkit.
2. Uploaded documents have no consistent left-hand navigation. Most have no navigation at all.
3. Paid HTML is fetchable by direct URL — the API response is gated, the file on disk is not.
4. Uploaded HTML executes on the SPA's own origin and can read the viewer's JWT.

### The source material

Three real files, three genuinely different shapes. Any design that only handles one of them
fails the "works with every uploaded HTML" requirement.

| File | Own sidebar | Section ids | Behaviour |
|---|---|---|---|
| `Healthcare_RCA_Toolkit_Standalone.html` | `aside#nav`, sticky, 6 `data-target` buttons | yes | tab-switch (`.section{display:none}`), ~25 form fields persisted to `localStorage`, `buildSummary()` / `nextSection()` / `saveLocal()` / `clearAll()` / `window.print()` |
| Course / Module Assessment | `aside.sidebar` — **decorative**: eyebrow, meta rows, progress bar, no links or buttons | no | plain scroll |
| RCA Learning Module | none | no | plain scroll |

All three are complete documents with `<!DOCTYPE>`, their own `<style>`, and (in two cases)
Google Fonts. None of the non-toolkit files contains a single `id` attribute or internal
`href="#…"` anchor, so tab-to-section navigation cannot be wired up — it must be derived.

---

## Decisions

| # | Decision | Rationale |
|---|---|---|
| 1 | **Respect a file's own navigation; generate only when absent.** | The toolkit's nav drives `show()`, `nextSection()` and active-tab state. Replacing it means re-driving that logic from outside the iframe and keeping it in sync — the fragile path. |
| 2 | **New `catalogue_html_resources` table**, N documents per catalogue. | "Reusable for different courses/modules/toolkits" cannot be met by three fixed columns. |
| 3 | **Serve uploaded HTML from the API origin, not the SPA origin.** | Removes uploaded scripts' access to the SPA's `localStorage` and JWT. |
| 4 | **Serve through Laravel, inject at request time.** | Enables entitlement gating, leaves the stored file untouched, and lets a later improvement to the injector benefit every existing document. |

---

## 1. Data model

New table `catalogue_html_resources`:

| Column | Type | Notes |
|---|---|---|
| `id` | pk | |
| `catalogue_id` | fk → `catalogues`, cascade delete | |
| `title` | string | Shown in the course UI and as the document label |
| `kind` | enum: `module`, `story_guide`, `toolkit`, `worksheet` | A label for grouping and iconography. **Does not affect rendering.** |
| `file_path` | string | Relative path under `uploads/html-resources/` |
| `is_public` | boolean, default `false` | `true` bypasses the entitlement check (marketing/brochure documents) |
| `sort_order` | integer, default 0 | Ordering within a catalogue |
| timestamps | | |

`details_file`, `story_guide_file` and `module_file` on `catalogues` are **left in place and
unchanged**. Nothing migrates. The existing pages keep working exactly as they do now.

### Model

`App\Models\CatalogueHtmlResource` — `belongsTo(Catalogue::class)`.
`Catalogue` gains `hasMany(CatalogueHtmlResource::class)->orderBy('sort_order')`.

---

## 2. Serving pipeline

### Route

```
GET /api/html/{resource}   →  HtmlResourceController@show
```

Served from the API host. Returns `text/html`.

### Steps

**a. Authorize.** If `is_public` is false, require an authenticated user with catalogue access.
Reuse `ExamEligibilityService::hasCatalogueAccess()` — the same rule the exam and catalogue
endpoints use, so entitlement cannot drift between them. Deny → 403.

**b. Read and analyse — read-only.** Load the stored file. Parse a throwaway copy with PHP's
built-in `DOMDocument` (`ext-dom`, confirmed available), wrapped in
`libxml_use_internal_errors(true)` — client HTML is not guaranteed well-formed and parse
warnings must not surface.

> **The parsed DOM is never serialised back out.** `DOMDocument::saveHTML()` rewrites markup —
> it normalises self-closing tags, re-encodes entities, and can corrupt inline `<script>`
> contents. Round-tripping arbitrary client HTML through it risks silently altering a working
> document. The DOM is used **only to answer questions about the file**; what is served is
> always the original bytes, with at most one `<script>` tag appended before `</body>` by plain
> string insertion.

**c. Classify: does the file own its navigation?**

> A file owns its navigation if it contains an `<aside>` or `<nav>` holding **two or more**
> controls (`<a>`, `<button>`) that reference an in-document target — either a `data-target`
> attribute or an `href` beginning with `#`.

Verified against the real files:

- Toolkit → `aside#nav` with 6 `data-target` buttons → **owns nav**
- Assessment → `aside.sidebar` contains zero `<a>` and zero `<button>` → **does not own nav**
- Learning Module → no `<aside>`/`<nav>` → **does not own nav**

**d. Decide whether a sidebar is worth generating.** Using the read-only DOM, count section
candidates in priority order:

1. Existing `<section id="…">`.
2. `<section>` without an id but with a heading (`<h1>`–`<h3>`) to label it.
3. If 1 and 2 together yield fewer than 2, fall back to `<h2>` elements in document order.

Headings inside `<script>`, `<style>` or `<template>` are ignored. **Fewer than 2 candidates →
no navigation is generated**; serve the original bytes unchanged.

The server does not assign the ids — it only determines that usable candidates exist, and which
rule tier applies. Actual id assignment happens in the browser (section 3), so the stored markup
is never rewritten.

**e. Serve.**

- **Owns its nav, or fewer than 2 candidates** → return the original bytes, untouched. Nothing
  is appended. The toolkit is served byte-for-byte identical to the uploaded file.
- **Otherwise** → return the original bytes with one `<script>` tag inserted before the final
  `</body>` (plain string insertion; if no `</body>` is found, append to the end). The tag
  carries the chosen rule tier as a `data-tier` attribute.

**f. Cache** the transformed output keyed on `resource id + file mtime`, so a document is parsed
once and re-parsed automatically if the file is replaced.

### Response headers

`Content-Type: text/html; charset=utf-8`, `X-Content-Type-Options: nosniff`,
`Cache-Control: private, max-age=0, must-revalidate`.

---

## 3. Navigation — nothing visual is injected

Injecting a sidebar into an arbitrary document's layout is the most reliable way to break its
CSS. The injected script is therefore **invisible** and purely a message channel.

### Injected bootstrap (~1 KB, no dependencies)

Injected **only** into documents that do not own their navigation and have at least two section
candidates. Responsibilities, in full:

1. Walk the document using the rule tier given in `data-tier` and assign an `id` to each section
   that lacks one — slugified from its heading text, de-duplicated with `-2`, `-3`, … Existing
   ids are never overwritten.
2. `postMessage` the section list to the parent — `{type:'gihqs:sections', sections:[{id,label}]}`.
3. Listen for `{type:'gihqs:scrollTo', id}` and run `scrollIntoView({behavior:'smooth'})`.
4. On scroll, report the section currently in view — `{type:'gihqs:active', id}` — for scrollspy.

It renders no markup and adds no CSS. Id assignment happens against the live DOM, which is both
safer than server-side rewriting and more accurate — the browser's HTML parser handles malformed
markup the same way it will when the document is displayed.

Documents that **own their navigation** receive nothing at all: no script, no ping, no
modification. They are served byte-for-byte as uploaded.

### Platform-side sidebar

The platform renders the sidebar **in React, outside the iframe**, styled with the dashboard's
own design system, populated from the `gihqs:sections` message. `postMessage` is cross-origin by
design, so this is unaffected by decision 3.

**Consequences, stated explicitly:**

- Uploaded CSS cannot reach the platform sidebar, and platform CSS cannot reach the document.
  Guaranteed by the iframe boundary, not by naming discipline.
- "Sidebar visible while content scrolls" is structural — the sidebar is not inside the scrolling
  region at all.
- **A file that owns its navigation gets no platform sidebar.** The toolkit will therefore look
  different from a generated-nav document. This is the direct and accepted consequence of
  decision 1.

---

## 4. Frontend

### `HtmlResourceViewer`

Replaces `AutoIframe` for these documents:

- Fixed-height iframe — `height: calc(100vh - 114px)`, matching the value `AutoIframe` already
  uses for the dashboard chrome, with `scrolling="auto"`. Required for two
  independent reasons: a cross-origin iframe cannot be height-measured, and a file's own
  `position:sticky` nav only works when the iframe has its own scroll container.
- Optional platform sidebar, shown only when a `gihqs:sections` message arrives.
- Scrollspy highlight driven by `gihqs:active`.
- Origin check on every received message; ignore anything not from the API origin.
- Mobile (<768px): sidebar collapses to a toggle above the document.

`AutoIframe` is left in place for the three legacy columns.

### Routes

A generic viewer: `/dashboard/courses/:catalogueId/resources/:resourceId`, behind `PrivateRoute`.
Public resources (`is_public`) additionally reachable without auth.

### `vercel.json` — no change required

The original plan was to narrow the `/uploads/:path*` rewrite so it stopped covering `.html`.
**That turned out to be both unnecessary and harmful, and was reverted during implementation.**

- *Unnecessary*: new documents are served from `/api/html/{id}` on the API origin. That path was
  never under the `/uploads` rewrite, so the isolation goal is met without touching Vercel.
- *Harmful*: six existing pages (the five About pages, Advisory, Other Pages) render
  `content_file` through `AutoIframe` in **auto-height** mode, which requires same-origin access
  to measure the document. Removing the proxy would break their layout, and a non-proxied
  `/uploads/*.html` would fall through to the SPA catch-all rewrite and render the application
  inside its own iframe.

**Known residual issue, deliberately left in place:** legacy HTML reached through `AutoIframe`
(`details_file`, `story_guide_file`, `module_file`, and the About/Other Pages `content_file`)
is still proxied onto the SPA origin and can therefore read `localStorage`. Closing that means
migrating those documents onto the new endpoint, which is out of scope here. It is recorded in
"Follow-up work" below rather than partially broken now.

---

## 5. Admin

CRUD under the existing catalogue edit screen: list, add, reorder, delete HTML resources.
Upload validation mirrors the current rules — `mimes:html,txt`, `max:10240` (10 MB) — stored via
`MiaHelper::uploadFile($file, 'html-resources')`.

Because uploaded HTML runs scripts, the upload form states plainly that HTML resources execute
JavaScript and must only be accepted from trusted sources.

---

## 6. Testing

Pest feature tests over four fixtures:

| Fixture | Asserts |
|---|---|
| Toolkit (real client file) | classified as owning nav; `data-target` buttons and inline `<script>` survive byte-intact; no sidebar markup injected |
| Learning Module | nav generated from `<h2>`s; ids assigned; section list well-formed |
| Assessment | decorative `<aside>` correctly **not** treated as nav |
| Pathological | no sections; nested sections; duplicate ids; `<h2>` inside `<script>`; malformed markup — must not throw, must degrade to no sidebar |

Access control:

- non-purchaser → 403 on a non-public resource
- purchaser → 200
- `is_public` → 200 anonymously
- unknown resource → 404

Regression: the existing `AutoIframe` pages continue to work unchanged.

---

## Follow-up work

1. **Migrate legacy HTML onto the new endpoint.** `details_file`, `story_guide_file`,
   `module_file` and the About/Other Pages `content_file` are still served through the
   `/uploads` proxy on the SPA origin, where their scripts can read the viewer's JWT from
   `localStorage`. Moving them behind `/api/html/{id}` closes it, but the six auto-height
   `AutoIframe` callers must move to fixed-height rendering first, since a cross-origin iframe
   cannot be measured.
2. Consider storing the JWT somewhere script-inaccessible, which would neutralise this class of
   issue regardless of origin.

## Out of scope

- Migrating the three existing columns into the new table.
- Editing HTML in the admin panel.
- Per-user progress or state inside uploaded documents (the toolkit persists to its own
  `localStorage`, which is sufficient and unchanged).
- Server-side sanitisation of uploaded HTML. Origin isolation is the control; stripping scripts
  would break the toolkit.

---

## Risks

| Risk | Mitigation |
|---|---|
| A file's own nav is missed by the heuristic and a redundant sidebar appears | Threshold is deliberately low (2 targeted controls). Pathological fixture covers it; `is_public`-style override can be added if a real file trips it. |
| Malformed client HTML breaks `DOMDocument` | Parsing is read-only and advisory. `libxml_use_internal_errors(true)`; on parse failure, fall through to serving the original file with no injection. A document can never be corrupted by the pipeline, because it is never rewritten. |
| Injected script collides with a variable or handler in the uploaded file | Bootstrap runs inside an IIFE, declares nothing on `window`, and namespaces all message types under `gihqs:`. |
| Dropping the `/uploads` HTML rewrite breaks an existing link | Rewrite is narrowed to HTML only; legacy columns keep using `AutoIframe` and the old path. |
| Per-request parsing cost | Cached on `resource id + file mtime`. |
