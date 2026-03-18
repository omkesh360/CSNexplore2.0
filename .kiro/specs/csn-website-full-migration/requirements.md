# Requirements Document

## Introduction

CSNExplore is a travel platform for Chhatrapati Sambhajinagar (Aurangabad), Maharashtra. The new website
(PHP 8 + SQLite + Tailwind CSS) already has a structural foundation — homepage, listing pages, 74 static
detail pages, 300 static blog pages, admin panel skeleton, and a REST API layer. This migration spec covers
every feature from the old codebase that must be fully implemented, wired up, and deployment-ready in the
new site. The goal is a single, self-contained PHP application deployable on shared hosting or a VPS with
Apache, with no Composer dependencies.

---

## Glossary

- **System**: The CSNExplore PHP web application as a whole.
- **Admin**: An authenticated user with role = 'admin' in the users table.
- **Visitor**: An unauthenticated browser user browsing the public site.
- **User**: A registered, authenticated browser user with role = 'user'.
- **Listing**: A single record in any of the six category tables: stays, cars, bikes, restaurants, attractions, buses.
- **Booking**: A booking-call request submitted by a Visitor or User for a Listing.
- **API**: The PHP scripts under php/api/ that return JSON responses.
- **Admin Panel**: The password-protected section under admin/ accessible only to Admins.
- **JWT**: JSON Web Token used for stateless authentication, stored in localStorage.
- **DB**: The SQLite database at database/csnexplore.db accessed via PDO.
- **RateLimiter**: A file-based IP rate-limiting utility that rejects excess requests.
- **Cache**: A file-based JSON cache stored under cache/ with configurable TTL.
- **Sitemap**: The sitemap.xml file at the website root, auto-generated from DB records.
- **Vendor**: An optional business entity that owns one or more Listings.
- **Slug**: A URL-safe, hyphenated string derived from a Listing or Blog title.

---

## Requirements

### Requirement 1: Database Schema — Complete & Deployment-Ready

**User Story:** As a developer deploying the site, I want the DB schema to be fully initialised on first
run, so that no manual SQL steps are needed.

#### Acceptance Criteria

1. THE System SHALL auto-create all required tables on first request via Database::initSchema() if they do not already exist.
2. THE System SHALL create the following tables: users, stays, cars, bikes, restaurants, attractions, buses, bookings, blogs, about_contact, contact_messages, vendors, newsletter_subscribers.
3. THE System SHALL add the columns guest_reviews (TEXT), rooms (TEXT), breakfast_info (TEXT), and top_location_rating (TEXT) to the stays table if they are absent.
4. THE System SHALL add the column guest_reviews (TEXT) to the cars, bikes, restaurants, attractions, and buses tables if absent.
5. THE System SHALL add the column vendor_id (INTEGER) to stays, cars, bikes, restaurants, and buses tables if absent, with a foreign key reference to vendors(id).
6. THE System SHALL seed one default Admin user (email from ADMIN_EMAIL constant, password 'admin123') if no admin user exists.
7. WHEN the DB file does not exist, THE System SHALL create the database directory and file automatically before executing any query.

---

### Requirement 2: JWT Authentication System

**User Story:** As a Visitor, I want to register and log in, so that I can make bookings and access
personalised features.

#### Acceptance Criteria

1. WHEN a POST request is sent to php/api/auth.php?action=register with valid name, email, phone, and password (minimum 6 characters), THE System SHALL create a new User record and return a signed JWT plus user object.
2. WHEN a POST request is sent to php/api/auth.php?action=login with a valid email and password, THE System SHALL return a signed JWT and user object.
3. WHEN a POST request is sent to php/api/auth.php?action=login with an invalid email or wrong password, THE System SHALL return HTTP 401 with an error message.
4. WHEN a GET request is sent to php/api/auth.php?action=verify with a valid Bearer token, THE System SHALL return { valid: true, user: payload }.
5. WHEN a GET request is sent to php/api/auth.php?action=verify with an expired or tampered token, THE System SHALL return HTTP 401.
6. THE System SHALL store the JWT in the browser's localStorage under the key 'csn_admin_token' for Admin sessions and 'csn_token' for User sessions.
7. THE register.php page SHALL present a form with fields: full name, email, phone, password, confirm password, and submit the data to the register API endpoint.
8. WHEN a registration form is submitted with a password that does not match confirm password, THE register.php page SHALL display an inline validation error without submitting to the API.
9. THE login.php page SHALL present a form with email and password fields and redirect to the homepage on successful login.
10. THE adminexplorer.php page SHALL present a separate admin login form and redirect to admin/dashboard.php on successful admin login.

