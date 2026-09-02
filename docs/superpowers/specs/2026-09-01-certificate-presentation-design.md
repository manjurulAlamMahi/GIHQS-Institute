# Certificate Presentation & Access

**Date:** 2026-09-01
**Status:** Approved, implementing

---

## Scope

The client clarified that a certificate needs **only an issue date** — no validity or expiration
field. Alongside that, the signature block must show the signer's name and title.

Two items on the original checklist are **already complete** from earlier in this session and are
re-verified rather than rebuilt:

- *Fix the certification flow so the exam cannot be bypassed* — `cm_user_id` is HMAC-signed, the
  ClassMarker webhook re-runs entitlement, coursework, attempt and cooldown checks, and result
  attribution is bound to the test that was actually sat.
- *Verify the user must complete the required process before certification* — coursework
  completion is enforced server-side in `ExamEligibilityService`, not only in the browser.

---

## Findings

| Item | State before this work |
|---|---|
| Issue date | Present, correct. `{{ISSUE_DATE}}` under a "Date of Issue" label. |
| Valid Until | Present in **both** templates: a `Valid Until` label and `{{EXPIRY_DATE}}`. Must go. |
| Signature image | Present, rendered from `CertificateSetting`. |
| Signer **title** | Present but **hardcoded** — "Chairman of the Board", "Executive Director". |
| Signer **name** | **Missing.** `chairman_name` and `executive_director_name` are captured by the admin screen and stored, but no placeholder ever rendered them. |

The date row is a four-cell table: `Certificate ID | Date of Issue | Valid Until | spacer`, at
`30% / 30% / 30% / 10%`.

---

## Decisions

| # | Decision | Rationale |
|---|---|---|
| 1 | Remove expiry **from the certificate only** | The client spoke about what appears on the certificate. Public verification keeps reporting an expiry, and `validity_years` keeps governing re-application, so nobody's eligibility to re-sit an exam changes. |
| 2 | Render name above title | The conventional arrangement, and the missing piece. |
| 3 | Make titles admin-editable | Names are already editable; a hardcoded title beside an editable name breaks the first time an officeholder's title differs. |
| 4 | Leave certificate PDFs publicly reachable | A certificate is meant to be shown to an employer. URLs are unguessable and serials carry random entropy. Gating them would break the point of the public verification endpoint. |

---

## 1. Template changes

Applied identically to `resources/views/certification/certification_template.html` (Certification)
and `others_template.html` (Courses).

**Date row.** Delete the `Valid Until` cell. Rebalance the remaining cells to `45% / 45% / 10%` so
`Certificate ID` and `Date of Issue` sit evenly rather than leaving a visible gap.

**Generator.** `{{EXPIRY_DATE}}` is no longer supplied. It is replaced with an empty string so a
stale template that still contains the token renders nothing rather than a literal `{{EXPIRY_DATE}}`.

## 2. Signature block

Current order is: signature image → rule → hardcoded title → "GIHQS Certification Authority".

New order:

```
[signature image]
──────────────────
{{CHAIRMAN_NAME}}              bold, 8.5pt
{{CHAIRMAN_TITLE}}             regular, 8pt
GIHQS Certification Authority  unchanged, 7pt
```

and the mirror image on the right using `{{EXECUTIVE_DIRECTOR_NAME}}` and
`{{EXECUTIVE_DIRECTOR_TITLE}}`.

## 3. Data

`certificate_settings` gains two nullable columns:

| Column | Default |
|---|---|
| `chairman_title` | `Chairman of the Board` |
| `executive_director_title` | `Executive Director` |

The migration backfills existing rows with those values, so appearance is unchanged until an admin
edits them. Both are exposed on the existing certificate settings screen beside the matching name.

## 4. Missing data

A certificate must never render a half-empty signature block or a stray label.

- Name blank → the name line is omitted; the title still renders (today's appearance).
- Title blank → falls back to the column default.
- Signature image missing → rule and text still render.
- No "N/A", no empty labels, no unreplaced `{{...}}` tokens under any combination.

## 5. Testing

### Content

Assert the rendered certificate HTML:

- contains the issue date
- contains both signer names and both titles
- contains **no** "Valid Until" and no `{{EXPIRY_DATE}}`
- contains **no** unreplaced `{{...}}` placeholder at all
- degrades correctly with a blank name, a blank title, and a missing signature image

### End to end

Buy a course → the coursework gate refuses the exam → complete the videos → sit the exam → pass →
a PDF is written → fetch it over HTTP and confirm it is a real PDF → verify the serial through the
public endpoint.

### Access security

| Question | Test |
|---|---|
| Can an unauthorised user *generate* another user's certificate? | Only two paths create one. A forged ClassMarker webhook and a tampered `cm_user_id` both produce no result row. |
| Can they *access* another user's certificate? | `download_certificate` appears only on the owner's own result; a second user's API responses never contain it. |
| Can certificates be enumerated? | Serials carry a random block; sequential guesses do not resolve. |

---

## Out of scope

- Removing expiry from the verification endpoint or from `hasValidCertification()`.
- Gating certificate PDFs behind authentication.
- Changing the certificate's visual design beyond the two edits above.
