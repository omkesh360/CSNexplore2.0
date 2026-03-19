# Bugfix Requirements Document

## Introduction

The CSNExplore PHP travel/tourism website has three interconnected bug clusters affecting the user-facing login/authentication flow, the booking UI on listing-detail pages, and the admin panel. The core issue is that the site uses a **JWT-in-localStorage** authentication model: tokens are stored as `csn_token` / `csn_user` (regular users) and `csn_admin_token` / `csn_admin_user` (admin), and all auth state is toggled client-side by JavaScript. When this JS does not run correctly — or when the Material Symbols icon font fails to load — the UI renders raw icon ligature text and broken layout elements instead of the intended icons and conditional UI.

The bugs span:
1. **Login & Authentication** — session persistence, redirect loops, and token key mismatches.
2. **Booking UI on listing-detail pages** — broken icon rendering and the guest/logged-in state toggle not working correctly, causing the "lock Login or Register to Book" block to show as plain text.
3. **Admin panel** — admin auth guard using a separate localStorage key (`csn_admin_token`) that is never set by the regular login flow, plus several admin page functional issues.

---

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a user logs in via `login.php` and their role is `admin`, THEN the system stores the token under `csn_token` / `csn_user` but the admin panel auth guard reads `csn_admin_token` / `csn_admin_user`, so the admin is immediately redirected back to `adminexplorer.php` on every admin page visit.

1.2 WHEN a user visits `adminexplorer.php` and logs in successfully, THEN the system stores the token under `csn_admin_token` / `csn_admin_user` but the regular site header reads `csn_token` / `csn_user`, so the admin appears logged out on the public-facing site.

1.3 WHEN a logged-in user visits `login.php` or `register.php`, THEN the early-redirect script checks `csn_token` but may fail silently on a malformed JWT, leaving the user stuck on the login page instead of being redirected away.

1.4 WHEN the Material Symbols Outlined font fails to load (network error, slow connection, or ad-blocker), THEN icon ligature strings such as `star`, `call`, `lock`, `schedule`, `WhatsApp` render as visible plain text throughout all pages.

1.5 WHEN a user is not logged in and visits a listing-detail page (e.g. `listing-detail/stays-1-hotel-renaissance-aurangabad.html`), THEN the booking sidebar correctly shows the guest state block; however WHEN the user logs in and returns via the `?redirect=` parameter, THEN the booking form does not always replace the guest state block because the auth-state JS runs before `localStorage` is populated by the login redirect, leaving the "Login or Register to Book" block visible even to authenticated users.

1.6 WHEN the booking form on a listing-detail page is submitted by a logged-in user, THEN the system sends the POST to `../php/api/bookings.php` without an `Authorization` header, yet the bookings API `POST` endpoint does not require auth — this is correct — but the form's auth-check JS block re-checks `csn_token` and may show the login-required modal incorrectly if the token has just expired.

1.7 WHEN an admin navigates to `admin/listings.php` and tries to edit a listing, THEN the `openEditModal` function calls `api('../php/api/listings.php?category='+currentCat+'&id='+id)` which expects a single object response, but the listings API may return an array, causing the form fields to remain empty.

1.8 WHEN an admin navigates to `admin/bookings.php` and the `loadBookings` function fetches data, THEN the `colspan` in the loading row is set to `8` but the table has `9` columns, causing a visual misalignment.

1.9 WHEN an admin navigates to `admin/content.php` and the homepage section is loaded, THEN the `loadHomepage` function calls `api('../php/api/about_contact.php?section=homepage')` but the API may return the raw JSON string stored in the `content` column rather than a parsed object, so all homepage fields remain blank.

1.10 WHEN a user submits the contact form on `contact.php`, THEN the form posts to `php/api/contact.php` but the `.htaccess` rule `RewriteRule ^php/(?!api/).*$ - [F,L]` should allow API calls — however if the API file does not exist or returns an error, the user sees no feedback.

1.11 WHEN the listing-detail HTML pages render the booking sidebar price line (e.g. `₹4,500 /night`), THEN the `/night` unit text and the star rating badge render correctly in HTML but the surrounding layout uses inline `style="display:none"` on the booking form which is toggled by JS — if JS is slow or blocked, both the guest state and the form are hidden simultaneously, showing a blank booking sidebar.