---

### Requirement 3: Public Booking Modal

**User Story:** As a Visitor, I want to book a service directly from a listing detail page, so that the
business can call me back to confirm.

#### Acceptance Criteria

1. WHEN a Visitor clicks "Book Now" on any listing detail page, THE System SHALL display a modal overlay with a booking form.
2. THE Booking_Modal SHALL contain fields: full name (required), phone (required), email (optional), preferred date, number of guests/people, and notes.
3. THE Booking_Modal SHALL pre-fill the listing name and service type from the page context.
4. WHEN the booking form is submitted with valid name and phone, THE System SHALL POST to php/api/bookings.php and display a success confirmation message.
5. WHEN the booking form is submitted with an empty name or phone, THE Booking_Modal SHALL display inline validation errors and SHALL NOT submit to the API.
6. WHEN the booking API returns an error, THE Booking_Modal SHALL display the error message to the Visitor.
7. THE Booking_Modal SHALL be closable by clicking outside the modal or pressing the Escape key.
8. THE System SHALL inject the booking modal JavaScript and HTML into all 74 static listing detail pages via a shared include or inline script block.

---

### Requirement 4: Listing Detail Pages — Dynamic Content & Gallery

**User Story:** As a Visitor, I want to see full details, a photo gallery, and a booking option on every
listing page, so that I can make an informed decision.

#### Acceptance Criteria

1. THE System SHALL serve a dynamic listing detail page at listing-detail/{category}-{id}-{slug}.html that loads data from the DB via the listings API.
2. WHEN a Visitor opens a stays detail page, THE System SHALL display: name, location, description, price per night, rating, amenities list, room types, gallery, guest reviews, and a Book Now button.
3. WHEN a Visitor opens a cars detail page, THE System SHALL display: name, type, fuel type, transmission, seats, features list, price per day, gallery, guest reviews, and a Book Now button.
4. WHEN a Visitor opens a bikes detail page, THE System SHALL display: name, type, fuel type, CC, features list, price per day, gallery, guest reviews, and a Book Now button.
5. WHEN a Visitor opens a restaurants detail page, THE System SHALL display: name, cuisine, location, price per person, menu highlights, gallery, guest reviews, and a Book Now button.
6. WHEN a Visitor opens an attractions detail page, THE System SHALL display: name, type, location, entry fee, opening hours, best time to visit, gallery, guest reviews, and a Book Now button.
7. WHEN a Visitor opens a buses detail page, THE System SHALL display: operator, bus type, route (from → to), departure time, arrival time, duration, price, amenities, seats available, gallery, and a Book Now button.
8. WHEN a listing has a gallery array with one or more image URLs, THE System SHALL render a lightbox gallery that opens full-screen on image click.
9. WHEN a listing has no gallery images, THE System SHALL display the single listing image as a full-width hero without a lightbox trigger.
10. THE System SHALL include correct Open Graph and Twitter Card meta tags on every listing detail page using the listing's name, description, and image.

---

### Requirement 5: Homepage — Dynamic Sections & Hero

**User Story:** As a Visitor, I want to see a curated homepage with featured listings and blog posts, so
that I can quickly discover what CSNExplore offers.

#### Acceptance Criteria

