# CSNExplore Full Migration — Implementation Tasks

## Tasks

- [x] 1. Fix login.php — replace form POST with JS fetch to php/api/auth.php?action=login
- [x] 2. Fix register.php — replace form POST with JS fetch to php/api/auth.php?action=register
- [x] 3. Create .htaccess — Apache URL rewriting, security headers, gzip, browser caching
- [x] 4. Create robots.txt
- [x] 5. Create sitemap.xml (static initial version)
- [x] 6. Create privacy.php
- [x] 7. Create terms.php
- [x] 8. Create subscribe.php — newsletter subscription handler
- [x] 9. Create install.php — one-click setup script
- [x] 10. Create php/api/generate_sitemap.php — dynamic sitemap gener
ator
- [x] 11. Create cache/ directory with .gitkeep and .htaccess protection
- [x] 12. Create logs/ directory with .gitkeep and .htaccess protection
- [x] 13. Update php/config.php — point error_log to logs/php_errors.log

## UI/UX & Performance Improvements

- [x] 14. header.php — reduce logo size (h-14 → h-9), remove top marquee bar call/WhatsApp links
- [x] 15. footer.php — add more Quick Links (Restaurants, About Us, Contact, Privacy, Terms, Bus Tickets)
- [x] 16. sitemap.xml — regenerate with all 74 listing detail pages + 300 blog pages + all static pages
- [x] 17. listing-detail pages — add hero image above rating block, remove top "Need help?" call+WhatsApp from booking card, add image zoom/lightbox (gallery if images exist, single zoomable if not), full responsive polish
  - [x] 17.1 Create shared listing-detail upgrade script (Python) to batch-process all 74 HTML files
  - [x] 17.2 Apply upgrades to all 15 attractions pages
  - [x] 17.3 Apply upgrades to all 12 bikes pages
  - [x] 17.4 Apply upgrades to all 12 buses pages
  - [x] 17.5 Apply upgrades to all 10 cars pages
  - [x] 17.6 Apply upgrades to all 15 restaurants pages
  - [x] 17.7 Apply upgrades to all 10 stays pages
- [x] 18. index.php — remove top marquee bar call/WhatsApp links (keep marquee text only), full mobile responsive audit
- [x] 19. listing.php — full mobile/tablet responsive audit and polish
- [x] 20. blogs.php — full mobile/tablet responsive audit and polish
- [x] 21. about.php, contact.php — full mobile/tablet responsive audit and polish
