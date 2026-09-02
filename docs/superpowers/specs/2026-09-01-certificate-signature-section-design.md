# Certificate Signature Section

**Date:** 2026-09-01
**Status:** Approved, implementing
**Builds on:** `2026-09-01-certificate-presentation-design.md`

---

## Problem

The signature footer cannot be controlled. Three issues, all rooted in one cause.

**Placeholder granularity is wrong.** The template hardcodes each block's scaffolding — the rule
line and the caption — and substitutes only the image, name and title. So "hide the signature" can
only ever mean "omit the image", leaving an orphaned rule, title and caption behind. This spec
supersedes the earlier decision that a missing signature image should still render its rule and
text; the client has asked for the opposite.

**No selection.** There is no way to say which of the two signatories appears on a certificate.

**Visual confirmation is outstanding.** Nobody has checked the finished certificate against the
client's expected format.

---

## Decisions

| # | Decision | Rationale |
|---|---|---|
| 1 | Explicit show/hide toggles in certificate settings | Matches "can be selected". A toggle can hide a signatory whose name and signature are still on file; an automatic rule cannot express that. |
| 2 | Block-level placeholders | The only way to remove a section's rule and caption along with its image. |
| 3 | Column widths never change | The seal holds the true page centre and the visible signature keeps its side, so nothing drifts out of alignment with the border, crest or meta bar. |
| 4 | Captions move into the renderer | So they disappear with their block instead of stranding. |

---

## 1. Template

Each signature cell collapses to a single placeholder. The surrounding `<td>` and its width stay in
the template, which is what keeps the layout fixed when a block is hidden:

```html
<td width="34%" align="center" valign="bottom">{{CHAIRMAN_BLOCK}}</td>
<td width="32%" align="center" valign="bottom">{{SEAL_HTML}}</td>
<td width="34%" align="center" valign="bottom">{{EXECUTIVE_DIRECTOR_BLOCK}}</td>
```

`CertificateRenderer` builds the entire inner table — signature image, 180px rule, name, title,
caption — or returns an empty string. Applied identically to `certification_template.html` and
`others_template.html`.

The retired placeholders (`{{CHAIRMAN_SIGNATURE_HTML}}`, `{{CHAIRMAN_NAME}}`,
`{{CHAIRMAN_TITLE}}` and their Executive Director counterparts) keep working as empty
substitutions, so a customised template that still contains them cannot print literal tokens.

## 2. Data

`certificate_settings` gains two booleans, both defaulting to `true`, backfilled to `true` for
existing rows so appearance is unchanged:

| Column | Meaning |
|---|---|
| `show_chairman` | render the left block |
| `show_executive_director` | render the right block |

## 3. Visibility rule

A block renders when **both** hold:

1. Its toggle is on, **and**
2. it has something to show — a name **or** a signature image.

The second condition is a safety net: an enabled block with no signatory would otherwise print a
bare rule and caption, which is worse than showing nothing. A title alone does not qualify, since
every title falls back to a default and would therefore always pass.

When a block is hidden its cell renders `&nbsp;` so the table column survives at its declared
width.

## 4. Captions

| Side | Caption |
|---|---|
| Left | `GIHQS Certification Authority` |
| Right | `GIHQS Professional Standards` |

Moved verbatim from the templates into the renderer. Not admin-editable.

## 5. Admin

Two checkboxes on the certificate settings screen, each placed with the name and title fields it
governs, labelled to make clear they hide the whole section rather than just the signature image.

## 6. Testing

Four combinations — both, chairman only, director only, neither — each asserting:

- the hidden side's name, title **and caption** are all absent
- the visible side's name, title and caption are present
- the column widths `34%` / `32%` / `34%` are unchanged
- the seal still renders
- no unreplaced `{{...}}` token survives

Plus the existing guarantees: issue date present, no "Valid Until", recipient and credential
correct.

## 7. Visual verification

**This cannot be completed unaided.** No PDF rasteriser is installed (`pdftoppm`, ImageMagick and
Ghostscript are all absent), so the rendered PDF cannot be inspected here.

The split of responsibility:

- **Verified programmatically:** content, ordering and structure, by inflating the PDF's
  FlateDecode content streams and asserting on the extracted text. Note that `strings` does **not**
  work on a Dompdf PDF — it extracts nothing, so every content assertion passes vacuously. The
  extractor therefore fails loudly if it recovers less than 200 bytes.
- **Requires human eyes:** whether the layout matches the client's expected format. A rendered
  preview of all four combinations is published as a browser page for that purpose.

---

## Out of scope

- Per-catalogue choice of signatories.
- Editable captions.
- More than two signature blocks.
- Changing the seal, border or any other part of the certificate design.