1. THE System SHALL render the homepage hero section with a configurable headline, subtext, and background image loaded from the about_contact table (section = 'homepage').
2. THE System SHALL render a marquee/ticker bar below the hero with configurable promotional text items stored in the homepage settings.
3. THE System SHALL render a stats bar with four configurable stat labels (e.g. "500+ Hotels") loaded from homepage settings.
4. THE System SHALL render up to five content sections (attractions, bikes, restaurants, buses, blogs) in the order defined by the section_order setting.
5. WHEN a section's show_{key} setting is false, THE System SHALL omit that section from the rendered homepage.
6. WHEN a section has specific picks_{key} IDs configured, THE System SHALL display only those Listings in the configured order.
7. WHEN a section has no specific picks configured, THE System SHALL display the top N active Listings ordered by display_order ASC, rating DESC, where N equals the count_{key} setting.
8. THE System SHALL support four layout modes per section — 3-col, 4-col, 2-col, and scroll — and apply the corresponding Tailwind CSS grid or flex class.
9. THE System SHALL display a city introduction paragraph below the stats bar when the city_intro setting is non-empty.
10. WHEN a Visitor submits the search form on the homepage, THE System SHALL redirect to listing.php with the appropriate category and search query parameters.

---

### Requirement 6: Admin Dashboard

**User Story:** As an Admin, I want a dashboard overview of key metrics, so that I can monitor the
platform at a glance.

#### Acceptance Criteria

1. WHEN an Admin loads admin/dashboard.php, THE System SHALL fetch stats from php/api/dashboard.php and display: total bookings, pending bookings count, total registered users, and published blog count.
2. THE Dashboard SHALL display active listing counts for all six categories: stays, cars, bikes, restaurants, attractions, buses.
3. THE Dashboard SHALL display a "Recent Bookings" table showing the 10 most recent bookings with columns: name, phone, service, date, and status.
4. WHEN the pending bookings count is greater than zero, THE Admin_Panel SHALL display a notification badge in the top bar showing the pending count.
5. THE php/api/dashboard.php endpoint SHALL require a valid Admin JWT and return HTTP 403 for non-admin tokens.

---

### Requirement 7: Admin Bookings Management

**User Story:** As an Admin, I want to view, filter, and update all booking requests, so that I can
follow up with customers efficiently.

#### Acceptance Criteria

1. THE Admin_Panel SHALL display all bookings in a table with columns: ID, customer name, phone, service, date, people count, status, and actions.
2. WHEN an Admin selects a status filter tab (All / Pending / Completed / Cancelled), THE System SHALL reload the bookings table showing only bookings matching that status.
3. WHEN an Admin types in the search field, THE System SHALL filter bookings by customer name, phone, or email with a 400 ms debounce.
4. WHEN an Admin clicks a booking row, THE System SHALL open a detail modal showing all booking fields and action buttons: Mark Completed, Cancel, and Reset to Pending.
5. WHEN an Admin clicks "Mark Completed" or "Cancel", THE System SHALL send a PUT request to the bookings API and refresh the table.
6. THE Admin_Panel SHALL display a clickable phone number link (tel:) and a WhatsApp deep-link button for each booking.
7. WHEN an Admin clicks the delete button on a booking row, THE System SHALL prompt for confirmation and then send a DELETE request to remove the booking permanently.

---

### Requirement 8: Admin Listings Management

**User Story:** As an Admin, I want to add, edit, reorder, and toggle visibility of listings per
category, so that the public site always shows accurate information.

#### Acceptance Criteria

1. THE Admin_Panel SHALL display listings in a table per category, switchable via category tabs (Stays, Cars, Bikes, Restaurants, Attractions, Buses).
2. WHEN an Admin clicks "Add Listing", THE System SHALL open a modal form with fields appropriate to the selected category.
3. WHEN an Admin submits the add listing form with all required fields, THE System SHALL POST to the listings API and refresh the table.
4. WHEN an Admin clicks the edit button on a listing row, THE System SHALL open the same modal pre-filled with the listing's current data.
5. WHEN an Admin submits the edit form, THE System SHALL PUT to the listings API and refresh the table.
6. WHEN an Admin clicks the visibility toggle on a listing, THE System SHALL toggle the is_active flag via the listings API without a page reload.
7. WHEN an Admin clicks the delete button on a listing, THE System SHALL prompt for confirmation and then soft-delete the listing (set is_active = 0).
8. THE Admin_Panel SHALL provide a drag-and-drop interface to reorder listings within a category, persisting the new display_order via the listings API reorder action.
9. THE listings API SHALL accept an image URL or a gallery picker selection from the Images Manager.