### Expected Behavior (Correct)

2.1 WHEN a user with role `admin` logs in via `login.php`, THEN the system SHALL store the token under both `csn_token`/`csn_user` AND `csn_admin_token`/`csn_admin_user` so that both the public site header and the admin panel auth guard recognise the session.

2.2 WHEN a user logs in via `adminexplorer.php`, THEN the system SHALL also store the token under `csn_token`/`csn_user` so the public site header reflects the logged-in state.

2.3 WHEN a logged-in user visits `login.php` or `register.php` and the JWT is valid, THEN the system SHALL redirect them away immediately without showing the login/register form.

2.4 WHEN the Material Symbols font is unavailable, THEN the system SHALL display meaningful fallback text or hide icon-only elements gracefully so that no raw ligature strings (e.g. `star`, `lock`, `call`) are visible to the user.

2.5 WHEN a user logs in and is redirected back to a listing-detail page via `?redirect=`, THEN the system SHALL correctly hide the guest state block and show the booking form, because the auth-state JS runs after `localStorage` has been populated.

2.6 WHEN a logged-in user's token has expired and they attempt to submit the booking form, THEN the system SHALL show a clear "session expired, please log in again" message and redirect to `login.php?redirect=<current-url>` rather than showing the generic login-required modal.

2.7 WHEN an admin edits a listing in `admin/listings.php`, THEN the system SHALL correctly populate all form fields because the listings API SHALL return a single object (not an array) when queried with both `category` and `id` parameters.

2.8 WHEN `admin/bookings.php` renders the loading state, THEN the system SHALL use `colspan="9"` to match the actual 9-column table header.

2.9 WHEN `admin/content.php` loads the homepage section, THEN the system SHALL correctly parse the JSON content from the API response and populate all homepage setting fields.

2.10 WHEN a user submits the contact form, THEN the system SHALL display a success or error message based on the API response.

2.11 WHEN the listing-detail booking sidebar loads, THEN the system SHALL default to showing the guest state block (not a blank sidebar) so that even without JS the user sees a call-to-action, and SHALL transition to the booking form only after confirming a valid auth token.

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a regular (non-admin) user logs in via `login.php`, THEN the system SHALL CONTINUE TO store the token under `csn_token`/`csn_user` and redirect to `index.php` or the `?redirect=` URL.

3.2 WHEN a non-logged-in user visits any listing-detail page, THEN the system SHALL CONTINUE TO show the guest state block with Login and Register buttons linking to the correct redirect URLs.

3.3 WHEN a logged-in user submits the booking form on a listing-detail page, THEN the system SHALL CONTINUE TO POST to `../php/api/bookings.php` and show the success message on a 201 response.

3.4 WHEN an admin logs out via the admin panel sidebar, THEN the system SHALL CONTINUE TO clear `csn_admin_token`/`csn_admin_user` and redirect to `adminexplorer.php`.

3.5 WHEN an admin logs out via the public site header, THEN the system SHALL CONTINUE TO clear `csn_token`/`csn_user` and redirect to `index.php`.

3.6 WHEN the listing pages (`listing.php`) render cards with price, rating, and icons, THEN the system SHALL CONTINUE TO display them correctly using Material Symbols and Tailwind CSS classes.

3.7 WHEN the admin panel loads any page, THEN the system SHALL CONTINUE TO verify the admin token client-side and redirect to `adminexplorer.php` if the token is missing, invalid, or belongs to a non-admin user.

3.8 WHEN the admin bookings, listings, users, blogs, gallery, and content pages make API calls, THEN the system SHALL CONTINUE TO send the `Authorization: Bearer <token>` header via the shared `api()` helper in `admin-footer.php`.

3.9 WHEN the `.htaccess` rewrite rules are in effect, THEN the system SHALL CONTINUE TO block direct access to `php/` (non-API) files and allow access to `php/api/` endpoints.

3.10 WHEN the SQLite database schema is initialised, THEN the system SHALL CONTINUE TO seed the admin user (`travelhubadmin@gmail.com`) if it does not already exist.
