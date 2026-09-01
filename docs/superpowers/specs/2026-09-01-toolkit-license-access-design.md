# Toolkit Licence & Access Control

**Date:** 2026-09-01
**Status:** Approved, implementing
**Builds on:** `2026-09-01-html-resource-system-design.md`

---

## Problem

Two things, closed by one mechanism.

### 1. A defect in the delivered HTML resource system

`GET /api/html/{resource}` authenticates from the `Authorization` header. An
`<iframe src="…">` **cannot set headers**, so the browser path was broken for every paid
document. Verified against the running app:

| Request | Result |
|---|---|
| curl with `Authorization: Bearer …` | 200 |
| Browser-shaped iframe request (no header, cookies sent) | **403** |
| Anonymous | 403 |

296 unit tests and 22 live probes passed because every one of them set the header explicitly.
None exercised the shape a browser actually produces. The lesson is recorded in Testing below.

Cookies cannot rescue this: the JWT parser chain is header-only by deliberate decision (see the
security work), and the login cookie is `SameSite=Lax`, so it is not sent on a cross-site iframe
subresource request anyway.

### 2. Toolkits need licence-gated access

Admins must be able to attach an access key to a resource, users must redeem it, and a copied
URL must not grant access.

---

## Decisions

| # | Decision | Rationale |
|---|---|---|
| 1 | Users redeem a key the admin set | Fits the checklist: a key field on the resource *and* real per-user licences to expire, revoke and test against. |
| 2 | One shared key per resource | Matches "a license/access key field" literally, simplest to administer. The per-user licence row still allows individual expiry and revocation. |
| 3 | Short-lived single-use tickets | Lets an iframe authenticate without a header, and makes a copied URL worthless. One mechanism solves both problems. |
| 4 | No third-party object storage | Explicit instruction. Everything stays on the existing server and database. |

---

## 1. Data model

### `catalogue_html_resources` — two new nullable columns

| Column | Type | Meaning |
|---|---|---|
| `access_key` | `string`, nullable | The licence key. **null means no key is required**, so every existing resource keeps working unchanged. |
| `license_validity_days` | `integer`, nullable | null means licences never expire. Otherwise a redemption expires this many days later. |

### `html_resource_licenses` — new table

| Column | Notes |
|---|---|
| `user_id` | fk, cascade delete |
| `catalogue_html_resource_id` | fk, cascade delete |
| `granted_at` | when the key was redeemed |
| `expires_at` | nullable; null means no expiry |
| `revoked_at` | nullable; set by an admin to withdraw access |
| unique | `(user_id, catalogue_html_resource_id)` — one licence per user per resource |

A licence is **valid** when `revoked_at is null` and (`expires_at is null` or `expires_at` is in
the future).

---

## 2. The access decision

Evaluated in order, in one place, for both ticket issue and document serve:

1. `is_public` → allow.
2. Otherwise a user must be resolved. No user → 403.
3. The user must have catalogue access — the existing
   `ExamEligibilityService::hasCatalogueAccess()`. No access → 403.
4. If `access_key` is set, the user must hold a **valid** licence. No licence → 403 with reason
   `license_required`; expired → `license_expired`; revoked → `license_revoked`.

Reasons are returned so the frontend can show the right screen rather than a generic error.

---

## 3. Tickets

### Endpoints

| Route | Auth | Purpose |
|---|---|---|
| `POST /api/html/{resource}/redeem` | Bearer | Body `{key}`. Validates and creates a licence. |
| `POST /api/html/{resource}/ticket` | Bearer | Runs the access decision, returns a one-time URL. |
| `GET /api/html/view/{ticket}` | **none** | Serves the document. This is what the iframe loads. |
| `GET /api/html/{resource}` | none | Retained for `is_public` documents only. |

### Ticket properties

- Opaque random value (32 bytes, URL-safe), never derived from the resource id.
- Held in the cache for **60 seconds**, storing the resolved user id and resource id.
- **Consumed on first fetch** — the cache entry is deleted before the response is produced.
- The access decision is re-run at issue time, so revoking a licence takes effect immediately.

Consequences:

- An iframe can load the document, because the URL carries its own authority.
- A copied URL is worthless: already consumed, or expired within a minute.
- **Known trade-off:** reloading inside the iframe (right-click → Reload frame) fails, because
  the ticket is spent. The viewer mints a fresh ticket on mount, so ordinary navigation and full
  page refreshes are unaffected. Accepted deliberately in exchange for copied URLs being dead.

### Key comparison

`hash_equals` against the stored `access_key`, so redemption cannot be timed. Redemption is rate
limited to blunt guessing.

---

## 4. Admin

- Access key and validity-days fields on the resource create/edit form.
- Per-resource licence list: who redeemed, when, expiry, revoked state, and a revoke action.
- The form states that a shared key can be passed on, so the licence list shows who redeemed it,
  not who was entitled to.

---

## 5. User side

The viewer requests a ticket on mount and branches on the reason it gets back:

| Reason | Screen |
|---|---|
| ok | Renders the iframe with the ticket URL |
| `license_required` | Key entry form |
| `license_expired` | "Your access has expired" plus the key form to re-redeem |
| `license_revoked` | "Access withdrawn", no key form |
| 403 without a reason | "You do not have access to this course" |

A wrong key returns a validation error on the form.

---

## 6. Testing

Everything on the checklist, plus the case that was missed:

| Test | Asserts |
|---|---|
| **Browser-shaped iframe request** — no `Authorization` header | The ticket URL serves 200. This is the regression test for the defect above. |
| Another user's account | A second user with course access but no licence is refused. |
| Direct URL without authorisation | `/api/html/{id}` on a licensed resource is refused anonymously. |
| Reused ticket | Second fetch of the same ticket is 404/403. |
| Expired ticket | A ticket older than its TTL is refused. |
| Wrong key | Redemption fails, no licence row created. |
| Expired licence | Ticket issue refused with `license_expired`. |
| Revoked licence | Ticket issue refused with `license_revoked`. |
| No key configured | Behaviour is exactly as before this change. |

**Testing lesson, recorded deliberately:** an endpoint that a browser loads directly must have at
least one test that sends what a browser sends. Testing it only through an API client with an
auth header proves the API contract while leaving the real path broken.

---

## Out of scope

- Unique single-use key batches (the schema does not preclude adding them later).
- Third-party object storage — explicitly excluded.
- Self-service licence purchase; keys are distributed by the admin out of band.