---

### Requirement 9: Admin Blogs Manager

**User Story:** As an Admin, I want to create, edit, and publish blog posts with rich text, SEO settings,
and featured images, so that the site's content stays fresh.

#### Acceptance Criteria

1. THE Admin_Panel SHALL provide a blog editor page (admin/blogs.php) with a Quill.js rich text editor for the post body.
2. THE Blog_Editor SHALL include fields: title, author, category, tags (comma-separated), featured image URL, meta description, read time, and status (Published / Draft).
3. WHEN an Admin clicks "Publish" or "Save Draft", THE System SHALL POST or PUT to php/api/blogs.php and display a success notification.
4. THE Admin_Panel SHALL list all blog posts in a table with columns: title, category, status, date, and actions (edit, delete).
5. WHEN an Admin clicks "Delete" on a blog post, THE System SHALL prompt for confirmation and then DELETE the post via the API.
6. THE Blog_Editor SHALL allow the Admin to pick a featured image from the Images Manager gallery picker.
7. THE php/api/blogs.php endpoint SHALL require a valid Admin JWT for POST, PUT, and DELETE operations.
8. WHEN a blog post is published, THE System SHALL make it accessible at blogs/{slug}.html or via blog-detail.php?id={id}.

---

### Requirement 10: Admin Homepage Editor

**User Story:** As an Admin, I want to edit all homepage content from the admin panel, so that I can
update promotions and featured items without touching code.

#### Acceptance Criteria

1. THE Admin_Panel content page SHALL provide inputs for: hero headline, hero subtext, city intro paragraph, marquee bar items (one per line), and four stats bar labels.
2. THE Admin_Panel content page SHALL list all five homepage sections (attractions, bikes, restaurants, buses, blogs) as draggable rows that can be reordered.
3. WHEN an Admin drags a section row to a new position, THE System SHALL update the visual order immediately and persist it on save.
4. EACH section row SHALL have a visibility toggle, a title input, a card count selector, and a layout selector (3-col / 4-col / 2-col / scroll / list).
5. EACH section row SHALL have a "Pick specific items" panel that loads Listing thumbnails and names, allowing the Admin to check specific items to feature.
6. WHEN an Admin clicks "Save Homepage Settings", THE System SHALL PUT the full settings object to php/api/about_contact.php?section=homepage and display a success or error notification.
7. WHEN the homepage settings are saved, THE index.php page SHALL reflect the new settings on the next page load without any cache clearing step.

---

### Requirement 11: Admin About & Contact Editor

**User Story:** As an Admin, I want to edit the About Us and Contact pages from the admin panel, so that
business information stays current.

#### Acceptance Criteria

1. THE Admin_Panel content page SHALL provide a tab for "About Us" with textarea fields: mission statement, vision, and about description.
2. THE Admin_Panel content page SHALL provide a tab for "Contact Info" with inputs: phone, email, WhatsApp number, business hours, and address.
3. WHEN an Admin saves About Us content, THE System SHALL PUT the data to php/api/about_contact.php with section = 'about'.
4. WHEN an Admin saves Contact Info, THE System SHALL PUT the data to php/api/about_contact.php with section = 'contact'.
5. THE about.php public page SHALL load its content from the about_contact table (section = 'about') and render it dynamically.
6. THE contact.php public page SHALL load its content from the about_contact table (section = 'contact') and render it dynamically.

---

### Requirement 12: Contact Form & Messages

**User Story:** As a Visitor, I want to send a message via the contact page, so that I can reach the
CSNExplore team.

#### Acceptance Criteria

1. THE contact.php page SHALL display a form with fields: first name, last name, email, interest/subject, and message.
2. WHEN a Visitor submits the contact form with all required fields, THE System SHALL POST to php/api/contact_messages.php and display a success confirmation.
3. WHEN a Visitor submits the contact form with an invalid email address, THE System SHALL display an inline validation error and SHALL NOT submit to the API.
4. THE php/api/contact_messages.php endpoint SHALL save the message to the contact_messages table.
5. THE Admin_Panel content page SHALL display a "Messages" tab listing all contact form submissions with sender name, email, subject, message preview, and date.
6. WHEN an Admin views the Messages tab, THE System SHALL fetch messages from php/api/contact_messages.php (admin-only GET) and render them in a list.

---

### Requirement 13: Admin Images Manager (Gallery)

**User Story:** As an Admin, I want to upload, browse, copy URLs, and delete images, so that I can manage
all media assets from one place.

#### Acceptance Criteria

1. THE Admin_Panel gallery page (admin/gallery.php) SHALL display all images from the images/ and images/uploads/ directories in a responsive grid.
2. WHEN an Admin selects an image file and clicks "Upload", THE System SHALL POST the file to php/api/upload.php and refresh the gallery grid.
3. THE upload API SHALL accept only JPEG, PNG, GIF, WebP, and SVG files and SHALL reject other file types with HTTP 400.
4. THE upload API SHALL reject files larger than 5 MB with HTTP 400.
5. WHEN an Admin clicks "Copy URL" on an image tile, THE System SHALL copy the image URL to the clipboard and display a brief confirmation tooltip.
6. WHEN an Admin clicks "Delete" on an image tile, THE System SHALL prompt for confirmation and then DELETE the file via php/api/upload.php?path={url}.
7. THE Images Manager SHALL expose a gallery picker function (openGalleryPicker) callable from the listing add/edit modal and the blog editor to select an image URL.

---

### Requirement 14: Admin Users Management

**User Story:** As an Admin, I want to view all registered users, change their roles, and delete
accounts, so that I can manage platform access.

#### Acceptance Criteria

1. THE Admin_Panel users page (admin/users.php) SHALL display all users in a table with columns: ID, name, email, phone, role, verified status, and registration date.
2. WHEN an Admin changes a user's role via a dropdown, THE System SHALL PUT the new role to php/api/users.php and display a success notification.
3. WHEN an Admin clicks "Delete" on a user row, THE System SHALL prompt for confirmation and then DELETE the user via the API.
4. IF the user to be deleted is the last Admin in the system, THEN THE System SHALL reject the deletion with an error message "Cannot delete the last admin account".
5. THE php/api/users.php endpoint SHALL require a valid Admin JWT for all write operations.

---

### Requirement 15: Rate Limiting

**User Story:** As a site operator, I want API endpoints protected from abuse, so that the server
remains stable under high traffic or bot attacks.

#### Acceptance Criteria

1. THE RateLimiter SHALL track request counts per client IP address using a file stored in the system temp directory.
2. WHEN a client IP exceeds 100 requests within a 15-minute window on any booking or auth API endpoint, THE System SHALL return HTTP 429 with the message "Too many requests, please try again later."
3. THE RateLimiter SHALL automatically expire and remove entries older than the configured window on each check.
4. THE php/api/auth.php login and register actions SHALL apply rate limiting on every request.
5. THE php/api/bookings.php POST action SHALL apply rate limiting on every request.

---

### Requirement 16: Input Validation & Sanitisation

**User Story:** As a site operator, I want all user inputs validated and sanitised server-side, so that
the application is protected from injection and XSS attacks.

#### Acceptance Criteria

1. THE System SHALL strip HTML tags and encode special characters on all string inputs received by any API endpoint using the sanitize() helper.
2. WHEN an API endpoint receives an email field, THE System SHALL validate it with filter_var(FILTER_VALIDATE_EMAIL) and return HTTP 400 if invalid.
3. WHEN an API endpoint receives a numeric field (price, rating, seats, etc.), THE System SHALL cast it to the appropriate numeric type and reject non-numeric values with HTTP 400.
4. WHEN an API endpoint receives a status field for bookings, THE System SHALL validate it against the allowed set ('pending', 'completed', 'cancelled') and return HTTP 400 for any other value.
5. THE System SHALL use PDO prepared statements for all DB queries and SHALL NOT construct SQL strings by concatenating unsanitised user input.

---

### Requirement 17: File-Based Cache System

**User Story:** As a site operator, I want frequently-read API responses cached, so that DB load is
reduced on high-traffic pages.

#### Acceptance Criteria

1. THE Cache SHALL store JSON-encoded values in files under a cache/ directory at the project root, named by MD5 hash of the cache key.
2. WHEN a cached value is requested and the file exists and has not expired, THE Cache SHALL return the stored value without querying the DB.
3. WHEN a cached value is requested and the file has expired or does not exist, THE Cache SHALL return null so the caller can fetch fresh data and re-cache it.
4. THE Cache SHALL support a configurable TTL per entry, defaulting to 3600 seconds.
5. WHEN an Admin saves listing, blog, or homepage data via any admin API, THE System SHALL invalidate (delete) the relevant cache entries.
6. THE php/api/dashboard.php endpoint SHALL cache its stats response for 60 seconds.

---

### Requirement 18: Sitemap & robots.txt

**User Story:** As a site operator, I want an auto-generated sitemap.xml and a robots.txt, so that
search engines can crawl the site efficiently.

#### Acceptance Criteria

1. THE System SHALL provide a php/generate-sitemap.php script that generates sitemap.xml at the project root.
2. THE Sitemap_Generator SHALL include all static pages (index.php, about.php, contact.php, listing.php per category, blogs.php, bus.php, privacy.php, terms.php) with appropriate priority and changefreq values.
3. THE Sitemap_Generator SHALL include all active Listing detail pages from all six categories, using the URL pattern /listing-detail/{category}-{id}-{slug}.html.
4. THE Sitemap_Generator SHALL include all published blog pages from the blogs/ directory, using the URL pattern /blogs/{slug}.html.
5. THE Sitemap_Generator SHALL write the lastmod date for each URL using the record's updated_at timestamp.
6. THE System SHALL provide a robots.txt file at the project root that allows all crawlers, disallows /admin/, and references the sitemap URL.
7. WHEN an Admin triggers sitemap regeneration from the admin panel, THE System SHALL call the sitemap generator and display the count of URLs written.

---

### Requirement 19: SEO Meta Tags & Canonical URLs

**User Story:** As a site operator, I want every public page to have correct meta tags and canonical
URLs, so that search engine rankings are maximised.

#### Acceptance Criteria

1. THE System SHALL include a <title> tag, meta description, Open Graph (og:title, og:description, og:image, og:url), and Twitter Card tags on every public PHP page.
2. THE System SHALL include a <link rel="canonical"> tag on every public PHP page pointing to the page's canonical URL.
3. WHEN a listing detail page is rendered, THE System SHALL use the listing's name as og:title, the listing's description (truncated to 160 characters) as og:description, and the listing's image as og:image.
4. WHEN a blog detail page is rendered, THE System SHALL use the blog's title as og:title, the blog's meta_description as og:description, and the blog's image as og:image.
5. THE header.php shared include SHALL accept $page_title, $meta_description, $og_image, and $canonical_url variables and render the appropriate tags.

---

### Requirement 20: .htaccess — URL Rewriting, Security & Caching

**User Story:** As a site operator, I want an .htaccess file that handles clean URLs, security headers,
and browser caching, so that the site is fast and secure on Apache.

#### Acceptance Criteria

1. THE System SHALL provide an .htaccess file at the project root with mod_rewrite rules that enable clean URLs.
2. THE .htaccess SHALL add security headers: X-Content-Type-Options: nosniff, X-Frame-Options: SAMEORIGIN, X-XSS-Protection: 1; mode=block, and Referrer-Policy: strict-origin-when-cross-origin.
3. THE .htaccess SHALL set browser cache expiry for static assets: images (30 days), CSS/JS (7 days), fonts (365 days).
4. THE .htaccess SHALL block direct access to the database/ directory, cache/ directory, and any .json files under data/.
5. THE .htaccess SHALL redirect HTTP requests to HTTPS when the site is accessed over a non-secure connection.
6. THE .htaccess SHALL block access to files matching *.sql, *.log, *.env, and composer.json.

---

### Requirement 21: Newsletter Subscription

**User Story:** As a Visitor, I want to subscribe to the CSNExplore newsletter, so that I receive travel
updates about Chhatrapati Sambhajinagar.

#### Acceptance Criteria

1. THE System SHALL provide a subscribe.php API endpoint that accepts a POST request with an email field.
2. WHEN a valid email is submitted to subscribe.php, THE System SHALL insert the email into the newsletter_subscribers table with a subscribed_at timestamp and return HTTP 201.
3. WHEN an already-subscribed email is submitted, THE System SHALL return HTTP 200 with a message "Already subscribed" without creating a duplicate record.
4. WHEN an invalid email is submitted, THE System SHALL return HTTP 400 with a validation error.
5. THE homepage footer SHALL include a newsletter subscription input and button that submits to subscribe.php via fetch and displays a success or error message inline.

---

### Requirement 22: Privacy Policy & Terms of Service Pages

**User Story:** As a Visitor, I want to read the privacy policy and terms of service, so that I
understand how my data is used.

#### Acceptance Criteria

1. THE System SHALL provide a privacy.php page accessible at /privacy.php with static privacy policy content relevant to a travel booking platform.
2. THE System SHALL provide a terms.php page accessible at /terms.php with static terms of service content.
3. THE privacy.php and terms.php pages SHALL use the shared header.php and footer.php includes.
4. THE footer.php shared include SHALL contain links to /privacy.php and /terms.php.

---

### Requirement 23: Vendor Management

**User Story:** As an Admin, I want to manage vendor records linked to listings, so that I can track
which business owns each listing.

#### Acceptance Criteria

1. THE DB SHALL contain a vendors table with columns: id, name, email, phone, address, description, rating, total_bookings, created_at, updated_at.
2. THE System SHALL provide a php/api/vendors.php endpoint supporting GET (list all), POST (create), PUT (update by id), and DELETE (delete by id) — all requiring Admin JWT.
3. WHEN a Listing is created or updated with a vendor_id, THE System SHALL validate that the vendor_id exists in the vendors table before saving.
4. THE Admin_Panel listings add/edit modal SHALL include an optional vendor selector dropdown populated from the vendors API.

---

### Requirement 24: Listing Search & Filtering on listing.php

**User Story:** As a Visitor, I want to search and filter listings by category, keyword, and type, so
that I can find exactly what I need.

#### Acceptance Criteria

1. THE listing.php page SHALL accept query parameters: category (required), search (optional keyword), and type (optional sub-type filter).
2. WHEN listing.php is loaded with a category parameter, THE System SHALL fetch active listings for that category from the listings API and render them as cards.
3. WHEN a Visitor types in the search box on listing.php, THE System SHALL re-fetch listings with the search parameter and update the card grid without a full page reload.
4. WHEN no listings match the search query, THE listing.php page SHALL display a "No results found" message with a suggestion to clear the search.
5. EACH listing card on listing.php SHALL link to the corresponding listing detail page at /listing-detail/{category}-{id}-{slug}.html.

---

### Requirement 25: Static Blog Pages & Blog Listing

**User Story:** As a Visitor, I want to browse and read blog posts about Chhatrapati Sambhajinagar, so
that I can plan my trip with local knowledge.

#### Acceptance Criteria

1. THE blogs.php page SHALL fetch published blog posts from php/api/blogs.php and render them as cards with title, category, read time, featured image, and excerpt.
2. THE blog-detail.php page SHALL accept an id query parameter, fetch the blog from the API, and render the full post with title, author, date, category, tags, featured image, and HTML content.
3. WHEN a blog post has a meta_description, THE blog-detail.php page SHALL use it as the page's meta description and og:description.
4. THE System SHALL provide a php/api/seed_blogs.php script that populates the blogs table with sample blog data for development and testing.
5. THE blogs.php page SHALL support pagination or a "Load More" button when more than 12 published posts exist.

---

### Requirement 26: Admin Panel — Security & Access Control

**User Story:** As a site operator, I want the admin panel protected so that only authenticated Admins
can access it.

#### Acceptance Criteria

1. WHEN a browser navigates to any page under admin/ without a valid Admin JWT in localStorage, THE Admin_Panel SHALL immediately redirect to adminexplorer.php.
2. THE adminexplorer.php page SHALL present a login form and SHALL NOT expose any admin data before authentication.
3. WHEN an Admin JWT expires (default 24-hour TTL), THE Admin_Panel SHALL redirect to adminexplorer.php on the next API call that returns HTTP 401.
4. THE php/jwt.php SHALL provide requireAdmin() and requireUser() helper functions that verify the Bearer token from the Authorization header and call sendError(403) if the role check fails.
5. THE Admin_Panel SHALL display the logged-in Admin's name and email in the sidebar footer, loaded from localStorage.

---

### Requirement 27: Seed Data Script

**User Story:** As a developer setting up the site for the first time, I want a seed script that
populates the DB with realistic sample data, so that the site looks complete immediately after deployment.

#### Acceptance Criteria

1. THE System SHALL provide a php/api/seed.php script that inserts sample records into stays, cars, bikes, restaurants, attractions, and buses tables.
2. THE seed.php script SHALL be idempotent: WHEN run multiple times, THE System SHALL NOT create duplicate records if records with the same name already exist.
3. THE seed.php script SHALL insert at least 5 records per category with realistic names, locations, prices, ratings, and images relevant to Chhatrapati Sambhajinagar.
4. THE seed.php script SHALL require Admin authentication or a secret query parameter to prevent public execution.
5. THE System SHALL provide a php/api/seed_blogs.php script that inserts at least 10 sample blog posts with titles, content, categories, and tags relevant to Chhatrapati Sambhajinagar travel.

---

### Requirement 28: Performance — Page Load & API Response

**User Story:** As a Visitor, I want pages to load quickly, so that I have a smooth browsing experience.

#### Acceptance Criteria

1. THE System SHALL enable PHP output buffering and gzip compression via .htaccess for all PHP responses.
2. THE php/api/listings.php GET endpoint SHALL respond within 500 ms for a category with up to 100 active records on a standard shared hosting environment.
3. THE System SHALL add DB indexes on the is_active column for all six listing tables and on bookings.status and bookings.created_at.
4. THE System SHALL lazy-load images on listing cards and detail pages using the loading="lazy" attribute.
5. THE System SHALL minify inline CSS and JS in production by avoiding unnecessary whitespace in critical render-path code.

---

### Requirement 29: Error Handling & Logging

**User Story:** As a site operator, I want errors logged server-side and friendly messages shown to
Visitors, so that issues are diagnosable without exposing internals.

#### Acceptance Criteria

1. THE php/config.php SHALL set display_errors = 0 and log_errors = 1 so that PHP errors are written to the server log and not shown to Visitors.
2. WHEN any API endpoint catches an exception, THE System SHALL log the full exception message via error_log() and return a generic "Server error" JSON response with HTTP 500.
3. WHEN a requested Listing or Blog is not found, THE System SHALL return HTTP 404 with a JSON error message.
4. THE System SHALL provide a custom 404 error page (404.php) that uses the shared header.php and footer.php and suggests navigation links.
5. THE .htaccess SHALL configure ErrorDocument 404 to point to /404.php.
